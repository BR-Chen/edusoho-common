<?php

namespace Biz\QuestionBank\Service\Impl;

use Biz\BaseService;
use Biz\QuestionBank\Dao\QuestionBankDao;
use Biz\QuestionBank\Service\CategoryService;
use Biz\QuestionBank\Service\MemberService;
use Biz\QuestionBank\Service\QuestionBankService;
use AppBundle\Common\ArrayToolkit;
use Biz\Taxonomy\CategoryException;
use Biz\Common\CommonException;
use Biz\QuestionBank\QuestionBankException;
use Codeages\Biz\ItemBank\ItemBank\Service\ItemBankService;

class QuestionBankServiceImpl extends BaseService implements QuestionBankService
{
    public function getQuestionBank($id)
    {
        return $this->getQuestionBankDao()->get($id);
    }

    public function getQuestionBankByCourseSetId($courseSetId)
    {
        return $this->getQuestionBankDao()->getByCourseSetId($courseSetId);
    }

    public function findQuestionBanksByIds($ids)
    {
        return $this->getQuestionBankDao()->findByIds($ids);
    }

    public function searchQuestionBanks($conditions, $orderBys, $start, $limit, $columns = array())
    {
        $conditions = $this->prepareConditions($conditions);

        return $this->getQuestionBankDao()->search($conditions, $orderBys, $start, $limit, $columns);
    }

    public function countQuestionBanks($conditions)
    {
        $conditions = $this->prepareConditions($conditions);

        return $this->getQuestionBankDao()->count($conditions);
    }

    public function createQuestionBank($fields)
    {
        if (!ArrayToolkit::requireds($fields, array('name', 'categoryId'))) {
            $this->createNewException(CommonException::ERROR_PARAMETER_MISSING());
        }

        $category = $this->getCategoryService()->getCategory($fields['categoryId']);
        if (empty($category)) {
            $this->createNewException(CategoryException::NOTFOUND_CATEGORY());
        }

        try {
            $this->beginTransaction();
            $questionBank = array(
                'name' => $fields['name'],
                'categoryId' => $fields['categoryId'],
                'orgCode' => $this->getCurrentUser()->getSelectOrgCode(),
            );
            $questionBank = $this->fillOrgId($questionBank);
            $itemBank = $this->getItemBankService()->createItemBank(array('name' => $questionBank['name']));
            $questionBank['itemBankId'] = $itemBank['id'];
            $questionBank = $this->getQuestionBankDao()->create($questionBank);
            $this->getCategoryService()->waveCategoryBankNum($fields['categoryId'], 1);

            if (!empty($fields['members'])) {
                $members = explode(',', $fields['members']);
                $this->getMemberService()->batchCreateMembers($questionBank['id'], $members);
            }

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }

        return $questionBank;
    }

    public function updateQuestionBankWithMembers($id, $fields, $members)
    {
        $questionBank = $this->getQuestionBank($id);
        if (empty($questionBank)) {
            $this->createNewException(QuestionBankException::NOT_FOUND_BANK());
        }

        if (!empty($fields['categoryId'])) {
            $category = $this->getCategoryService()->getCategory($fields['categoryId']);
            if (empty($category)) {
                $this->createNewException(CategoryException::NOTFOUND_CATEGORY());
            }
        }

        try {
            $this->beginTransaction();

            $updateFields = array(
                'name' => empty($fields['name']) ? $questionBank['name'] : $fields['name'],
                'categoryId' => empty($fields['categoryId']) ? $questionBank['categoryId'] : $fields['categoryId'],
            );
            $newQuestionBank = $this->updateQuestionBank($id, $updateFields);

            if (!empty($fields['categoryId'])) {
                $this->changeQuestionBankCategory($fields['categoryId'], $questionBank['categoryId']);
            }

            $this->getMemberService()->resetBankMembers($newQuestionBank['id'], $members);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }

        return $newQuestionBank;
    }

    public function updateQuestionBank($id, $fields)
    {
        $fields = ArrayToolkit::parts($fields, array('name', 'categoryId', 'isHidden'));
        if (empty($fields)) {
            $this->createNewException(CommonException::ERROR_PARAMETER());
        }

        $fields = $this->fillOrgId($fields);

        return $this->getQuestionBankDao()->update($id, $fields);
    }

    public function updateQuestionBankByCourseSetId($courseSetId, $fields)
    {
        $bank = $this->getQuestionBankByCourseSetId($courseSetId);
        if (empty($bank)) {
            return array();
        }

        return $this->updateQuestionBank($bank['id'], $fields);
    }

    public function deleteQuestionBank($id)
    {
        $questionBank = $this->getQuestionBank($id);
        if (empty($questionBank)) {
            $this->createNewException(QuestionBankException::NOT_FOUND_BANK());
        }

        if ($questionBank['testpaperNum'] > 0 || $questionBank['questionNum'] > 0) {
            $this->createAccessDeniedException();
        }

        try {
            $this->beginTransaction();

            $this->getQuestionBankDao()->delete($id);
            $this->getItemBankService()->deleteItemBank($questionBank['itemBankId']);

            $this->getCategoryService()->waveCategoryBankNum($questionBank['categoryId'], -1);

            $this->getMemberService()->batchDeleteByBankId($questionBank['id']);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function canManageBank($bankId)
    {
        $user = $this->getCurrentUser();

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->hasPermission('admin_question_bank') || $user->hasPermission('admin_v2_question_bank')) {
            return true;
        }

        if ($this->getMemberService()->isMemberInBank($bankId, $user['id'])) {
            return true;
        }

        return false;
    }

    public function waveTestpaperNum($id, $diff)
    {
        $questionBank = $this->getQuestionBank($id);
        $this->getItemBankService()->updateAssessmentNum($questionBank['itemBankId'], $diff);

        return $this->getQuestionBankDao()->wave(array($id), array('testpaperNum' => $diff));
    }

    public function waveQuestionNum($id, $diff)
    {
        $questionBank = $this->getQuestionBank($id);
        $this->getItemBankService()->updateItemNum($questionBank['itemBankId'], $diff);

        return $this->getQuestionBankDao()->wave(array($id), array('questionNum' => $diff));
    }

    public function findUserManageBanks()
    {
        $user = $this->getCurrentUser();

        if (!$user->isLogin()) {
            return array();
        }

        if ($user->isSuperAdmin() || $user->hasPermission('admin_question_bank') || $user->hasPermission('admin_v2_question_bank')) {
            $banks = $this->getQuestionBankDao()->findAll();
        } else {
            $members = $this->getMemberService()->findMembersByUserId($user['id']);
            $banks = $this->findQuestionBanksByIds(ArrayToolkit::column($members, 'bankId'));
        }

        return $banks;
    }

    protected function changeQuestionBankCategory($newCategoryId, $oldCategoryId)
    {
        if ($newCategoryId != $oldCategoryId) {
            $this->getCategoryService()->waveCategoryBankNum($newCategoryId, 1);
            $this->getCategoryService()->waveCategoryBankNum($oldCategoryId, -1);
        }
    }

    protected function prepareConditions($conditions)
    {
        if (isset($conditions['categoryId'])) {
            $conditions['categoryIds'] = array();
            if (!empty($conditions['categoryId'])) {
                $childrenIds = $this->getCategoryService()->findAllChildrenIdsByParentId($conditions['categoryId']);
                $conditions['categoryIds'] = array_merge(array($conditions['categoryId']), $childrenIds);
            }
            unset($conditions['categoryId']);
        }

        $conditions['isHidden'] = 0;

        return $conditions;
    }

    /**
     * @return QuestionBankDao
     */
    protected function getQuestionBankDao()
    {
        return $this->createDao('QuestionBank:QuestionBankDao');
    }

    /**
     * @return CategoryService
     */
    protected function getCategoryService()
    {
        return $this->createService('QuestionBank:CategoryService');
    }

    /**
     * @return MemberService
     */
    protected function getMemberService()
    {
        return $this->createService('QuestionBank:MemberService');
    }

    /**
     * @return ItemBankService
     */
    protected function getItemBankService()
    {
        return $this->createService('ItemBank:ItemBank:ItemBankService');
    }
}
