<?php
/**
 * (developer comment)
 *
 * @link http://www.mustafaunesi.com.tr/
 * @copyright Copyright (c) 2021 Polimorf IO
 * @product PhpStorm.
 * @author : Mustafa Hayri ÜNEŞİ <mhunesi@gmail.com>
 * @date: 30.12.2021
 * @time: 12:27
 */

namespace mhunesi\cargo\models;

use yii\base\Model;

class Content extends Model
{
    /**
     * @var string
     */
    public $sku;

    /**
     * @var string
     */
    public $description;

    /**
     * @var double
     */
    public $quantity;

    /**
     * Width
     * @var double
     */
    public $width;

    /**
     * @var double
     */
    public $length;

    /**
     * @var double
     */
    public $height;

    /**
     * @var double
     */
    public $weight;

    /**
     * @var double
     */
    public $price;

    /**
     * Currency ISO Code
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $barcode;

}