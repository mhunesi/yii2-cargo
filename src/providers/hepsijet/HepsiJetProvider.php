<?php
/**
 * (developer comment)
 *
 * @link http://www.mustafaunesi.com.tr/
 * @copyright Copyright (c) 2021 Polimorf IO
 * @product PhpStorm.
 * @author : Mustafa Hayri ÜNEŞİ <mhunesi@gmail.com>
 * @date: 20.12.2021
 * @time: 14:27
 */

namespace mhunesi\cargo\providers\hepsijet;

use mhunesi\cargo\models\CancelShipment;
use mhunesi\cargo\providers\Provider;
use mhunesi\cargo\models\Shipment;
use mhunesi\cargo\response\BaseResponse;
use mhunesi\cargo\response\CreateShipmentResponse;
use mhunesi\cargo\response\TrackingProcess;
use mhunesi\cargo\response\TrackingResponse;
use mhunesi\cargo\response\TrackingResponses;
use mhunesi\hepsijet\HepsiJet;
use mhunesi\hepsijet\models\Cargo;
use mhunesi\hepsijet\models\Company;
use mhunesi\hepsijet\models\CurrentXDock;
use mhunesi\hepsijet\models\Delivery;
use mhunesi\hepsijet\models\DeliveryContent;
use mhunesi\hepsijet\models\Product;
use mhunesi\hepsijet\models\Receiver;
use mhunesi\hepsijet\models\City;
use mhunesi\hepsijet\models\Town;
use mhunesi\hepsijet\models\District;
use mhunesi\hepsijet\models\Address;
use mhunesi\hepsijet\models\Country;
use yii\helpers\ArrayHelper;

class HepsiJetProvider extends Provider
{
    public $name = 'HepsiJet Kargo';

    public $imageName = 'hepsijet.svg';

    protected $componentClass = 'mhunesi\cargo\providers\hepsijet\HepsiJet';

    /**
     * @var HepsiJet
     */
    public $component;

    public function createShipment(Shipment $data): CreateShipmentResponse
    {
        $prefix = $this->getCargoConfig('_prefix','');

        $model = new Cargo([
            'company' => new Company([
                'name' => $this->getCargoConfig('company.name'),
                'abbreviationCode' => $this->getCargoConfig('company.abbreviationCode')
            ]),
            'currentXDock' => new CurrentXDock([
                'abbreviationCode' => $this->getCargoConfig('currentXDock.abbreviationCode')
            ]),
            'delivery' => new Delivery([
                'customerDeliveryNo' => $prefix.$data->delivery_number,
                'customerOrderId' => $prefix.$data->order_number,
                'totalParcels' => $data->total_parcels,
                'desi' => $data->desi,
                'deliverySlotOriginal' => $this->getCargoConfig('delivery.deliverySlotOriginal'),
                'deliveryDateOriginal' => $data->date,
                'deliveryType' => $this->getCargoConfig('delivery.deliveryType'),
                'product' => new Product([
                    'productCode' => $this->getCargoConfig('delivery.product.productCode')
                ]),
                'receiver' => new Receiver([
                    'companyCustomerId' => $prefix .$data->receiver->id,
                    'firstName' => explode(' ',$data->receiver->name)[0] ?? $data->receiver->name,
                    'lastName' => explode(' ',$data->receiver->name)[1] ?? '',
                    'phone1' => $data->receiver->phone,
                    'email' => $data->receiver->email
                ]),
                'senderAddress' => new Address([
                    'companyAddressId' => $this->getCargoConfig('delivery.senderAddress.companyAddressId'),
                    'country' => new Country([
                        'name' => $data->sender->address->country->name
                    ]),
                    'city' => new City([
                        'name' => $data->sender->address->city->name
                    ]),
                    'town' => new Town([
                        'name' => $data->sender->address->town->name,
                    ]),
                    'district' => new District([
                        'name' => $data->sender->address->district->name ?? '',
                    ]),
                    'addressLine1' => $data->sender->address->addressLine1 . ' ' .  $data->sender->address->addressLine2
                ]),
                'recipientAddress' => new Address([
                    'companyAddressId' => $prefix.$data->receiver->address->id,
                    'country' => new Country([
                        'name' => $data->receiver->address->country->name
                    ]),
                    'city' => new City([
                        'name' => $data->receiver->address->city->name
                    ]),
                    'town' => new Town([
                        'name' => $data->receiver->address->town->name,
                    ]),
                    'district' => new District([
                        'name' => $data->receiver->address->district->name ?? '',
                    ]),
                    'addressLine1' => $data->receiver->address->addressLine1 . ' ' .$data->receiver->address->addressLine2,
                ]),
                'recipientPerson' => $data->receiver->name,
                'recipientPersonPhone1' => $data->receiver->phone
            ])
        ]);

        foreach ($data->contents as $content) {
            $model->delivery->addDeliveryContent(new DeliveryContent([
                'sku' => $content->sku,
                'description' => $content->description,
                'quantity' => $content->quantity
            ]));
        }

        $cargoResult = $this->component->delivery()->sendDeliveryOrderEnhanced($model);

        $cargoResultArray = $cargoResult->toArray();

        $labelZpl = $cargoResult->status ? ((array)ArrayHelper::getColumn(ArrayHelper::getValue($cargoResultArray,'data.zplBarcodeDTOList'),'zplBarcode')) : [];

        $trackingNumber = ArrayHelper::getValue($cargoResultArray,'data.customerDeliveryNo');

        return new CreateShipmentResponse([
            'status' => $cargoResult->status,
            'statusCode' => $cargoResult->getResponse()->getStatusCode(),
            'errorCode' => $cargoResult->getResponse()->getStatusCode(),
            'errorMessage' => $cargoResult->getMessage(),
            'tracking_number' => $trackingNumber,
            'label_url' => "https://hepsijet.com/gonderi-takibi/{$trackingNumber}",
            'tracking_url' => "https://hepsijet.com/gonderi-takibi/{$trackingNumber}",
            'label_zpl' => $labelZpl,
            'parcelNumbers' => $this->getParcelNumbers($data->total_parcels,$trackingNumber)
        ]);
    }

    public function updateShipment(Shipment $data): CreateShipmentResponse
    {
        return new CreateShipmentResponse();
    }

    /**
     * @inheritdoc
     */
    public function tracking($trackingNumber)
    {
        $multi = is_array($trackingNumber);

        $response = $this->component->deliveryTransaction()->getDeliveryTracking((array)$trackingNumber);

        $responseModelData = [
            'status' => $response->status,
            'errorMessage' => $response->message,
            'errorCode' => $response->response->getStatusCode(),
            'statusCode' => $response->response->getStatusCode(),
            'response' => $response->getResponse(),
            'request' => $response->getRequest(),
        ];

        $model = $multi ? new TrackingResponses($responseModelData) : new TrackingResponse($responseModelData);

        if($response->status){
            if($multi){
                $allTracking = ArrayHelper::getValue($response->toArray(),'data',[]);
                foreach ($allTracking as $item) {
                    $model->trackingResponses[] = $this->prepareTrackingResponse($item);
                }
            }else{
                if($res = ArrayHelper::getValue($response->toArray(),'data.0',null)){
                    $model->trackingProcesses = $this->prepareTrackingResponse($res)->trackingProcesses;
                }
            }
        }

        return $model;
    }

    public function prepareTrackingResponse($response)
    {
        $model = new TrackingResponse([
            'trackingNumber' => $response['barcode']
        ]);

        foreach ($response['transactions'] as $transaction) {
            $model->trackingProcesses[] = new TrackingProcess([
                'statusCode' => $transaction['deliveryStatus'],
                'date' => (new \DateTime($transaction['transactionDateTime']))->format('Y-m-d H:i:s'),
                'location' => $transaction['location'],
                'description' => $transaction['transaction'],
            ]);
        }

        return $model;
    }

    /**
     * @param CancelShipment $cancelShipment
     * @return BaseResponse
     */
    public function cancelShipment($cancelShipment): BaseResponse
    {
        $response = $this->component->delivery()->deleteDeliveryOrder($cancelShipment->trackingNumber,$cancelShipment->cancelReason);

        return new BaseResponse([
            'status' => $response->status,
            'errorMessage' => $response->message,
            'errorCode' => $response->response->getStatusCode(),
            'statusCode' => $response->response->getStatusCode(),
            'response' => $response->getResponse(),
            'request' => $response->getRequest(),
        ]);
    }

    public function getParcelNumbers($totalParcels,$trackingNumber) :array
    {
        $numbers = [];

        for ($i=0;$i < $totalParcels;$i++){
            $numbers[] = $trackingNumber;
        }

        return $numbers;
    }
}