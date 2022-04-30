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
use yii\base\Component;

/**
 * Test Url https://customerservicestest.araskargo.com.tr/arascargoservice/arascargoservice.asmx
 * Prod Url https://customerws.araskargo.com.tr/arascargoservice.asmx
 *
 * @property SoapClient $client
 * @property SoapClient $informationClient
 */
class Aras extends Component
{
    public $apiUrl = 'https://customerservicestest.araskargo.com.tr/arascargoservice/arascargoservice.asmx?wsdl';

    public $informationUrl = 'https://customerservices.araskargo.com.tr/ArasCargoCustomerIntegrationService/ArasCargoIntegrationService.svc?wsdl';

    public $username = 'neodyum';

    public $password = 'nd2580';

    public $customerCode;

    private $_client;

    private $_informationClient;

    public function init()
    {
        $this->_client = new SoapClient([
            'url' => $this->apiUrl
        ]);

        $this->_informationClient = new SoapClient([
            'url' => $this->informationUrl
        ]);
    }

    /**
     * @return SoapClient
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * @return SoapClient
     */
    public function getInformationClient()
    {
        return $this->_informationClient;
    }
}