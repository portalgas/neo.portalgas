<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProdGasPromotionsOrganization Entity
 *
 * @property int $id
 * @property int $prod_gas_promotion_id
 * @property int $organization_id
 * @property int $order_id
 * @property string $hasTrasport
 * @property float $trasport
 * @property string $hasCostMore
 * @property float $cost_more
 * @property string|null $nota_supplier
 * @property string $nota_user
 * @property int $user_id
 * @property string $state_code
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\ProdGasPromotion $prod_gas_promotion
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\Order $order
 * @property \App\Model\Entity\User $user
 */
class ProdGasPromotionsOrganization extends Entity
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
        'prod_gas_promotion_id' => true,
        'organization_id' => true,
        'order_id' => true,
        'hasTrasport' => true,
        'trasport' => true,
        'hasCostMore' => true,
        'cost_more' => true,
        'nota_supplier' => true,
        'nota_user' => true,
        'user_id' => true,
        'state_code' => true,
        'created' => true,
        'modified' => true,
        'prod_gas_promotion' => true,
        'organization' => true,
        'order' => true,
        'user' => true,
    ];
}
