<?php
/**
 * (developer comment)
 *
 * @link http://www.mustafaunesi.com.tr/
 * @copyright Copyright (c) 2021 Polimorf IO
 * @product PhpStorm.
 * @author : Mustafa Hayri ÜNEŞİ <mhunesi@gmail.com>
 * @date: 7/29/21
 * @time: 11:18 AM
 */

namespace mhunesi\cargo\providers\ups;

use mhunesi\cargo\enums\GoodsPaymentType;
use mhunesi\cargo\models\CancelShipment;
use mhunesi\cargo\models\Content;
use mhunesi\cargo\models\Shipment;
use mhunesi\cargo\providers\Provider;
use mhunesi\cargo\response\BaseResponse;
use mhunesi\cargo\response\CreateShipmentResponse;
use mhunesi\cargo\response\TrackingProcess;
use mhunesi\cargo\providers\ups\helpers\CityAreaMap;
use mhunesi\cargo\response\TrackingResponse;
use mhunesi\cargo\response\TrackingResponses;
use yii\helpers\ArrayHelper;

class UpsProvider extends Provider
{
    public $name = 'UPS Kargo';

    public $imageName = 'ups.svg';
    /**
     * @var \mhunesi\cargo\providers\ups\Ups
     */
    public $component;

    protected $componentClass = 'mhunesi\cargo\providers\ups\Ups';

    public function createShipment(Shipment $shipmentModel): CreateShipmentResponse
    {
        $shipmentRequest = $this->prepareRequestData($shipmentModel);

        return new CreateShipmentResponse($this->component->createShipment($shipmentRequest));
    }

    private function prepareRequestData(Shipment $shipment) :array
    {
        $dimensions = $this->prepareDimensions($shipment->contents);

        $ShipperCityCode = CityAreaMap::getCityCode($shipment->sender->address->city->name);
        $ShipperAreaCode = CityAreaMap::getAreaCode($ShipperCityCode, $shipment->sender->address->town->name);

        $ConsigneeCityCode = CityAreaMap::getCityCode($shipment->receiver->address->city->name);
        $ConsigneeAreaCode = CityAreaMap::getAreaCode($ConsigneeCityCode, $shipment->receiver->address->town->name);

        return [
            'SessionID' => $this->component->getSessionID(),
            'ReturnLabelLink' => true,
            'ReturnLabelImage' => true,
            'PaperSize' => "4X6",
            'ShipmentInfo' => [
                "ShipperAccountNumber" => $this->component->customerNumber,
                "ShipperName" => $shipment->sender->name,
                "ShipperContactName" => $shipment->sender->contactName,
                "ShipperAddress" => $shipment->sender->address->addressLine1 . ' ' . $shipment->sender->address->addressLine2,
                "ShipperCityCode" => $ShipperCityCode,
                "ShipperAreaCode" => $ShipperAreaCode,
                "ShipperPostalCode" => $shipment->sender->address->postalCode,
                "ShipperPhoneNumber" => $shipment->sender->phone,
                "ShipperEMail" => $shipment->sender->email,
                "ConsigneeAccountNumber" => "",
                "ConsigneeName" => $shipment->receiver->name,
                "ConsigneeContactName" => $shipment->receiver->contactName,
                "ConsigneeAddress" => $shipment->receiver->address->addressLine1 . ' ' . $shipment->receiver->address->addressLine2,
                "ConsigneeCityCode" => $ConsigneeCityCode,
                "ConsigneeAreaCode" => $ConsigneeAreaCode,
                "ConsigneePostalCode" => $shipment->receiver->address->postalCode,
                "ConsigneePhoneNumber" => $shipment->receiver->phone,
                "ConsigneeEMail" => $shipment->receiver->email,
                "ServiceLevel" => $this->getCargoConfig('ServiceLevel'),
                "PaymentType" => $this->getCargoConfig('PaymentType'),
                "PackageType" => $this->getCargoConfig('PackageType'),
                "NumberOfPackages" => $shipment->total_parcels,
                "CustomerReferance" => $shipment->delivery_number,
                "CustomerInvoiceNumber" => $shipment->order_number,
                "DescriptionOfGoods" => $shipment->order_number,
                "DeliveryNotificationEmail" => $shipment->sender->email,
                "IdControlFlag" => "0",
                "PhonePrealertFlag" => "0",
                "SmsToShipper" => "0",
                "SmsToConsignee" => "0",
                "InsuranceValue" => "0",
                "InsuranceValueCurrency" => "",
                "ValueOfGoods" => "0",
                "ValueOfGoodsCurrency" => "",
                "ValueOfGoodsPaymentType" => "",
                "DeliveryByTally" => "0",
                "ThirdPartyAccountNumber" => "",
                "ThirdPartyExpenseCode" => "0",
                "PackageDimensions" => $dimensions,
            ]
        ];
    }

    private function prepareDimensions($dimension) : array
    {
        $upsDimension = [];
        /**w
         * @var  $key
         * @var Content $value
         */
        foreach ($dimension as $key => $value) {

            for ($i = 0; $i >= $value->quantity; $i++) {
                $upsDimension[] = [
                    "DescriptionOfGoods" => $value->description . ' | ' . $value->sku,
                    "Length" => $value->length,
                    "Height" => $value->height,
                    "Width" => $value->width,
                    "Weight" => $value->weight,
                ];
            }
        }
        return $upsDimension;
    }

    public function updateShipment(Shipment $shipmentModel): CreateShipmentResponse
    {
        $shipmentRequest = $this->prepareShipmentModel($shipmentModel);

        return $this->responseMap($this->component->createShipment($shipmentRequest));
    }

    /**
     * @param array|string $trackingNumber
     */
    public function tracking($trackingNumber)
    {
        if(is_array($trackingNumber)){
            return $this->trackingMulti($trackingNumber);
        }

        return  $this->trackingOne($trackingNumber);
    }

    /**
     * @param CancelShipment $cancelShipment
     * @return BaseResponse
     */
    public function cancelShipment($cancelShipment): BaseResponse
    {
        $response = $this->component->cancelShipment($cancelShipment->orderNumber,$cancelShipment->trackingNumber);
        $options = ArrayHelper::toArray($response);
        return new BaseResponse($options);
    }

    private function trackingOne($trackingNumber)
    {
        $response = $this->component->tracking($trackingNumber);

        return $this->prepareTrackingResponseModel($response);
    }

    private function trackingMulti($trackingNumbers)
    {
        $response = $this->component->trackingList($trackingNumbers);

        $trackingResponses = $response['trackingResponses'];

        unset($response['trackingResponses']);

        $result = new TrackingResponses($response);

        foreach ($trackingResponses as $trackingResponse) {
            $result->trackingResponses[] = $this->prepareTrackingResponseModel($trackingResponse);
        }

        return $result;
    }

    private function prepareTrackingResponseModel($response)
    {
        $trackingHistories = $response['trackingHistory'];

        unset($response['trackingHistory']);

        $trackingResponse = new TrackingResponse($response);

        foreach ($trackingHistories as $trackingHistory) {
            $trackingResponse->trackingProcesses[] = new TrackingProcess($trackingHistory);
        }

        return $trackingResponse;
    }
}