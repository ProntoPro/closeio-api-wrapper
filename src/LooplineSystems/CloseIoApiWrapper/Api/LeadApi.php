<?php
/**
* Close.io Api Wrapper - LLS Internet GmbH - Loopline Systems
*
* @link      https://github.com/loopline-systems/closeio-api-wrapper for the canonical source repository
* @copyright Copyright (c) 2014 LLS Internet GmbH - Loopline Systems (http://www.loopline-systems.com)
* @license   https://github.com/loopline-systems/closeio-api-wrapper/blob/master/LICENSE (MIT Licence)
*/

namespace LooplineSystems\CloseIoApiWrapper\Api;

use LooplineSystems\CloseIoApiWrapper\CloseIoRequest;
use LooplineSystems\CloseIoApiWrapper\CloseIoResponse;
use LooplineSystems\CloseIoApiWrapper\Library\Api\AbstractApi;
use LooplineSystems\CloseIoApiWrapper\Library\Curl\Curl;
use LooplineSystems\CloseIoApiWrapper\Library\Curl\ParallelCurl;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\BadApiRequestException;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\InvalidNewLeadPropertyException;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\InvalidParamException;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\UrlNotSetException;
use LooplineSystems\CloseIoApiWrapper\Model\Lead;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\ResourceNotFoundException;

class LeadApi extends AbstractApi
{
    const NAME = 'LeadApi';

    /**
     * {@inheritdoc}
     */
    protected function initUrls()
    {
        $this->urls = [
            'get-leads' => '/lead/',
            'add-lead' => '/lead/',
            'get-lead-id' => '/lead/[:id]/',
            'get-lead-query' => '/lead/',
            'update-lead' => '/lead/[:id]/',
            'delete-lead' => '/lead/[:id]/',
        ];
    }

    /**
     * @param null         $limit
     * @param null         $skip
     * @param array|string $query
     *
     * @return \LooplineSystems\CloseIoApiWrapper\Model\Lead[]
     */
    public function getAllLeads($limit = null, $skip = null, $query = null)
    {
        /** @var Lead[] $leads */
        $leads = array();

        $filters = [];
        if (!empty($limit)) {
            $filters['_limit'] = $limit;
        }
        if (!empty($skip)) {
            $filters['_skip'] = $skip;
        }
        if (!empty($query)) {
            $filters['query'] = $query;
        }

        $apiRequest = $this->prepareRequest('get-leads', null, [], $filters);

        /** @var CloseIoResponse $result */
        $result = $this->triggerGet($apiRequest);

        if ($result->getReturnCode() == 200) {
            $rawData = $result->getData()[CloseIoResponse::GET_ALL_RESPONSE_LEADS_KEY];

            foreach ($rawData as $lead) {
                $leads[] = new Lead($lead);
            }
        }
        return $leads;
    }

    /**
     * @param $id
     * @return Lead
     * @throws ResourceNotFoundException
     */
    public function getLeadFromId($id)
    {
        $apiRequest = $this->prepareRequest('get-lead-id', null, ['id' => $id]);

        /** @var CloseIoResponse $result */
        $result = $this->triggerGet($apiRequest);

        if ($result->getReturnCode() == 200 && ($result->getData() !== null)) {
            $lead = new Lead($result->getData());
        } else {
            throw new ResourceNotFoundException();
        }
        return $lead;
    }

    /**
     * @param array $query
     * @return Lead
     * @throws ResourceNotFoundException
     */
    public function getLeadFromQuery(array $query)
    {
        $query = array_merge($query, [
            '_limit' => 1,
        ]);

        $apiRequest = $this->prepareRequest('get-lead-query', null, [], $query);

        /** @var CloseIoResponse $result */
        $result = $this->triggerGet($apiRequest);

        if ($result->getReturnCode() == 200
            && ($result->getData() !== null)
            && (in_array('data', $result->getData()))
            && (!empty($result->getData()['data']))
        ) {
            $lead = new Lead($result->getData()['data'][0]);
        } else {
            throw new ResourceNotFoundException();
        }
        return $lead;
    }

    /**
     * @param Lead $lead
     * @return CloseIoResponse
     */
    public function addLead(Lead $lead)
    {
        $this->validateLeadForPost($lead);

        $lead = json_encode($lead);
        $apiRequest = $this->prepareRequest('add-lead', $lead);

        return $this->triggerPost($apiRequest);
    }

    /**
     * @param Lead[] $leads
     *
     * @return CloseIoResponse[]
     */
    public function addLeads(array $leads)
    {
        /** @var CloseIoRequest[] $requests */
        $requests = [];

        foreach ($leads as $lead) {
            $this->validateLeadForPost($lead);

            $lead = json_encode($lead);
            $request = clone $this->prepareRequest('add-lead', $lead);
            $request->setMethod(Curl::METHOD_POST);

            $requests[] = $request;
        }

        $parallelCurl = new ParallelCurl();

        return $parallelCurl->getResponses($requests);
    }

    /**
     * @param Lead $lead
     * @return Lead|string
     * @throws InvalidParamException
     * @throws ResourceNotFoundException
     */
    public function updateLead(Lead $lead)
    {
        // check if lead has id
        if (empty($lead->getId())) {
            throw new InvalidParamException('When updating a lead you must provide the lead ID');
        }
        // remove id from lead since it won't be part of the patch data
        $id = $lead->getId();
        $lead->setId(null);

        $lead = json_encode($lead);
        $apiRequest = $this->prepareRequest('update-lead', $lead, ['id' => $id]);
        $response = $this->triggerPut($apiRequest);

        // return Lead object if successful
        if ($response->getReturnCode() == 200 && ($response->getData() !== null)) {
            $lead = new Lead($response->getData());
        } else {
            throw new ResourceNotFoundException();
        }
        return $lead;
    }

    /**
     * @param Lead[] $leads
     *
     * @return Lead[]
     * @throws InvalidParamException
     * @throws ResourceNotFoundException
     * @throws BadApiRequestException
     * @throws UrlNotSetException
     */
    public function updateLeads(array $leads)
    {
        /** @var CloseIoRequest[] $requests */
        $requests = [];

        foreach ($leads as $lead) {
            // check if lead has id
            if (empty($lead->getId())) {
                throw new InvalidParamException('When updating a lead you must provide the lead ID');
            }
            // remove id from lead since it won't be part of the patch data
            $id = $lead->getId();
            $lead->setId(null);

            $lead = json_encode($lead);
            $request = clone $this->prepareRequest('update-lead', $lead, ['id' => $id]);
            $request->setMethod(Curl::METHOD_PUT);

            $requests[] = $request;
        }

        $parallelCurl = new ParallelCurl();

        $responses = $parallelCurl->getResponses($requests);

        /** @var Lead[] $leads */
        $leads = [];
        foreach ($responses as $response) {
            // return Lead object if successful
            if ($response->getReturnCode() == 200 && ($response->getData() !== null)) {
                $lead = new Lead($response->getData());
            } else {
                throw new ResourceNotFoundException();
            }
            $leads[] = $lead;
        }

        return $leads;
    }

    /**
     * @param $id
     * @return CloseIoResponse
     * @throws ResourceNotFoundException
     */
    public function deleteLead($id){
        $apiRequest = $this->prepareRequest('delete-lead', null, ['id' => $id]);

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
     * @param Lead $lead
     * @throws InvalidNewLeadPropertyException
     * @description Checks if lead does not contain invalid properties
     */
    public function validateLeadForPost(Lead $lead)
    {
        $invalidProperties = ['id', 'organization', 'tasks', 'opportunities'];
        foreach ($invalidProperties as $invalidProperty){
            $getter = 'get' . ucfirst($invalidProperty);
            if ($lead->$getter()){
                throw new InvalidNewLeadPropertyException('Cannot post ' . $invalidProperty . ' to new lead.');
            }
        }
    }

}
