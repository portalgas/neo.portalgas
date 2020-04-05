<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SummaryOrder Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $user_id
 * @property int $delivery_id
 * @property int $order_id
 * @property float $importo
 * @property float $importo_pagato
 * @property string|null $saldato_a
 * @property string $modalita
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Delivery $delivery
 * @property \App\Model\Entity\Order $order
 */
class SummaryOrder extends Entity
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
        'delivery_id' => true,
        'importo' => true,
        'importo_pagato' => true,
        'saldato_a' => true,
        'modalita' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'user' => true,
        'delivery' => true,
        'order' => true,
    ];
}
