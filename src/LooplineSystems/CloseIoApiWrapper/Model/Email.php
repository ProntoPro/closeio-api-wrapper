<?php
/**
* Close.io Api Wrapper - LLS Internet GmbH - Loopline Systems
*
* @link      https://github.com/loopline-systems/closeio-api-wrapper for the canonical source repository
* @copyright Copyright (c) 2014 LLS Internet GmbH - Loopline Systems (http://www.loopline-systems.com)
* @license   https://github.com/loopline-systems/closeio-api-wrapper/blob/master/LICENSE (MIT Licence)
*/

namespace LooplineSystems\CloseIoApiWrapper\Model;

use LooplineSystems\CloseIoApiWrapper\Library\Exception\InvalidParamException;
use LooplineSystems\CloseIoApiWrapper\Library\ObjectHydrateHelperTrait;
use LooplineSystems\CloseIoApiWrapper\Library\JsonSerializableHelperTrait;
use Symfony\Component\Config\Definition\Exception\Exception;

class Email implements \JsonSerializable
{

    use ObjectHydrateHelperTrait;
    use JsonSerializableHelperTrait;

    const EMAIL_TYPE_HOME = 'Home';
    const EMAIL_TYPE_OFFICE = 'Office';
    const EMAIL_TYPE_DIRECT = 'Direct';

    /**
     * @var  string
     */
    private $email;

    /**
     * @var  string
     */
    private $type;

    /**
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        if ($data) {
            $this->hydrate($data);
        }
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     * @throws InvalidParamException
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function isEmailValid()
    {
        return false !== filter_var($this->email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
