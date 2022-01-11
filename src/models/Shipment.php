<?php

namespace mhunesi\cargo\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @property string $order_number
 * @property string $delivery_number
 * @property string $payment_type
 * @property string $date
 * @property int $desi
 * @property int $total_parcels
 * @property Receiver $receiver
 * @property Sender $sender
 * @property Content[] $contents
 */
class Shipment extends Model
{
    /**
     * Sipariş Numarası
     * @var int|string
     */
    public $order_number;

    /**
     * Sevkiyat No
     * @var int|string
     */
    public $delivery_number;

    /**
     * InvoiceDate
     * @var string müşteriye kesilen fatura tarihi
     */
    public $date;

    /**
     * @var double
     */
    private $_desi = 0;
    /**
     * @var int
     */
    private $_total_parcels = 0;
    /**
     * Sender
     * @var Sender
     */
    private $_sender;
    /**
     * Receiver
     * @var Receiver
     */
    private $_receiver;
    /**
     * Delivery Content
     * @var Content[]
     */
    private $_contents = [];

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return ArrayHelper::merge(parent::fields(), [
            'receiver' => function () {
                return $this->getReceiver();
            },
            'sender' => function () {
                return $this->getSender();
            },
            'contents' => function () {
                return $this->getContents();
            },
            'desi' => function () {
                return $this->getDesi();
            },
            'total_parcels' => function () {
                return $this->getTotal_Parcels();
            }
        ]);
    }

    /**
     * @return Receiver
     */
    public function getReceiver(): Receiver
    {
        return $this->_receiver;
    }

    /**
     * @param array|Receiver $receiver
     */
    public function setReceiver($receiver): void
    {
        if ($receiver instanceof Receiver) {
            $this->_receiver = $receiver;
        } else {
            $this->_receiver = new Receiver($receiver);
        }
    }

    /**
     * @return Sender
     */
    public function getSender(): Sender
    {
        return $this->_sender;
    }

    /**
     * @param array|Sender $sender
     */
    public function setSender($sender): void
    {
        if ($sender instanceof Sender) {
            $this->_sender = $sender;
        } else {
            $this->_sender = new Sender($sender);
        }
    }

    /**
     * @return Content[]
     */
    public function getContents(): array
    {
        return $this->_contents;
    }

    /**
     * @param Content[] $contents
     */
    public function setContents(array $contents): void
    {
        $this->_contents = [];
        foreach ($contents as $content) {
            $this->addContent($content);
        }
    }

    /**
     * @return float
     */
    public function getDesi(): float
    {
        if (!$this->_desi && count($this->_contents) > 0) {
            $desiTotal = 0;

            foreach ($this->_contents as $content) {

                $desi = (($content->height * $content->length * $content->width) / 3000) * $content->quantity;

                $weight = $content->weight * $content->quantity;

                $desiTotal += max($weight, $desi);
            }

            return $desiTotal;
        }

        return $this->_desi;
    }

    /**
     * @param int|float $desi
     */
    public function setDesi($desi): void
    {
        $this->_desi = $desi;
    }

    /**
     * @return int
     */
    public function getTotal_Parcels(): int
    {
        if (!$this->_total_parcels && count($this->_contents) > 0) {
            return (int)array_sum(ArrayHelper::getColumn($this->_contents, 'quantity'));
        }
        return $this->_total_parcels;
    }

    /**
     * @param int $total_parcels
     */
    public function setTotal_Parcels(int $total_parcels): void
    {
        $this->_total_parcels = $total_parcels;
    }

    /**
     * @param array|Content $content
     */
    public function addContent($content): void
    {
        if ($content instanceof Content) {
            $this->_contents[] = $content;
        } elseif (is_array($content)) {
            $this->_contents[] = new Content($content);
        }
    }
}