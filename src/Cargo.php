<?php

namespace mhunesi\cargo;

use mhunesi\cargo\assets\CargoAsset;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use mhunesi\cargo\providers\Provider;
use mhunesi\cargo\enums\CargoCompany;
use mhunesi\cargo\models\Shipment;
use mhunesi\cargo\models\BaseResponseModel;
use mhunesi\cargo\models\CreateShipmentResponseModel;

/**
 * This is just an example.
 *
 * @property Provider $provider
 */
class Cargo extends Component
{
    /**
     * @var array
     */
    public $providers = [];

    /**
     * @var Provider[]
     */
    private $_providers = [
        CargoCompany::UPS => 'mhunesi\cargo\providers\ups\UpsProvider',
        CargoCompany::HEPSIJET => 'mhunesi\cargo\providers\hepsijet\HepsiJetProvider',
        CargoCompany::ARAS => 'mhunesi\cargo\providers\aras\ArasProvider',
    ];

    /**
     * @var Provider
     */
    private $_provider;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        foreach ($this->providers as $name => $config) {
            if (array_key_exists($name, $this->_providers)) {
                if (!is_object($this->_providers[$name])) {
                    $object = ArrayHelper::merge(['class' => $this->_providers[$name]], ['config' => $this->providers[$name]]);
                    $this->_providers[$name] = Yii::createObject($object);
                }
            }
        }
    }

    public function setProvider($name)
    {
        $this->_providers[$name]->prepare();

        $this->_provider = $this->_providers[$name];

        return $this;
    }


    public function getProviders()
    {
        return $this->_providers;
    }

    public function getProvider()
    {
        return $this->_provider;
    }

    public function createShipment(Shipment $data)
    {
        return $this->_provider->createShipment($data);
    }

    public function updateShipment(Shipment $data)
    {
        return $this->_provider->updateShipment($data);
    }

    public function cancelShipment($trackingNumber)
    {
        return $this->_provider->cancelShipment($trackingNumber);
    }

    /**
     * @param string|array $trackingNumber
     * @return response\TrackingResponse
     */
    public function tracking($trackingNumber)
    {
        return $this->_provider->tracking($trackingNumber);
    }
}
