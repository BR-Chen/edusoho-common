<?php

namespace AppBundle\Controller\Question;

use Codeages\Biz\ItemBank\Item\Service\ItemService;
use ExamParser\Parser\Parser;
use ExamParser\Reader\ReadDocx;
use AppBundle\Common\FileToolkit;
use AppBundle\Controller\BaseController;
use Biz\Content\Service\FileService;
use Biz\User\Service\TokenService;
use Biz\QuestionBank\Service\QuestionBankService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File as FileObject;
use Symfony\Component\HttpFoundation\Request;

class QuestionParserController extends BaseController
{
    public function readAction(Request $request, $type, $questionBank)
    {
        $templateInfo = $this->getTemplateInfo($type);
        if ($request->isMethod('POST')) {
            $file = $request->files->get('importFile');

            if ('docx' == FileToolkit::getFileExtension($file)) {
                $result = $this->getFileService()->uploadFile('course_private', $file);
                $uploadFile = $this->getFileService()->parseFileUri($result['uri']);
                try {
                    $questions = $this->parseQuestions($uploadFile['fullpath']);
                } catch (\Exception $e) {
                    return $this->render($templateInfo['readErrorModalTemplate']);
                }

                $token = $this->getTokenService()->makeToken('upload.course_private_file', array(
                    'data' => array(
                        'id' => $result['id'],
                        'filename' => $file->getClientOriginalName(),
                        'fileuri' => $result['uri'],
                        'filepath' => $uploadFile['fullpath'],
                        'questionBankId' => $questionBank['id'],
                        'cacheFilePath' => $this->cacheQuestions($questions, $uploadFile),
                    ),
                    'duration' => 86400,
                    'userId' => $this->getCurrentUser()->getId(),
                ));

                return $this->createJsonResponse(array(
                    'url' => $this->generateUrl($templateInfo['reEditRoute'], array(
                        'token' => $token['token'],
                    )),
                    'success' => true,
                ));
            } else {
                return $this->render($templateInfo['readErrorModalTemplate']);
            }
        }

        return $this->render($templateInfo['readModalTemplate'], array(
            'questionBank' => $questionBank,
        ));
    }

    public function reEditAction(Request $request, $token, $type)
    {
        $token = $this->getTokenService()->verifyToken('upload.course_private_file', $token);
        if (empty($token)) {
            throw new \Exception('超过有效期');
        }
        $data = $token['data'];
        if (!$this->getQuestionBankService()->canManageBank($data['questionBankId'])) {
            return $this->createMessageResponse('error', '您不是该题库管理者，不能查看此页面！');
        }

        $questionsJson = file_get_contents($data['cacheFilePath']);
        $questions = json_decode($questionsJson, true);

        $questionAnalysis = array(
            'choice' => 0,
            'single_choice' => 0,
            'uncertain_choice' => 0,
            'fill' => 0,
            'essay' => 0,
            'material' => 0,
            'determine' => 0,
        );

        $totalScore = 0;

        foreach ($questions as $question) {
            ++$questionAnalysis[$question['type']];
            if ('material' == $question['type']) {
                foreach ($question['subQuestions'] as $subQuestion) {
                    $totalScore += $subQuestion['score'];
                }
            } else {
                $totalScore += $question['score'];
            }
        }
        $templateInfo = $this->getTemplateInfo($type);

        return $this->render($templateInfo['reEditTemplate'], array(
            'filename' => mb_substr(str_replace('.docx', '', $data['filename']), 0, 50, 'utf-8'),
            'questions' => $questions,
            'questionAnalysis' => $questionAnalysis,
            'questionBankId' => $data['questionBankId'],
            'totalScore' => $totalScore,
        ));
    }

    protected function parseQuestions($fullpath)
    {
        $tmpPath = $this->get('kernel')->getContainer()->getParameter('topxia.upload.public_directory').'/tmp';
        $wordRead = new ReadDocx($fullpath, array('resourceTmpPath' => $tmpPath));
        $text = $wordRead->read();
        $self = $this;
        $fileService = $this->getFileService();
        $text = preg_replace_callback(
            '/src=[\'\"](.*?)[\'\"]/',
            function ($matches) use ($self, $fileService) {
                $file = new FileObject($matches[1]);
                $result = $fileService->uploadFile('course', $file);
                $url = $self->get('web.twig.extension')->getFpath($result['uri']);

                return "src=\"{$url}\"";
            },
            $text
        );

        $parser = new Parser($text);

        return $parser->parser();
    }

    protected function cacheQuestions($questions, $uploadFile)
    {
        $fileSystem = new Filesystem();
        $filePath = $uploadFile['fullpath'].'json';
        $fileSystem->dumpFile($filePath, json_encode($questions));

        return $filePath;
    }

    protected function getTemplateInfo($type)
    {
        if (!in_array($type, array('testpaper', 'question'))) {
            return $this->createNotFoundException('parser type not found');
        }

        $info = array();

        if ('testpaper' == $type) {
            $info = array(
                'readModalTemplate' => 'testpaper/manage/read-modal.html.twig',
                'readErrorModalTemplate' => 'testpaper/manage/read-error.html.twig',
                'reEditRoute' => 'testpaper_re_edit',
                'reEditTemplate' => 'testpaper/manage/re-edit.html.twig',
            );
        }

        if ('question' == $type) {
            $info = array(
                'readModalTemplate' => 'question-manage/read-modal.html.twig',
                'readErrorModalTemplate' => 'question-manage/read-error.html.twig',
                'reEditRoute' => 'question_re_edit',
                'reEditTemplate' => 'question-manage/re-edit.html.twig',
            );
        }

        return $info;
    }

    /**
     * @return TokenService
     */
    protected function getTokenService()
    {
        return $this->createService('User:TokenService');
    }

    /**
     * @return QuestionBankService
     */
    protected function getQuestionBankService()
    {
        return $this->createService('QuestionBank:QuestionBankService');
    }

    /**
     * @return ItemService
     */
    protected function getItemService()
    {
        return $this->createService('ItemBank:Item:ItemService');
    }

    /**
     * @return FileService
     */
    protected function getFileService()
    {
        return $this->createService('Content:FileService');
    }
}
