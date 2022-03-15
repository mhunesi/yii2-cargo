<?php
/**
 * (developer comment)
 *
 * @link http://www.mustafaunesi.com.tr/
 * @copyright Copyright (c) 2022 Polimorf IO
 * @product PhpStorm.
 * @author : Mustafa Hayri ÜNEŞİ <mhunesi@gmail.com>
 * @date: 10.01.2022
 * @time: 16:36
 */

namespace mhunesi\cargo\providers\ups;

use DateTime;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * This is just an example.
 * Test Url : https://ws.ups.com.tr/wsCreateShipmenttest/wsCreateShipment.asmx?wsdl
 */
class Ups extends Component
{
    public $customerNumber;

    public $username;

    public $password;

    public $apiUrl = "http://ws.ups.com.tr/wsCreateShipment/wsCreateShipment.asmx?wsdl";

    public $trackingApiUrl = "https://ws.ups.com.tr/QueryPackageInfo/wsQueryPackagesInfo.asmx?wsdl";

    protected $sessionID;

    protected $soapClient;

    public function init()
    {
        $this->prepareClient($this->apiUrl);
        $this->sessionID = $this->login();
    }

    private function prepareClient($url)
    {
        $context = stream_context_create(array(
            'ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true)
        ));

        $this->soapClient = new \SoapClient($url, [
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => true,
            'stream_context' => $context,
        ]);
    }

    public function login()
    {
        $login = [
            "CustomerNumber" => $this->customerNumber,
            "UserName" => $this->username,
            "Password" => $this->password
        ];
        $session = $this->soapClient->Login_Type1($login);

        return $session->Login_Type1Result->SessionID;
    }

    public function getSessionID()
    {
        return $this->sessionID;
    }

    public function createShipment($shipment)
    {
        $response = [];

        try {
            $_response = $this->soapClient->CreateShipment_Type3_ZPL_Types($shipment);
            if ((int)$_response->CreateShipment_Type3_ZPL_TypesResult->ErrorCode !== 0) {
                throw new \Exception($_response->CreateShipment_Type3_ZPL_TypesResult->ErrorDefinition,
                    $_response->CreateShipment_Type3_ZPL_TypesResult->ErrorCode);
            }
            $response['status'] = true;
            $response['tracking_number'] = $_response->CreateShipment_Type3_ZPL_TypesResult->ShipmentNo;
            $response['label_zpl'] = (array)$_response->CreateShipment_Type3_ZPL_TypesResult->ZplResult->string;
            $response['label_png'] =(array)($_response->CreateShipment_Type3_ZPL_TypesResult->BarkodArrayPng->string ?? []);
            $response['label_url'] = $_response->CreateShipment_Type3_ZPL_TypesResult->LinkForLabelPrinting;
            $response['tracking_url'] = "https://www.ups.com.tr/WaybillSorgu.aspx?Waybill={$response['tracking_number']}";
            $response['parcelNumbers'] = $this->getParcels((array)$response['label_zpl']);
        } catch (\Exception $th) {
            $response['status'] = false;
            $response['errorMessage'] = $th->getMessage();
            $response['statusCode'] = $th->getCode();
        }

        $response['request'] = $this->soapClient->__getLastRequest();
        $response['response'] = $this->soapClient->__getLastResponse();
        $response['client'] = $this->soapClient;

        return $response;
    }

    public function getParcels($zpl_barcodes)
    {
        $parcels = [];

        foreach ($zpl_barcodes as $zpl_barcode) {
            preg_match_all('/\^FO50,480.*?\^FD(?P<barcode>.*?)\^FS$.*?/mxs', $zpl_barcode, $result);
            $parcels[] = ArrayHelper::getValue($result, 'barcode.0');
        }

        return $parcels;
    }

    /**
     * @param $customerReference
     * @param $cargoTrackingNumber
     * @return array
     * Erhan Ulaş gelmişti.
     */
    public function cancelShipment($customerReference, $cargoTrackingNumber)
    {
        $cancel = [
            "sessionId" => $this->sessionID,
            "customerCode" => $customerReference,
            "waybillNumber" => $cargoTrackingNumber,
        ];

        $response = [];

        try {
            $soapResponse = $this->soapClient->Cancel_Shipment_V1($cancel);
            if (isset($soapResponse->Cancel_Shipment_V1Result->ErrorDefinition)) {
                throw new \Exception($soapResponse->Cancel_Shipment_V1Result->ErrorDefinition);
            }
            $response['status'] = true;
        } catch (\SoapFault $th) {
            $response['status'] = false;
            $response['errorMessage'] = $th->getMessage();
            $response['statusCode'] = $th->getCode();
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['errorMessage'] = $th->getMessage();
            $response['statusCode'] = $th->getCode();
        }

        $response['request'] = $this->soapClient->__getLastRequest();
        $response['response'] = $this->soapClient->__getLastResponse();
        $response['client'] = $this->soapClient;

        return $response;
    }

    public function tracking(string $cargoTrackingNumber)
    {
        $this->prepareClient($this->trackingApiUrl);

        $response = ['trackingNumber' => $cargoTrackingNumber];

        try {
            $data = [
                "SessionID" => $this->sessionID,
                "InformationLevel" => 10,
                "TrackingNumber" => $cargoTrackingNumber
            ];
            $_response = $this->soapClient->GetTransactionsByTrackingNumber_V1($data);

            $trackingHistory = [];

            foreach ($_response->GetTransactionsByTrackingNumber_V1Result->PackageTransaction as $key => $tracking) {
                $datetime = new DateTime(str_replace("-", "", $tracking->ProcessTimeStamp));
                $trackingHistory[] = [
                    "date" => $datetime->format("Y-m-d H:i:s"),
                    "statusCode" => $tracking->StatusCode,
                    "description" => $this->prepareDescription($tracking),
                    "location" => $tracking->OperationBranchName,
                ];

            }
            $response['trackingHistory'] = $trackingHistory;
            $response['status'] = true;
        } catch (\Exception $th) {
            $response['status'] = false;
            $response['errorMessage'] = $th->getMessage();
            $response['statusCode'] = $th->getCode();
        }

        $response['request'] = $this->soapClient->__getLastRequest();
        $response['response'] = $this->soapClient->__getLastResponse();
        $response['client'] = $this->soapClient;

        return $response;
    }

    public function trackingList($cargoTrackingNumbers, $trnType = 'ALL_TRANSACTIONS',$referansType = 'WAYBILL_TYPE')
    {
        $this->prepareClient($this->trackingApiUrl);
        $response = [
            'trackingResponses' => []
        ];

        try {
            $data = [
                "SessionID" => $this->sessionID,
                "InformationLevel" => 10,
                'trnType' => $trnType,
                "refList" => [
                    'referansType' => $referansType,
                    'referansList' => $cargoTrackingNumbers
                ]
            ];
            $_response = $this->soapClient->GetTransactionsByList_V2($data);

            $trackingGroup = ArrayHelper::index(array_filter($_response->GetTransactionsByList_V2Result->PackageTransactionwithDeliveryDetailV2),null,'TrackingNumber');

            foreach ($trackingGroup as $trackingNumber => $allProcess) {
                $trackingResponse = [
                    'trackingNumber' => trim($trackingNumber),
                ];

                foreach ($allProcess as $process) {
                    $datetime = new DateTime(str_replace("-", "", $process->ProcessTimeStamp));

                    $trackingResponse['trackingHistory'][] = [
                        "date" => $datetime->format("Y-m-d H:i:s"),
                        "statusCode" => $process->StatusCode,
                        "description" => $this->prepareDescription($process),
                        "location" => $process->OperationBranchName,
                    ];
                }

                $response['trackingResponses'][] = $trackingResponse;
            }

            $response['status'] = true;

        } catch (\SoapFault $th) {
            $response['status'] = false;
            $response['errorMessage'] = $th->getMessage();
            $response['statusCode'] = $th->getCode();
        } catch (\Throwable $th) {
            $response['status'] = false;
            $response['errorMessage'] = $th->getMessage();
            $response['statusCode'] = $th->getCode();
        } finally {
            $response['request'] = $this->soapClient->__getLastRequest();
            $response['response'] = $this->soapClient->__getLastResponse();
            $response['client'] = $this->soapClient;
            return $response;
        }
    }

    private function prepareDescription($process)
    {
        switch ((int)$process->StatusCode) {
            case 31 :
                return "ÇAĞRI SONUCU ALINDI";
                break;
            case 6 :
                return $process->ProcessDescription2;
                break;
            case 5 :
                return "KURYE GERİ GETİRDİ ({$process->ProcessDescription2}})";
                break;
            case 4 :
                return "KURYE DAĞITMAK ÜZERE ÇIKARDI ({$process->ProcessDescription2})";
                break;
            case 3 :
                return "ILERIKI BIR TARIHTE TESLIMAT ICIN BEKLETILIYOR";
                break;
            case 2 :
                return "ALICIYA TESLİM EDİLDİ: {$process->SignedPersonName} {$process->SignedPersonSurname} ({$process->SignedPersonRelation})";
                break;
            case 1 :
                return "GİRİŞ SCAN EDİLDİ ({$process->ProcessDescription2})";
                break;
        }

        throw new \Exception("Unknown StatusCode : {$process->StatusCode}");
    }
}