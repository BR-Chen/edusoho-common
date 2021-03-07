<?php

namespace ApiBundle\Api\Resource\Course;

use ApiBundle\Api\Annotation\ApiConf;
use ApiBundle\Api\ApiRequest;
use ApiBundle\Api\Resource\AbstractResource;
use AppBundle\Common\ArrayToolkit;
use AppBundle\Common\TimeMachine;
use Biz\Classroom\Service\ClassroomService;
use Biz\Course\CourseException;
use Biz\Course\Service\CourseService;
use Biz\Course\Service\MemberService;

class Course extends AbstractResource
{
    const DEFAULT_PAGING_LIMIT = 30;

    /**
     * @ApiConf(isRequiredAuth=false)
     */
    public function get(ApiRequest $request, $courseId)
    {
        $course = $this->getCourseService()->getCourse($courseId);

        if (!$course) {
            throw CourseException::NOTFOUND_COURSE();
        }

        $user = $this->getCurrentUser();

        if ($user->isLogin()) {
            $member = $this->getMemberService()->getCourseMember($course['id'], $user['id']);
        }

        if ($course['parentId'] > 0) {
            $classroom = $this->getClassroomService()->getClassroomByCourseId($course['id']);
            empty($classroom) || $course['classroom'] = $classroom;
        }

        if (!empty($classroom) && empty($member)) {
            $this->joinCourseMemberByClassroomId($course['id'], $classroom['id']);
        }

        $this->getOCUtil()->single($course, ['creator', 'teacherIds']);
        $this->getOCUtil()->single($course, ['courseSetId'], 'courseSet');

        if (!empty($member)) {
            $course['access'] = $this->getCourseService()->canLearnCourse($courseId);
        } else {
            $course['access'] = $this->getCourseService()->canJoinCourse($courseId);
        }

        $course = $this->convertFields($course);

        return $course;
    }

    protected function convertFields($course)
    {
        $enableAudioStatus = $this->getCourseService()->isSupportEnableAudio($course['enableAudio']);
        $course['isAudioOn'] = $enableAudioStatus ? '1' : '0';
        $course['hasCertificate'] = $this->getCourseService()->hasCertificate($course['id']);
        unset($course['enableAudio']);
        $course = $this->getCourseService()->appendSpecInfo($course);

        return $course;
    }

    protected function joinCourseMemberByClassroomId($courseId, $classroomId)
    {
        $classroom = $this->getClassroomService()->getClassroom($classroomId);
        $user = $this->getCurrentUser();

        $classroomMember = $this->getClassroomService()->getClassroomMember($classroom['id'], $user['id']);

        if (empty($classroomMember) || !in_array('student', $classroomMember['role'])) {
            return;
        }

        $info = [
            'joinedChannel' => $classroomMember['joinedChannel'],
            'deadline' => $classroomMember['deadline'],
        ];

        $this->getMemberService()->createMemberByClassroomJoined($courseId, $user['id'], $classroom['id'], $info);
    }

    /**
     * @ApiConf(isRequiredAuth=false)
     */
    public function search(ApiRequest $request)
    {
        $conditions = $request->query->all();
        if (isset($conditions['type']) && 'all' === $conditions['type']) {
            unset($conditions['type']);
        }

        $conditions['status'] = 'published';
        $conditions['courseSetStatus'] = 'published';
        $conditions['parentId'] = isset($conditions['parentId']) ? $conditions['parentId'] : 0;
        //过滤约排课
        $conditions['excludeTypes'] = ['reservation'];
        if (!empty($conditions['lastDays'])) {
            $timeRange = TimeMachine::getTimeRangeByDays($conditions['lastDays']);
            $conditions['outerStartTime'] = $timeRange['startTime'];
            $conditions['outerEndTime'] = $timeRange['endTime'];
        }

        list($offset, $limit) = $this->getOffsetAndLimit($request);
        $sort = $this->getSort($request);

        if ($this->isPluginInstalled('Vip') && isset($conditions['vipLevelId'])) {
            $vipCourseIds = $this->getVipCourseIdsByVipLevelId($conditions['vipLevelId']);
            $conditions['ids'] = empty($vipCourseIds) ? [-1] : $vipCourseIds;
            unset($conditions['vipLevelId']);
        }

        $courses = $this->getCourseService()->searchBySort($conditions, $sort, $offset, $limit);
        $total = $this->getCourseService()->countWithJoinCourseSet($conditions);

        $this->getOCUtil()->multiple($courses, ['creator', 'teacherIds']);
        $this->getOCUtil()->multiple($courses, ['courseSetId'], 'courseSet');

        $courses = $this->getCourseService()->appendHasCertificate($courses);
        $courses = $this->getCourseService()->appendSpecsInfo($courses);

        return $this->makePagingObject($courses, $total, $offset, $limit);
    }

    protected function getVipCourseIdsByVipLevelId($vipLevelId)
    {
        if ('0' == $vipLevelId) {
            $levels = $this->getLevelService()->findEnabledLevels();
            $vipLevelIds = ArrayToolkit::column($levels, 'id');
        } else {
            if (empty($this->getLevelService()->getLevel($vipLevelId))) {
                return [];
            }
            $levels = $this->getLevelService()->findPrevEnabledLevels($vipLevelId);
            $vipLevelIds = array_merge(ArrayToolkit::column($levels, 'id'), [$vipLevelId]);
        }

        if (empty($vipLevelIds)) {
            return [];
        }

        $vipRights = $this->getVipRightService()->findVipRightsBySupplierCodeAndVipLevelIds('course', $vipLevelIds);

        return empty($vipRights) ? [] : ArrayToolkit::column($vipRights, 'uniqueCode');
    }

    /**
     * @return CourseService
     */
    private function getCourseService()
    {
        return $this->service('Course:CourseService');
    }

    /**
     * @return ClassroomService
     */
    private function getClassroomService()
    {
        return $this->service('Classroom:ClassroomService');
    }

    /**
     * @return MemberService
     */
    protected function getMemberService()
    {
        return $this->service('Course:MemberService');
    }

    protected function getLevelService()
    {
        return $this->service('VipPlugin:Vip:LevelService');
    }

    protected function getVipRightService()
    {
        return $this->service('VipPlugin:Marketing:VipRightService');
    }
}
