<?php
namespace mhunesi\cargo\models;

class Product 
{    
    /**
     * CommodityDescription
     *
     * @var string
     */
    public $commodityDescription;
    /**
     * Piece
     *
     * @var integer
     */
    public $piece;
    /**
     * Price
     *
     * @var float
     */
    public $price;
    /**
     * Currency
     *
     * @var string
     */
    public $currency;
    /**
     * HarmonisedCode
     *
     * @var string
     */
    public $harmonisedCode;
    /**
     * SkuCode
     *
     * @var string
     */
    public $skuCode;
}