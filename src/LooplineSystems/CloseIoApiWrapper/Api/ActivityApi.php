<?php
/**
* Close.io Api Wrapper - LLS Internet GmbH - Loopline Systems
*
* @link      https://github.com/loopline-systems/closeio-api-wrapper for the canonical source repository
* @copyright Copyright (c) 2014 LLS Internet GmbH - Loopline Systems (http://www.loopline-systems.com)
* @license   https://github.com/loopline-systems/closeio-api-wrapper/blob/master/LICENSE (MIT Licence)
*/

namespace LooplineSystems\CloseIoApiWrapper\Api;

use LooplineSystems\CloseIoApiWrapper\CloseIoResponse;
use LooplineSystems\CloseIoApiWrapper\Library\Api\AbstractApi;
use LooplineSystems\CloseIoApiWrapper\Model\Activity;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\ResourceNotFoundException;

class ActivityApi extends AbstractApi
{
    const NAME = 'ActivityApi';

    /**
     * {@inheritdoc}
     */
    protected function initUrls()
    {
        $this->urls = [
            'get-call' => '/activity/call/?lead_id=[:lead_id]',
            'get-calls' => '/activity/call/',
        ];
    }

    /**
     * @param null $limit
     * @param null $skip
     *
     * @return \Generator|\LooplineSystems\CloseIoApiWrapper\Model\Activity[]
     */
    public function getAllCalls($limit = INF, $skip = 0)
    {
        $totalCount = 0;

        $hasMore = false;
        do {
            /** @var Activity[] $activities */
            $activities = [];

            $result = $this->getPaginatedActivitiesResult(min($limit, 100), $skip);
            $skip += 100;

            $hasMore = $result->getData()[CloseIoResponse::GET_ALL_RESPONSE_HAS_MORE_KEY];
            $rawData = $result->getData()[CloseIoResponse::GET_ALL_RESPONSE_DATA_KEY];

            foreach ($rawData as $activity) {
                $activities[] = new Activity($activity);
            }

            $totalCount += count($activities);

            yield $activities;
        } while ($hasMore && $totalCount < $limit);
    }

    /**
     * @param string $leadId
     *
     * @return \LooplineSystems\CloseIoApiWrapper\Model\Activity[]
     * @throws ResourceNotFoundException
     */
    public function getAllCall($leadId)
    {
        $apiRequest = $this->prepareRequest('get-call', null, ['lead_id' => $leadId]);

        /** @var CloseIoResponse $result */
        $result = $this->triggerGet($apiRequest);

        if ($result->getReturnCode() === 200 && ($result->getData() !== null)) {
            $activity = new Activity($result->getData());
        } else {
            throw new ResourceNotFoundException();
        }

        return $activity;
    }

    private function getPaginatedActivitiesResult($limit, $skip)
    {
        $apiRequest = $this->prepareRequest('get-calls', null, [], [
            '_limit' => $limit,
            '_skip' => $skip
        ]);

        /** @var CloseIoResponse $result */
        $result = $this->triggerGet($apiRequest);

        if ($result->getReturnCode() === 200) {
            return $result;
        }

        return null;
    }
}
