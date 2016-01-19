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
use LooplineSystems\CloseIoApiWrapper\Library\Curl\Curl;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\InvalidParamException;
use LooplineSystems\CloseIoApiWrapper\Model\Opportunity;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\ResourceNotFoundException;
use LooplineSystems\CloseIoApiWrapper\Model\Status;

class StatusApi extends AbstractApi
{
    const NAME = 'StatusApi';

    /**
     * {@inheritdoc}
     */
    protected function initUrls()
    {
        $this->urls = [
            'get-statuses' => '/status/lead/',
            'add-status' => '/status/lead/',
            'update-status' => '/status/lead/[:id]/',
            'delete-status' => '/status/lead/[:id]/'
        ];
    }

    /**
     * @return Status[]
     */
    public function getAllStatuses()
    {
        /** @var Opportunity[] $statuses */
        $statuses = array();

        $apiRequest = $this->prepareRequest('get-statuses');

        /** @var CloseIoResponse $result */
        $result = $this->triggerGet($apiRequest);

        if ($result->getReturnCode() == 200) {
            $rawData = $result->getData()[CloseIoResponse::GET_ALL_RESPONSE_LEADS_KEY];
            foreach ($rawData as $status) {
                $statuses[] = new Status($status);
            }
        }

        return $statuses;
    }

//    /**
//     * @param $id
//     * @return Opportunity
//     * @throws ResourceNotFoundException
//     */
//    public function getOpportunity($id)
//    {
//        $apiRequest = $this->prepareRequest('get-opportunity', null, ['id' => $id]);
//
//        /** @var CloseIoResponse $result */
//        $result = $this->triggerGet($apiRequest);
//
//        if ($result->getReturnCode() == 200 && ($result->getData() !== null)) {
//            $opportunity = new Opportunity($result->getData());
//        } else {
//            throw new ResourceNotFoundException();
//        }
//        return $opportunity;
//    }

    /**
     * @param Status $status
     * @return CloseIoResponse
     */
    public function addStatus(Status $status)
    {
        $this->validateStatusForPost($status);

        $status = json_encode($status);
        $apiRequest = $this->prepareRequest('add-status', $status);

        $response = $this->triggerPost($apiRequest);

        // return Lead object if successful
        if ($response->getReturnCode() == 200 && ($response->getData() !== null)) {
            $status = new Status($response->getData());
        } else {
            throw new ResourceNotFoundException();
        }

        return $status;
    }

//    /**
//     * @param Opportunity $opportunity
//     * @return Opportunity|string
//     * @throws InvalidParamException
//     * @throws ResourceNotFoundException
//     */
//    public function updateOpportunity(Opportunity $opportunity)
//    {
//        // check if opportunity has id
//        if ($opportunity->getId() == null) {
//            throw new InvalidParamException('When updating a opportunity you must provide the opportunity ID');
//        }
//        // remove id from opportunity since it won't be part of the patch data
//        $id = $opportunity->getId();
//        $opportunity->setId(null);
//
//        $opportunity = json_encode($opportunity);
//        $apiRequest = $this->prepareRequest('update-opportunity', $opportunity, ['id' => $id]);
//        $response = $this->triggerPut($apiRequest);
//
//        // return Opportunity object if successful
//        if ($response->getReturnCode() == 200 && ($response->getData() !== null)) {
//            $opportunity = new Opportunity($response->getData());
//        } else {
//            throw new ResourceNotFoundException();
//        }
//        return $opportunity;
//    }
//
//    /**
//     * @param $id
//     * @return CloseIoResponse
//     * @throws ResourceNotFoundException
//     */
//    public function deleteOpportunity($id){
//        $apiRequest = $this->prepareRequest('delete-opportunity', null, ['id' => $id]);
//
//        /** @var CloseIoResponse $result */
//        $result = $this->triggerDelete($apiRequest);
//
//        if ($result->getReturnCode() == 200) {
//            return $result;
//        } else {
//            throw new ResourceNotFoundException();
//        }
//    }


    /**
     * @param Curl $curl
     */
    public function setCurl($curl)
    {
        $this->curl = $curl;
    }

    /**
     * @param Status $status
     * @return bool
     */
    public function validateStatusForPost(Status $status)
    {
        return true;
    }

}
