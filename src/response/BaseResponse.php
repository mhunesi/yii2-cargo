<?php

namespace mhunesi\cargo\response;

use GuzzleHttp\Client;
use yii\base\BaseObject;
/**
 * @property $status
 * @property $statusCode
 * @property $errorCode
 * @property $errorMessage
 * @property $request
 * @property $response
 * @property $request
 * @property $client
 */
class BaseResponse extends BaseObject
{
    /**
     * @var boolean
     */
    public $status;

    /**
     * @var int
     */
    public $statusCode;

    /**
     * @var string
     */
    public $errorCode;

    /**
     * @var string
     */
    public $errorMessage;

    /**
     * @var mixed
     */
    public $request;

    /**
     * @var mixed
     */
    public $response;

    /**
     * @var Client|\SoapClient
     */
    public $client;
}