<?php

namespace Biz\Visualization\Job;

use Biz\System\Service\SettingService;
use Biz\Visualization\Dao\UserLearnDailyDao;

class RefreshUserLearnDailyJob extends BaseRefreshJob
{
    const REFRESH_TYPE = 'user';
    const CACHE_NAME = 'refresh_user';
    const LIMIT = 10000;

    public function execute()
    {
        $statisticsSetting = $this->getSettingService()->get('videoEffectiveTimeStatistics', []);
        if (empty($statisticsSetting) || 'playing' == $statisticsSetting['statistical_dimension']) {
            $this->refreshByWatchDaily();
        } else {
            $this->refreshByStayDaily();
        }

        $this->getCacheService()->clear(self::CACHE_NAME);
    }

    protected function refreshByStayDaily()
    {
        $limit = self::LIMIT;
        $totalPage = ceil($this->biz['db']->fetchColumn('SELECT COUNT(*) FROM `user_learn_daily`') / $limit);
        for ($page = 0; $page <= $totalPage; ++$page) {
            $start = $page * $limit;

            $updateFields = $this->biz['db']->fetchAll("
                SELECT l.id AS id, IF(s.sumTime, s.sumTime, 0) AS sumTime FROM `user_learn_daily` l LEFT JOIN (
                    SELECT userId, dayTime, sum(sumTime) AS sumTime
                    FROM `user_stay_daily` GROUP BY userId, dayTime
                ) AS s ON l.dayTime = s.dayTime AND l.userId = s.userId LIMIT {$start}, {$limit};
            ");

            if (!empty($updateFields)) {
                $this->getUserLearnDailyDao()->batchUpdate(array_column($updateFields, 'id'), $updateFields);
            }
            $this->getLogger()->addInfo("从{$start}刷新user_learn_daily结束");
        }
    }

    protected function refreshByWatchDaily()
    {
        $limit = self::LIMIT;
        $totalPage = ceil($this->biz['db']->fetchColumn('SELECT COUNT(*) FROM `user_learn_daily`') / $limit);
        for ($page = 0; $page <= $totalPage; ++$page) {
            $start = $page * $limit;
            $watchData = $this->biz['db']->fetchAll("
                SELECT l.id AS id, IF(s.sumTime, s.sumTime, 0) AS sumTime FROM user_learn_daily l INNER JOIN (
                    SELECT userId, dayTime, sum(sumTime) AS sumTime FROM user_video_daily GROUP BY userId, dayTime
                ) AS s ON l.dayTime = s.dayTime AND l.userId = s.userId LIMIT {$start}, {$limit};
        ");
            $watchData = array_column($watchData, null, 'id');

            $stayData = $this->biz['db']->fetchAll("
                SELECT l.id AS id, IF(s.sumTime, s.sumTime, 0) AS sumTime FROM user_learn_daily l INNER JOIN (
                    SELECT userId, dayTime, sum(sumTime) AS sumTime FROM activity_stay_daily WHERE mediaType != 'video' GROUP BY userId, dayTime
                ) AS s ON l.dayTime = s.dayTime AND l.userId = s.userId LIMIT  {$start}, {$limit};
        ");
            $stayData = array_column($stayData, null, 'id');
            array_walk($stayData, function (&$data) use (&$watchData) {
                $data['sumTime'] += empty($watchData[$data['id']]) ? 0 : $watchData[$data['id']]['sumTime'];
                unset($watchData[$data['id']]);
            });

            $updateFields = array_merge($stayData, $watchData);
            if (!empty($updateFields)) {
                $this->getUserLearnDailyDao()->batchUpdate(array_column($updateFields, 'id'), $updateFields);
            }

            $this->getLogger()->addInfo("从{$start}刷新user_learn_daily结束");
        }
    }

    /**
     * @return SettingService
     */
    protected function getSettingService()
    {
        return $this->biz->service('System:SettingService');
    }

    /**
     * @return UserLearnDailyDao
     */
    protected function getUserLearnDailyDao()
    {
        return $this->biz->dao('Visualization:UserLearnDailyDao');
    }
}
