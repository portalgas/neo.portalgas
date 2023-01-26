<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RequestPaymentsOrder Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $delivery_id
 * @property int $order_id
 * @property int $request_payment_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\Delivery $delivery
 * @property \App\Model\Entity\Order $order
 * @property \App\Model\Entity\RequestPayment $request_payment
 */
class RequestPaymentsOrder extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'organization_id' => true,
        'delivery_id' => true,
        'order_id' => true,
        'request_payment_id' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'delivery' => true,
        'order' => true,
        'request_payment' => true,
    ];
}
