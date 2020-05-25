<?php

namespace Biz\Question\Service\Impl;

use Biz\BaseService;
use AppBundle\Common\ArrayToolkit;
use Biz\Question\Dao\QuestionDao;
use Biz\Question\Dao\QuestionFavoriteDao;
use Biz\Question\QuestionException;
use Biz\QuestionBank\Service\QuestionBankService;
use Codeages\Biz\Framework\Event\Event;
use Biz\Question\Service\QuestionService;

class QuestionServiceImpl extends BaseService implements QuestionService
{
    protected $defaultQuestionTypeSeq = array('single_choice', 'choice', 'essay', 'uncertain_choice', 'determine', 'fill', 'material');

    public function get($id)
    {
        return $this->getQuestionDao()->get($id);
    }

    public function create($fields)
    {
        $argument = $fields;
        $user = $this->getCurrentuser();
        $fields['createdUserId'] = $user['id'];
        $fields['updatedUserId'] = $user['id'];

        if (isset($fields['content'])) {
            $fields['content'] = $this->purifyHtml($fields['content'], true);
        }

        $this->beginTransaction();
        try {
            $questionConfig = $this->getQuestionConfig($fields['type']);
            $media = $questionConfig->create($fields);

            if (!empty($media)) {
                $fields['metas']['mediaId'] = $media['id'];
            }

            $fields['createdTime'] = time();
            $fields['updatedTime'] = time();
            $fields = $questionConfig->filter($fields);

            if (!empty($fields['parentId'])) {
                $parentQuestion = $this->get($fields['parentId']);
                empty($parentQuestion) || $fields['categoryId'] = $parentQuestion['categoryId'];
            }

            $question = $this->getQuestionDao()->create($fields);

            if ($question['parentId'] > 0) {
                $this->waveCount($question['parentId'], array('subCount' => '1'));
            }

            if (!empty($question['bankId']) && 0 == $question['parentId']) {
                $this->getQuestionBankService()->waveQuestionNum($question['bankId'], 1);
            }

            $this->dispatchEvent('question.create', new Event($question, array('argument' => $argument)));

            $this->commit();

            return $question;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function importQuestions($questions, $token)
    {
        $savedQuestions = array();
        //按照题型分组
        $groupQuestions = $this->groupQuestions($questions);
        $questionBankId = $token['data']['questionBankId'];
        try {
            $this->beginTransaction();
            foreach ($groupQuestions as $type => $questionsGroup) {
                //分组循环处理题目
                foreach ($questionsGroup as $key => $question) {
                    $question['bankId'] = $questionBankId;
                    $savedQuestions[] = $savedQuestion = $this->importQuestion($question);
                    if ('material' == $question['type']) {
                        $subQuestions = $question['subQuestions'];
                        //材料题子题处理
                        foreach ($subQuestions as $subQuestion) {
                            $subQuestion['parentId'] = $savedQuestion['id'];
                            $subQuestion['bankId'] = $questionBankId;
                            $savedQuestions[] = $this->importQuestion($subQuestion);
                        }
                    }
                }
            }
            $this->commit();

            return $savedQuestions;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    protected function groupQuestions($questions)
    {
        $questions = ArrayToolkit::group($questions, 'type');
        $groupQuestions = array();
        foreach ($this->defaultQuestionTypeSeq as $type) {
            $groupQuestions[$type] = isset($questions[$type]) ? $questions[$type] : array();
        }

        return $groupQuestions;
    }

    protected function importQuestion($question)
    {
        $question = $this->filterImportQuestion($question);
        $savedQuestion = $this->create($question);
        if (in_array($savedQuestion['type'], array('choice', 'uncertain_choice'))) {
            $savedQuestion['missScore'] = empty($question['missScore']) ? 0 : $question['missScore'];
        }

        return $savedQuestion;
    }

    protected function filterImportQuestion($question)
    {
        if (in_array($question['type'], array('choice', 'single_choice', 'uncertain_choice'))) {
            $question['choices'] = $question['options'];
            $question['answer'] = $question['answers'];
            unset($question['options']);
            unset($question['answers']);
        }

        if ('determine' == $question['type']) {
            $result = $question['answer'] ? 1 : 0;
            $question['answer'] = array($result);
        }

        if ('essay' == $question['type']) {
            $question['answer'] = array($question['answer']);
        }

        return $question;
    }

    public function batchCreateQuestions($questions)
    {
        if (empty($questions)) {
            return array();
        }

        return $this->getQuestionDao()->batchCreate($questions);
    }

    public function batchUpdateCategoryId($ids, $categoryId)
    {
        if (empty($ids)) {
            return array();
        }

        $updateFields = array();
        foreach ($ids as $id) {
            $updateFields[] = array('categoryId' => $categoryId);
        }

        return $this->batchUpdateQuestions($ids, $updateFields);
    }

    public function batchUpdateQuestions($ids, $fields)
    {
        if (empty($ids) || empty($fields)) {
            return array();
        }

        return $this->getQuestionDao()->batchUpdate($ids, $fields, 'id');
    }

    public function update($id, $fields)
    {
        $question = $this->get($id);
        $argument = array('question' => $question, 'fields' => $fields);
        if (!$question) {
            $this->createNewException(QuestionException::NOTFOUND_QUESTION());
        }

        $questionConfig = $this->getQuestionConfig($question['type']);
        if (!empty($question['metas']['mediaId'])) {
            $questionConfig->update($question['metas']['mediaId'], $fields);
        }

        $user = $this->getCurrentuser();
        $fields['updatedUserId'] = $user['id'];
        $fields['updatedTime'] = time();

        if (isset($fields['content'])) {
            $fields['content'] = $this->purifyHtml($fields['content'], true);
        }

        $fields = $questionConfig->filter($fields);

        if (!empty($question['parentId'])) {
            $parentQuestion = $this->get($question['parentId']);
            empty($parentQuestion) || $fields['categoryId'] = $parentQuestion['categoryId'];
        }

        $question = $this->getQuestionDao()->update($id, $fields);

        $this->getQuestionDao()->update(array('parentId' => $question['id']), array(
            'categoryId' => $question['categoryId'],
            'updatedUserId' => $question['updatedUserId'],
        ));

        $this->dispatchEvent('question.update', new Event($question, array('argument' => $argument)));

        return $question;
    }

    public function updateCopyQuestionsSubCount($parentId, $subCount)
    {
        return $this->getQuestionDao()->copyQuestionsUpdateSubCount($parentId, $subCount);
    }

    public function delete($id)
    {
        $question = $this->get($id);
        if (!$question) {
            return false;
        }

        if (!empty($question['metas']['mediaId'])) {
            $questionConfig = $this->getQuestionConfig($question['type']);
            $questionConfig->delete($question['metas']['mediaId']);
        }

        $result = $this->getQuestionDao()->delete($id);

        if ($question['parentId'] > 0) {
            $this->waveCount($question['parentId'], array('subCount' => '-1'));
        }

        if ($question['subCount'] > 0) {
            $this->deleteSubQuestions($question['id']);
        }

        if (!empty($question['bankId']) && 0 == $question['parentId']) {
            $this->getQuestionBankService()->waveQuestionNum($question['bankId'], -1);
        }

        $this->dispatchEvent('question.delete', new Event($question));

        return $result;
    }

    public function batchDeletes($ids)
    {
        if (!$ids) {
            return false;
        }

        foreach ($ids ?: array() as $id) {
            $this->delete($id);
        }

        return true;
    }

    public function deleteSubQuestions($parentId)
    {
        return $this->getQuestionDao()->deleteSubQuestions($parentId);
    }

    public function findQuestionsByIds(array $ids)
    {
        $questions = $this->getQuestionDao()->findQuestionsByIds($ids);

        return ArrayToolkit::index($questions, 'id');
    }

    public function findQuestionsByParentId($id)
    {
        return $this->getQuestionDao()->findQuestionsByParentId($id);
    }

    public function findQuestionsByCourseSetId($courseSetId)
    {
        return $this->getQuestionDao()->findQuestionsByCourseSetId($courseSetId);
    }

    public function findQuestionsByCopyId($copyId)
    {
        return $this->getQuestionDao()->findQuestionsByCopyId($copyId);
    }

    public function findQuestionsByCategoryIds($categoryIds)
    {
        return $this->getQuestionDao()->findQuestionsByCategoryIds($categoryIds);
    }

    public function search($conditions, $sort, $start, $limit, $columns = array())
    {
        $conditions = $this->filterQuestionFields($conditions);
        $questions = $this->getQuestionDao()->search($conditions, $sort, $start, $limit, $columns);

        if (empty($columns) || in_array('stem', $columns)) {
            $that = $this;
            array_walk($questions, function (&$question) use ($that) {
                $question = $that->hasStemImg($question);
            });
        }

        return $questions;
    }

    public function searchCount($conditions)
    {
        $conditions = $this->filterQuestionFields($conditions);

        return $this->getQuestionDao()->count($conditions);
    }

    public function getQuestionConfig($type)
    {
        return $this->biz["question_type.{$type}"];
    }

    public function waveCount($id, $diffs)
    {
        return $this->getQuestionDao()->wave(array($id), $diffs);
    }

    public function judgeQuestion($question, $answer)
    {
        if (!$question) {
            return array('status' => 'notFound', 'score' => 0);
        }

        if (!$answer) {
            return array('status' => 'noAnswer', 'score' => 0);
        }

        // 判断values为空["","",""]
        if (is_array($answer)) {
            $isEmpty = true;
            foreach ($answer as $value) {
                if ('' !== $value) {
                    $isEmpty = false;
                    break;
                }
            }

            if ($isEmpty) {
                return array('status' => 'noAnswer', 'score' => 0);
            }
        }

        $questionConfig = $this->getQuestionConfig($question['type']);

        return $questionConfig->judge($question, $answer);
    }

    public function filterAnswer($question, $answer)
    {
        if (empty($answer)) {
            return $answer;
        }

        $questionConfig = $this->getQuestionConfig($question['type']);

        return $questionConfig->filterAnswer($answer);
    }

    public function hasEssay($questionIds)
    {
        $count = $this->searchCount(array('ids' => $questionIds, 'type' => 'essay'));

        if ($count) {
            return true;
        }

        return false;
    }

    public function getQuestionCountGroupByTypes($conditions)
    {
        $conditions = $this->filterQuestionFields($conditions);

        return $this->getQuestionDao()->getQuestionCountGroupByTypes($conditions);
    }

    /**
     * question_favorite.
     */
    public function getFavoriteQuestion($favoriteId)
    {
        return $this->getQuestionFavoriteDao()->get($favoriteId);
    }

    public function createFavoriteQuestion($fields)
    {
        $user = $this->getCurrentUser();

        $fields['userId'] = $user['id'];
        $fields['target'] = $fields['targetType'].'-'.$fields['targetId'];
        $fields['createdTime'] = time();

        return $this->getQuestionFavoriteDao()->create($fields);
    }

    public function deleteFavoriteQuestion($id)
    {
        return $this->getQuestionFavoriteDao()->delete($id);
    }

    public function searchFavoriteQuestions($conditions, $orderBy, $start, $limit)
    {
        return $this->getQuestionFavoriteDao()->search($conditions, $orderBy, $start, $limit);
    }

    public function searchFavoriteCount($conditions)
    {
        return $this->getQuestionFavoriteDao()->count($conditions);
    }

    public function findUserFavoriteQuestions($userId)
    {
        return $this->getQuestionFavoriteDao()->findUserFavoriteQuestions($userId);
    }

    public function deleteFavoriteByQuestionId($questionId)
    {
        return $this->getQuestionFavoriteDao()->deleteFavoriteByQuestionId($questionId);
    }

    protected function filterQuestionFields($conditions)
    {
        if (!empty($conditions['range']) && 'lesson' == $conditions['range']) {
            $conditions['lessonId'] = 0;
        }

        if (empty($conditions['difficulty'])) {
            unset($conditions['difficulty']);
        }

        if (!empty($conditions['keyword'])) {
            $conditions['stem'] = '%'.trim($conditions['keyword']).'%';
            unset($conditions['keyword']);
        }

        if (empty($conditions['excludeIds'])) {
            unset($conditions['excludeIds']);
        } else {
            $conditions['excludeIds'] = explode(',', $conditions['excludeIds']);
        }

        if (empty($conditions['lessonId'])) {
            unset($conditions['lessonId']);
        }

        if (empty($conditions['courseId'])) {
            unset($conditions['courseId']);
        }

        return $conditions;
    }

    public function findAttachments($questionIds)
    {
        if (empty($questionIds)) {
            return array();
        }

        $conditions = array(
            'type' => 'attachment',
            'targetTypes' => array('question.stem', 'question.analysis'),
            'targetIds' => $questionIds,
        );
        $attachments = $this->getUploadFileService()->searchUseFiles($conditions);
        array_walk($attachments, function (&$attachment) {
            $attachment['dkey'] = $attachment['targetType'].$attachment['targetId'];
        });

        return ArrayToolkit::group($attachments, 'dkey');
    }

    public function hasStemImg($question)
    {
        $question['includeImg'] = false;

        if (preg_match('/<img (.*?)>/', $question['stem'])) {
            $question['includeImg'] = true;
        }

        return $question;
    }

    /**
     * @return QuestionDao
     */
    protected function getQuestionDao()
    {
        return $this->createDao('Question:QuestionDao');
    }

    /**
     * @return QuestionFavoriteDao
     */
    protected function getQuestionFavoriteDao()
    {
        return $this->createDao('Question:QuestionFavoriteDao');
    }

    protected function getUploadFileService()
    {
        return $this->createService('File:UploadFileService');
    }

    /**
     * @return QuestionBankService
     */
    protected function getQuestionBankService()
    {
        return $this->createService('QuestionBank:QuestionBankService');
    }
}
