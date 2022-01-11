<?php 
namespace mhunesi\cargo\enums;

class PaymentType
{
    /**
     * gönderici öder
     */
    public const PREPAID="P";

    /**
     * alıcı öder
     */
    public const COLLECT="C";

    /**
     * kapıda ödeme
     */
    public const CASH_ON_DELIVERY="COD";
}