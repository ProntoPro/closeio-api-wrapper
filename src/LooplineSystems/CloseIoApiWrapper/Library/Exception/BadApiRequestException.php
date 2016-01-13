<?php
/**
 * Close.io Api Wrapper - LLS Internet GmbH - Loopline Systems
 *
 * @link      https://github.com/loopline-systems/closeio-api-wrapper for the canonical source repository
 * @copyright Copyright (c) 2014 LLS Internet GmbH - Loopline Systems (http://www.loopline-systems.com)
 * @license   https://github.com/loopline-systems/closeio-api-wrapper/blob/master/LICENSE (MIT Licence)
 */

namespace LooplineSystems\CloseIoApiWrapper\Library\Exception;

class BadApiRequestException extends \Exception {

    /**
     * {@inheritdoc}
     */
    public function __construct(array $allErrors)
    {
        $output = json_encode($this->getFilteredErrors($allErrors));

        parent::__construct('Api request returned errors. ' . PHP_EOL . $output);
    }

    private function getFilteredErrors(array $aggregate)
    {
        $errors = [];
        foreach ($aggregate as $type => $error) {
            if (!empty($error)) {
                if (is_array($error)) {
                    $errors[$type] = $this->getFilteredErrors($error);
                } else {
                    $errors[$type] = $error;
                }
            }
        }

        return $errors;
    }
} 
