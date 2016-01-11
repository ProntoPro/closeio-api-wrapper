<?php
/**
 * Close.io Api Wrapper - LLS Internet GmbH - Loopline Systems
 *
 * @link      https://github.com/loopline-systems/closeio-api-wrapper for the canonical source repository
 * @copyright Copyright (c) 2014 LLS Internet GmbH - Loopline Systems (http://www.loopline-systems.com)
 * @license   https://github.com/loopline-systems/closeio-api-wrapper/blob/master/LICENSE (MIT Licence)
 */

namespace LooplineSystems\CloseIoApiWrapper\Library\Curl;

use LooplineSystems\CloseIoApiWrapper\CloseIoRequest;
use LooplineSystems\CloseIoApiWrapper\CloseIoResponse;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\BadApiRequestException;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\UrlNotSetException;

class Curl
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PATCH = 'PATCH';

    /**
     * @param CloseIoRequest $request
     * @return resource
     */
    private function init(CloseIoRequest $request)
    {
        return curl_init($request->getUrl());
    }

    /**
     * @param $curlHandler
     * @param CloseIoRequest $request
     */
    private function finalize($curlHandler, CloseIoRequest $request)
    {
        curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, $request->getHeaders());
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $request->getData());
        curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlHandler, CURLOPT_USERPWD, $request->getApiKey());
    }

    /**
     * @param $curlHandler
     * @return CloseIoResponse
     */
    private function execute($curlHandler)
    {
        $result = curl_exec($curlHandler);

        $curlInfo = curl_getinfo($curlHandler);
        $lastHttpCode = $curlInfo['http_code'];
        curl_close($curlHandler);

        $response = new CloseIoResponse();
        $response->setReturnCode($lastHttpCode);
        $response->setRawData($result);
        $response->setData(json_decode($result, true));
        $response->setCurlInfoRaw($curlInfo);

        return $response;
    }

    /**
     * @param CloseIoRequest $request
     * @return CloseIoResponse
     * @throws BadApiRequestException
     * @throws UrlNotSetException
     */
    public function getResponse(CloseIoRequest $request)
    {
        if ($request->getUrl() == null) {
            throw new UrlNotSetException();
        }
        $curlHandler = $this->init($request);
        $this->finalize($curlHandler, $request);

        /** @var CloseIoResponse $response */
        $response = $this->execute($curlHandler);

        if ($response->hasErrors()) {
            throw new BadApiRequestException($response->getErrors());
        } else {
            return $response;
        }
    }
}
