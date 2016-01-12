<?php
/**
 * Created by PhpStorm.
 * User: netzach
 * Date: 1/11/16
 * Time: 6:16 PM
 */

namespace LooplineSystems\CloseIoApiWrapper\Library\Curl;

use LooplineSystems\CloseIoApiWrapper\CloseIoRequest;
use LooplineSystems\CloseIoApiWrapper\CloseIoResponse;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\BadApiRequestException;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\UrlNotSetException;

class ParallelCurl
{
    /**
     * @param CloseIoRequest[] $requests
     * @return CloseIoResponse[]
     * @throws BadApiRequestException
     * @throws UrlNotSetException
     */
    public function getResponses(array $requests)
    {
        $multiHandle = curl_multi_init();
        $curlHandlers = [];

        foreach ($requests as $id => $request) {
            if ($request->getUrl() == null) {
                throw new UrlNotSetException();
            }
            $curlHandlers[$id] = curl_init($request->getUrl());

            curl_setopt($curlHandlers[$id], CURLOPT_CUSTOMREQUEST, $request->getMethod());
            curl_setopt($curlHandlers[$id], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlHandlers[$id], CURLOPT_HTTPHEADER, $request->getHeaders());
            curl_setopt($curlHandlers[$id], CURLOPT_POSTFIELDS, $request->getData());
            curl_setopt($curlHandlers[$id], CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curlHandlers[$id], CURLOPT_USERPWD, $request->getApiKey());

            curl_multi_add_handle($multiHandle, $curlHandlers[$id]);
        }

        /** @var CloseIoResponse $responses */
        $responses = [];

        $active = null;
        $mrc = null;
        do {
            $mrc = curl_multi_exec($multiHandle, $active);
        } while($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($multiHandle) != -1) {
                do {
                    $mrc = curl_multi_exec($multiHandle, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        foreach($curlHandlers as $id => $curlHandler) {
            $result = curl_multi_getcontent($curlHandler);
            $curlInfo = curl_getinfo($curlHandler);

            curl_multi_remove_handle($multiHandle, $curlHandler);

            $lastHttpCode = $curlInfo['http_code'];

            $response = new CloseIoResponse();
            $response->setReturnCode($lastHttpCode);
            $response->setRawData($result);
            $response->setData(json_decode($result, true));
            $response->setCurlInfoRaw($curlInfo);

            if ($response->hasErrors()) {
                throw new BadApiRequestException($response->getErrors());
            }

            $responses[$id] = $response;
        }

        curl_multi_close($multiHandle);

        return $responses;
    }
}
