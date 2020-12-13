<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SummaryOrderCostLess Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $user_id
 * @property int $order_id
 * @property float $importo
 * @property int $peso
 * @property float $importo_cost_less
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Order $order
 */
class SummaryOrderCostLess extends Entity
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
        'user_id' => true,
        'order_id' => true,
        'importo' => true,
        'peso' => true,
        'importo_cost_less' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'user' => true,
        'order' => true,
    ];
}
