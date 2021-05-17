<?php

namespace ApiBundle\Api\Resource\MultiClass;

use ApiBundle\Api\ApiRequest;
use ApiBundle\Api\Resource\AbstractResource;
use Biz\Common\CommonException;
use Biz\MultiClass\MultiClassException;
use Biz\MultiClass\Service\MultiClassService;

class MultiClass extends AbstractResource
{
    const MAX_ASSISTANT_NUMBER = 20;

    public function get(ApiRequest $request)
    {
        return [];
    }

    public function add(ApiRequest $request)
    {
        $multiClass = $this->checkParameters($request->request->all());

        $existed = $this->getMultiClassService()->getMultiClassByTitle($multiClass['title']);

        if ($existed) {
            throw MultiClassException::MULTI_CLASS_EXIST();
        }

        return $this->getMultiClassService()->createMultiClass($multiClass);
    }

    public function update(ApiRequest $request, $id)
    {
        $multiClass = $this->checkParameters($request->request->all());

        $existed = $this->getMultiClassService()->getMultiClassByTitle($multiClass['title']);

        if (!empty($existed) && $id != $existed['id']) {
            throw MultiClassException::MULTI_CLASS_EXIST();
        }

        return $this->getMultiClassService()->updateMultiClass($id, $multiClass);
    }

    private function checkParameters($multiClass)
    {
        $multiClass = array_merge(['copyId' => 0], $multiClass);

        if (empty($multiClass['title']) || empty($multiClass['courseId']) || empty($multiClass['productId'])) {
            throw CommonException::ERROR_PARAMETER_MISSING();
        }

        if (empty($multiClass['teacherId'])) {
            throw MultiClassException::MULTI_CLASS_TEACHER_REQUIRE();
        }

        if (empty($multiClass['assistantIds'])) {
            throw MultiClassException::MULTI_CLASS_ASSISTANT_REQUIRE();
        }

        if (!empty($multiClass['assistantIds']) && count($multiClass['assistantIds']) > self::MAX_ASSISTANT_NUMBER) {
            throw MultiClassException::MULTI_CLASS_ASSISTANT_OUT_MAX_NUMBER();
        }

        return $multiClass;
    }

    /**
     * @return MultiClassService
     */
    protected function getMultiClassService()
    {
        return $this->service('MultiClass:MultiClassService');
    }
}
