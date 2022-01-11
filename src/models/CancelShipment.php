<?php
/**
 * (developer comment)
 *
 * @link http://www.mustafaunesi.com.tr/
 * @copyright Copyright (c) 2022 Polimorf IO
 * @product PhpStorm.
 * @author : Mustafa Hayri ÜNEŞİ <mhunesi@gmail.com>
 * @date: 11.01.2022
 * @time: 16:46
 */

namespace mhunesi\cargo\models;

use yii\base\Model;

class CancelShipment extends Model
{
    public $orderNumber;

    public $trackingNumber;

    public $cancelReason ='IPTAL';
}