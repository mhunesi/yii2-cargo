<?php
namespace mhunesi\cargo\response;

/**
 * @property $tracking_number
 * @property $label_zpl
 * @property $label_url
 */
class CreateShipmentResponse extends BaseResponse
{
    /**
     * cargoTrackingNo
     * @var string
     */
    public $tracking_number;

    /**
     * labelImageType
     * @var array
     */
    public $label_zpl = [];

    /**
     * @var array
     */
    public $label_png = [];

    /**
     * labelUrl
     * @var string
     */
    public $label_url;

    /**
     * Parcel Numbers
     * @var array
     */
    public $parcelNumbers = [];
}