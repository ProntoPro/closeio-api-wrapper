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
use LooplineSystems\CloseIoApiWrapper\Library\Exception\InvalidParamException;
use LooplineSystems\CloseIoApiWrapper\Model\Opportunity;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\ResourceNotFoundException;

class OpportunityApi extends AbstractApi
{
    const NAME = 'OpportunityApi';

    /**
     * {@inheritdoc}
     */
    protected function initUrls()
    {
        $this->urls = [
            'get-opportunities' => '/opportunity/',
            'add-opportunity' => '/opportunity/',
            'get-opportunity' => '/opportunity/[:id]/',
            'update-opportunity' => '/opportunity/[:id]/',
            'delete-opportunity' => '/opportunity/[:id]/'
        ];
    }

    public function getAllOpportunities()
    {
        return $this->getAllOpportunitiesWithQueryParams();
    }

    /**
     * @param array $queryParams
     * @return \LooplineSystems\CloseIoApiWrapper\Model\Opportunity[]
     */
    public function getAllOpportunitiesWithQueryParams($queryParams = [])
    {
        /** @var Opportunity[] $opportunities */
        $opportunities = array();

        $apiRequest = $this->prepareRequest('get-opportunities', null, [], $queryParams);

        /** @var CloseIoResponse $result */
        $result = $this->triggerGet($apiRequest);

        if ($result->getReturnCode() == 200) {
            $rawData = $result->getData()[CloseIoResponse::GET_ALL_RESPONSE_DATA_KEY];
            foreach ($rawData as $opportunity) {
                $opportunities[] = new Opportunity($opportunity);
            }
        }
        return $opportunities;
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @return Opportunity[]|\Generator
     */
    public function getAllOpportunitiesBetween(\DateTime $from, \DateTime $to)
    {
        $query = [
            'date_created__gte' => $from->format(DATE_ISO8601),
            'date_created__lte' => $to->format(DATE_ISO8601),
        ];

        $limit = 100;
        $page = 0;

        do {
            $continue = false;
            $count = 0;

            $apiRequest = $this->prepareRequest('get-opportunities', null, [], array_merge($query, [
                '_limit' => $limit,
                '_skip' => $page * $limit,
            ]));

            $page++;

            $result = $this->triggerGet($apiRequest);

            if ($result->getReturnCode() === 200) {
                foreach ($result->getData()[CloseIoResponse::GET_ALL_RESPONSE_DATA_KEY] as $data) {
                    $count++;
                    yield new Opportunity($data);
                }

                $continue = $result->getData()[CloseIoResponse::GET_ALL_RESPONSE_HAS_MORE_KEY];
            }
        } while ($continue && $count === $limit);
    }

    /**
     * @param $id
     * @return Opportunity
     * @throws ResourceNotFoundException
     */
    public function getOpportunity($id)
    {
        $apiRequest = $this->prepareRequest('get-opportunity', null, ['id' => $id]);

        /** @var CloseIoResponse $result */
        $result = $this->triggerGet($apiRequest);

        if ($result->getReturnCode() == 200 && ($result->getData() !== null)) {
            $opportunity = new Opportunity($result->getData());
        } else {
            throw new ResourceNotFoundException();
        }
        return $opportunity;
    }

    /**
     * @param Opportunity $opportunity
     * @return Opportunity
     * @throws ResourceNotFoundException
     */
    public function addOpportunity(Opportunity $opportunity)
    {
        $this->validateOpportunityForPost($opportunity);

        $opportunity = json_encode($opportunity);
        $apiRequest = $this->prepareRequest('add-opportunity', $opportunity);

        $response = $this->triggerPost($apiRequest);

        if ($response->getReturnCode() == 200 && ($response->getData() !== null)) {
            return new Opportunity($response->getData());
        } else {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * @param Opportunity $opportunity
     * @return Opportunity|string
     * @throws InvalidParamException
     * @throws ResourceNotFoundException
     */
    public function updateOpportunity(Opportunity $opportunity)
    {
        // check if opportunity has id
        if ($opportunity->getId() == null) {
            throw new InvalidParamException('When updating a opportunity you must provide the opportunity ID');
        }
        // remove id from opportunity since it won't be part of the patch data
        $id = $opportunity->getId();
        $opportunity->setId(null);

        $opportunity = json_encode($opportunity);
        $apiRequest = $this->prepareRequest('update-opportunity', $opportunity, ['id' => $id]);
        $response = $this->triggerPut($apiRequest);

        // return Opportunity object if successful
        if ($response->getReturnCode() == 200 && ($response->getData() !== null)) {
            $opportunity = new Opportunity($response->getData());
        } else {
            throw new ResourceNotFoundException();
        }
        return $opportunity;
    }

    /**
     * @param $id
     * @return CloseIoResponse
     * @throws ResourceNotFoundException
     */
    public function deleteOpportunity($id){
        $apiRequest = $this->prepareRequest('delete-opportunity', null, ['id' => $id]);

        /** @var CloseIoResponse $result */
        $result = $this->triggerDelete($apiRequest);

        if ($result->getReturnCode() == 200) {
            return $result;
        } else {
            throw new ResourceNotFoundException();
        }
    }


    /**
     * @param Curl $curl
     */
    public function setCurl($curl)
    {
        $this->curl = $curl;
    }

    /**
     * @param Opportunity $opportunity
     * @return bool
     */
    public function validateOpportunityForPost(Opportunity $opportunity)
    {
        return true;
    }

}
