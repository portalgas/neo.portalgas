<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProdGasPromotionsOrganizationsDelivery Entity
 *
 * @property int $id
 * @property int $supplier_id
 * @property int $prod_gas_promotion_id
 * @property int $organization_id
 * @property int $delivery_id
 * @property string $isConfirmed
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Supplier $supplier
 * @property \App\Model\Entity\ProdGasPromotion $prod_gas_promotion
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\Delivery $delivery
 */
class ProdGasPromotionsOrganizationsDelivery extends Entity
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
        'supplier_id' => true,
        'prod_gas_promotion_id' => true,
        'organization_id' => true,
        'delivery_id' => true,
        'isConfirmed' => true,
        'created' => true,
        'modified' => true,
        'supplier' => true,
        'prod_gas_promotion' => true,
        'organization' => true,
        'delivery' => true,
    ];
}
