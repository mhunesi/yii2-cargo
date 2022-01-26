Yii2 Cargo Integration
========================

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require mhunesi/yii2-cargo "*"
```

or add

```
"mhunesi/yii2-cargo": "*"
```

to the require section of your `composer.json` file.

## Provider Settings

This package support only two provider. (UPS - HEPSİJET)

Add to components in web.php


```
'cargo' => [
    'class' => \mhunesi\cargo\Cargo::class,
    'providers' => [
        'ups' => [
            'customerNumber' => 'customerNumber',
            'username' => 'username',
            'password' => 'password',
            //Default Configuration
            'cargoConfigs' => [
                'ServiceLevel' => \mhunesi\cargo\providers\ups\enums\ServiceLevel::STANDARD,
                'PaymentType' => \mhunesi\cargo\providers\ups\enums\PaymentType::SHIPPER,
                'PackageType' => \mhunesi\cargo\providers\ups\enums\PackingType::PACKAGE,
            ]
        ],
        'hepsijet' => [
            'username' => 'username',
            'password' => 'password',
            'clientOptions' => [
                'verify' => false,
                'debug' => false,
                'timeout' => '5.0'
            ],
            //Default Configuration
            'cargoConfigs' => [
                'company.name' => 'Company Name',
                'company.abbreviationCode' => 'abbreviationCode',
                'currentXDock.abbreviationCode' => 'abbreviationCode',
                'delivery.senderAddress.companyAddressId' => 'companyAddressId',
                'delivery.product.productCode' => \mhunesi\hepsijet\enums\ProductCode::HX_STD,
                'delivery.deliverySlotOriginal' => \mhunesi\hepsijet\enums\DeliverySlotOriginal::SLOT_OFF,
                'delivery.deliveryType' => \mhunesi\hepsijet\enums\DeliveryType::RETAIL
            ]
        ]
    ]
],
```

## Usage

### 1.Create Shipment

will be continued...