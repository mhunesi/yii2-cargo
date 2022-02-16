<?php

/**
 * (developer comment)
 *
 * @link http://www.mustafaunesi.com.tr/
 * @copyright Copyright (c) 2022 Polimorf IO
 * @product PhpStorm.
 * @author : Mustafa Hayri ÜNEŞİ <mhunesi@gmail.com>
 * @date: 14.02.2022
 * @time: 01:14
 */

namespace mhunesi\cargo\providers\aras;

use mhunesi\cargo\helpers\SoapClient;
use mhunesi\cargo\models\Shipment;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Test Url https://customerservicestest.araskargo.com.tr/arascargoservice/arascargoservice.asmx
 * Prod Url https://customerws.araskargo.com.tr/arascargoservice.asmx
 *
 * @property SoapClient $client
 */
class Aras extends Component
{
    public $apiUrl = 'http://customerservices.araskargo.com.tr/ArasCargoCustomerIntegrationService/ArasCargoIntegrationService.svc?wsdl';

    public $username = 'neodyum';

    public $password = 'nd2580';

    public $customerCode;

    private $_client;

    public function init()
    {
        $this->_client = new SoapClient([
            'url' => $this->apiUrl
        ]);
    }

    /**
     * @return SoapClient
     */
    public function getClient()
    {
        return $this->_client;
    }
}