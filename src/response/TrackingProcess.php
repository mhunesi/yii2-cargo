<?php
namespace mhunesi\cargo\response;

use yii\base\BaseObject;

class TrackingProcess extends BaseObject
{    
    /**
     * Date
     * @var string
     */
    public $date;

    /**
     * StatusCode
     * @var string
     */
    public $statusCode;

    /**
     * Description
     * @var string
     */
    public $description;

    /**
     * Location
     * @var string
     */
    public $location;
}