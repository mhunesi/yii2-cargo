<?php
/**
 * (developer comment)
 *
 * @link http://www.mustafaunesi.com.tr/
 * @copyright Copyright (c) 2021 Polimorf IO
 * @product PhpStorm.
 * @author : Mustafa Hayri ÜNEŞİ <mhunesi@gmail.com>
 * @date: 7/29/21
 * @time: 11:19 AM
 */

namespace mhunesi\cargo\providers;

use mhunesi\cargo\models\CancelShipment;
use Yii;
use yii\base\BaseObject;
use mhunesi\cargo\assets\CargoAsset;
use mhunesi\cargo\models\Shipment;
use mhunesi\cargo\response\BaseResponse;
use mhunesi\cargo\response\CreateShipmentResponse;
use mhunesi\cargo\response\TrackingResponse;

abstract class Provider extends BaseObject
{
    public $name;

    public $config = [];

    public $component;

    public $imageName;

    public $cargoConfigs = [];

    protected $componentClass;

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        $bundle = CargoAsset::register(Yii::$app->view);
        return $bundle->baseUrl .'/images/'. $this->imageName;
    }

    /**
     * @return void
     * @throws \yii\base\InvalidConfigException
     */
    public function prepare()
    {
        if (!($this->component instanceof $this->componentClass)) {
            $this->cargoConfigs = $this->config['cargoConfigs'] ?? [];
            unset($this->config['cargoConfigs']);
            $this->config['class'] = $this->componentClass;
            $this->component = Yii::createObject($this->config);
        }
    }

    /**
     * @param array $configs
     * @return $this
     */
    public function setCargoConfigs(array $configs)
    {
        $this->cargoConfigs = $configs;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setCargoConfig($key,$value)
    {
        $this->cargoConfigs[$key] = $value;
        return $this;
    }

    public function getCargoConfig($key)
    {
        return $this->cargoConfigs[$key] ?? null;
    }


    /**
     * @param Shipment $shipmentModel
     * @return CreateShipmentResponse
     */
    abstract public function createShipment(Shipment $shipmentModel) : CreateShipmentResponse;

    /**
     * @param Shipment $shipmentModel
     * @return CreateShipmentResponse
     */
    abstract public function updateShipment(Shipment $shipmentModel) : CreateShipmentResponse;

    /**
     * @param string|array $trackingNumber
     * @return TrackingResponse|TrackingResponse[]
     */
    abstract public function tracking($trackingNumber);

    /**
     * @param CancelShipment $cancelShipment
     * @return BaseResponse
     */
    abstract public function cancelShipment($cancelShipment) : BaseResponse;

}