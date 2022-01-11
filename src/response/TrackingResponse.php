<?php

namespace mhunesi\cargo\response;

class TrackingResponse extends BaseResponse
{
    /**
     * TrackingNo
     * @var string
     */
    public $trackingNumber;

    /**
     * trackingHistory
     * @var TrackingProcess[]
     */
    public $trackingProcesses = [];
}