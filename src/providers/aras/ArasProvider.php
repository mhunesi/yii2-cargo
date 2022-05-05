<?php
/**
 * (developer comment)
 *
 * @link http://www.mustafaunesi.com.tr/
 * @copyright Copyright (c) 2022 Polimorf IO
 * @product PhpStorm.
 * @author : Mustafa Hayri ÜNEŞİ <mhunesi@gmail.com>
 * @date: 14.02.2022
 * @time: 01:15
 */

namespace mhunesi\cargo\providers\aras;

use mhunesi\cargo\models\Shipment;
use mhunesi\cargo\providers\Provider;
use mhunesi\cargo\response\BaseResponse;
use mhunesi\cargo\models\CancelShipment;
use mhunesi\cargo\response\CreateShipmentResponse;
use mhunesi\cargo\response\TrackingProcess;
use mhunesi\cargo\response\TrackingResponse;
use mhunesi\cargo\response\TrackingResponses;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class ArasProvider extends Provider
{
    public $name = 'Aras Kargo';

    public $imageName = 'aras.svg';
    /**
     * @var Aras
     */
    public $component;

    protected $componentClass = 'mhunesi\cargo\providers\aras\Aras';

    /**
     * @param Shipment $shipmentModel
     * @return CreateShipmentResponse
     */
    public function createShipment(Shipment $shipmentModel): CreateShipmentResponse
    {
        $response = new CreateShipmentResponse();

        $integrationCode = preg_replace('/\D/', '', $shipmentModel->order_number);

        $params = [
            'UserName' => $this->component->username,
            'Password' => $this->component->password,
            'TradingWaybillNumber' => $shipmentModel->order_number, //Sevk İrsaliye No.
            'InvoiceNumber' => $shipmentModel->order_number, //Fatura No
            'IntegrationCode' => $integrationCode, //Sipariş Kodu /Entegrasyon Kodu (mök )
            'ReceiverName' => $shipmentModel->receiver->name,
            'ReceiverAddress' => $shipmentModel->receiver->address->addressLine1 . ' ' . $shipmentModel->receiver->address->addressLine2,
            'ReceiverPhone1' => $shipmentModel->receiver->phone,
            'ReceiverCityName' => $shipmentModel->receiver->address->city->name,
            'ReceiverTownName' => $shipmentModel->receiver->address->town->name ?? '',
            'VolumetricWeight' => $shipmentModel->desi,
            'Weight' => $shipmentModel->desi,
            'SpecialField1' => '',
            'SpecialField2' => '',
            'SpecialField3' => '',
            'IsCod' => $this->getCargoConfig('IsCod'), //'Tahsilatlı Kargo' gönderisi (0=Hayır, 1=Evet)
            'CodAmount' => array_sum(ArrayHelper::getColumn($shipmentModel->contents,'price')), //'Tahsilatlı Kargo' gönderisi (0=Hayır, 1=Evet)
            'CodCollectionType' => $this->getCargoConfig('CodCollectionType'),
            'CodBillingType' => $this->getCargoConfig('CodBillingType',0),
            'Description' => '',
            'PayorTypeCode' => $this->getCargoConfig('PayorTypeCode'), // (1=Gönderici Öder, 2=Alıcı Öder)
            'IsWorldWide' => $this->getCargoConfig('IsWorldWide'), // (1=Gönderici Öder, 2=Alıcı Öder)
        ];

        foreach ($shipmentModel->contents as $k => $content) {

            for ($i = 0;$i < $content->quantity;$i++){

                $barcodeNumber = $integrationCode . str_pad($k + $i + 1,2,"0",STR_PAD_LEFT);

                $params['PieceDetails'][] = [
                    'VolumetricWeight' => round(($content->weight * $content->height * $content->length) / 3000),
                    'Weight' => $content->weight,
                    'BarcodeNumber' => $barcodeNumber,
                    'ProductNumber' => $content->sku,
                    'Description' => $content->description,
                ];

                $response->parcelNumbers[] = $barcodeNumber;
            }
        }

        $params['PieceCount'] = count($params['PieceDetails']);

        try {
            $result = $this->component->client->SetOrder(['orderInfo' => ['Order' => $params],'userName' => $this->component->username,'password' => $this->component->password]);

            $response->response = $result;

            if(isset($result->SetOrderResult->OrderResultInfo)){

                $orderResultInfo = (array)$result->SetOrderResult->OrderResultInfo;

                $response->status = (int)$orderResultInfo['ResultCode'] === 0;

                if($response->status){

                    $response->tracking_number = $integrationCode;

                    /*$getBarcodeResult = $this->component->client->GetBarcode([
                        'Username' => $this->component->username,
                        'Password' => $this->component->password,
                        'integrationCode' => $integrationCode
                    ]);

                    if (isset($getBarcodeResult->GetBarcodeResult)){

                        $response->label_zpl = (array)$getBarcodeResult->GetBarcodeResult->ZebraZpl->string;

                        $response->label_png = (array)$getBarcodeResult->GetBarcodeResult->Images->base64Binary;

                        $barcodeModel = ArrayHelper::toArray($getBarcodeResult->GetBarcodeResult->BarcodeModelLst->BarcodeModel);

                        $response->tracking_number = ArrayHelper::isAssociative($barcodeModel) ? $barcodeModel['TrackingNumber'] : $barcodeModel[0]['TrackingNumber'];

                        $response->tracking_url = 'https://social.araskargo.com.tr/';

                        //$response->parcelNumbers = ArrayHelper::isAssociative($barcodeModel) ? ((array)ArrayHelper::getValue($barcodeModel,'Barcode')) : ArrayHelper::getColumn($barcodeModel,'Barcode');
                    }*/

                }else{
                    $response->errorCode = $orderResultInfo['ResultCode'];
                    $response->errorMessage = $orderResultInfo['ResultMessage'];
                }
            }

            $response->client = $this->component->client;
        }catch (\Exception $exception){
            $response->status = false;
            $response->errorCode = '500';
            $response->errorMessage = $exception->getMessage();
        }

        return $response;
    }

    /**
     * @param Shipment $shipmentModel
     * @return CreateShipmentResponse
     */
    public function updateShipment(Shipment $shipmentModel): CreateShipmentResponse
    {
        return new CreateShipmentResponse();
    }

    /**
     * @param $trackingNumber
     * @return TrackingResponse|TrackingResponses
     */
    public function tracking($trackingNumber)
    {
        if(is_array($trackingNumber)){
            $response = [];

            foreach ($trackingNumber as $item) {
                $response[] = $this->trackingOne($item);
            }

            return new TrackingResponses([
                'status' => true,
                'trackingResponses' => $response
            ]);
        }

        return $this->trackingOne($trackingNumber);
    }

    public function trackingByIntegrationCode($integrationCode)
    {
        $result = $this->component->informationClient->GetQueryJSON([
            'loginInfo' => $this->loginInformation(),
            'queryInfo' =>  "<QueryInfo><QueryType>1</QueryType><IntegrationCode>{$integrationCode}</IntegrationCode></QueryInfo>"
        ]);

        $result = Json::decode($result->GetQueryJSONResult)['QueryResult']['Cargo'] ?? [];

        $trackingNumber = $result['KARGO_TAKIP_NO'] ?? null;

        return new CreateShipmentResponse([
            'status' => !is_null($trackingNumber),
            'tracking_number' => $trackingNumber,
            'tracking_url' => "http://kargotakip.araskargo.com.tr/mainpage.aspx?code={$trackingNumber}"
        ]);
    }

    private function isDelivered($trackingNumber)
    {
        $result = $this->component->informationClient->GetQueryJSON([
            'loginInfo' => $this->loginInformation(),
            'queryInfo' =>  "<QueryInfo><QueryType>1</QueryType><TrackingNumber>{$trackingNumber}</TrackingNumber></QueryInfo>"
        ]);

        $result = Json::decode($result->GetQueryJSONResult)['QueryResult']['Cargo'] ?? [];

        if(empty($result) || $result['DURUM_KODU'] !== "6"){
           return false;
        }

        return new TrackingProcess([
            'statusCode' => 'TESLİM EDİLDİ',
            'date' => date('Y-m-d H:i:s',strtotime($result['TESLIM_TARIHI'] . ' ' . $result['TESLIM_SAATI'])),
            'location' => $result['CIKIS_SUBE'],
            'description' => "Teslim Edildi - ({$result['TESLIM_ALAN']})",
        ]);
    }

    private function trackingOne($trackingNumber){

        try{
            $response = $this->component->informationClient->GetQueryJSON([
                'loginInfo' => $this->loginInformation(),
                'queryInfo' =>  "<QueryInfo><QueryType>9</QueryType><TrackingNumber>{$trackingNumber}</TrackingNumber></QueryInfo>"
            ]);

            $response = Json::decode($response->GetQueryJSONResult);

            $trackingResponse = new TrackingResponse([
                'status' => true,
                'errorMessage' => '',
                'errorCode' => '',
                'statusCode' => '',
                'trackingProcesses' => [],
                'trackingNumber' => $trackingNumber,
                'response' => $this->component->informationClient->__getLastResponse(),
                'request' => $this->component->informationClient->__getLastRequest(),
            ]);

            foreach (($response['QueryResult']['CargoTransaction']) as $trackingHistory) {
                $trackingResponse->trackingProcesses[] = new TrackingProcess([
                    'date' => date('Y-m-d H:i:s',strtotime($trackingHistory['ISLEM_TARIHI'])),
                    'location' => $trackingHistory['BIRIM'],
                    'statusCode' => $trackingHistory['ISLEM'],
                    'description' => $trackingHistory['ACIKLAMA'],
                ]);
            }

            if($cargoDetail = $this->isDelivered($trackingNumber)){
                $trackingResponse->trackingProcesses[] = $cargoDetail;
            }

            return $trackingResponse;

        }catch (\Exception $exception){
            return new TrackingResponse([
                'status' => false,
                'errorMessage' => $exception->getMessage(),
                'errorCode' => $exception->getCode(),
                'statusCode' => $exception->getCode(),
                'response' => $this->component->client->__getLastResponse(),
                'request' => $this->component->client->__getLastRequest(),
            ]);
        }
    }

    /**
     * @param $cancelShipment
     * @return BaseResponse
     */
    public function cancelShipment($cancelShipment): BaseResponse
    {
        $cancelDispatch = $this->component->client->CancelDispatch([
            'userName' => $this->component->username,
            'password' => $this->component->password,
            'integrationCode' => $cancelShipment->orderNumber
        ]);

        return new BaseResponse([
            'status' => (int)$cancelDispatch->CancelDispatchResult->ResultCode === 0,
            'errorMessage' => $cancelDispatch->CancelDispatchResult->ResultMessage,
            'errorCode' => $cancelDispatch->CancelDispatchResult->ResultCode,
            'statusCode' => $cancelDispatch->CancelDispatchResult->ResultCode,
            'response' => $this->component->client->__getLastResponse(),
            'request' => $this->component->client->__getLastRequest(),
        ]);
    }


    protected function loginInformation()
    {

        return sprintf('<LoginInfo>
			<UserName>%s</UserName>
			<Password>%s</Password>
			<CustomerCode>%s</CustomerCode>
		</LoginInfo>', $this->component->username, $this->component->password, $this->component->customerCode);

    }
}