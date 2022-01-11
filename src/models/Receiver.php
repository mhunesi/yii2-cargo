<?php
namespace mhunesi\cargo\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @property Address $address
 */
class Receiver extends Model
{
    /**
     * @var int|string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $contactName;

    /**
     * Email
     * @var string
     */
    public $email;

    /**
     * Phone
     * @var string
     */
    public $phone;

    /**
     * Address
     * @var Address;
     */
    private $_address;

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->_address;
    }

    /**
     * @param array|Address $address
     */
    public function setAddress($address): void
    {
        if($address instanceof Address){
            $this->_address = $address;
        } elseif (is_array($address)) {
            $this->_address = new Address($address);
        }
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return ArrayHelper::merge(parent::fields(), [
            'address' => function () {
                return $this->getAddress();
            },
        ]);
    }
}