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
     * Find all the calls between 2 dates. If none is specified, get all calls.
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @param string|null $leadId
     */
    public function getAllCallsBetween(\DateTime $from = null, \DateTime $to = null, $leadId)
    {
        $query = [];
        if ($from) {
            $query['date_created__gt'] = $from->format(DATE_ISO8601);
        }

        if ($to) {
            $query['date_created__lt'] = $to->format(DATE_ISO8601);
        }

        if ($leadId) {
            $query['leadId'] = $leadId;
        }

        $limit = 100;
        $skip = 0;
        do {
            $continue = false;
            $count = 0;
            $query = array_merge($query, ['_limit' => $limit, '_skip' => $skip]);

            $apiRequest = $this->prepareRequest('get-calls', null, [], $query);

            /** @var CloseIoResponse $result */
            $result = $this->triggerGet($apiRequest);

            if ($result->getReturnCode() === 200) {
                foreach ($result->getData()[CloseIoResponse::GET_ALL_RESPONSE_DATA_KEY] as $data) {
                    $count++;
                    yield new Activity($data);
                }

                $continue = $result->getData()[CloseIoResponse::GET_ALL_RESPONSE_HAS_MORE_KEY];
            }

            $skip += $limit;
        } while ($continue && $count === $limit);
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
