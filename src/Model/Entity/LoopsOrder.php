<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * LoopsOrder Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $loops_delivery_id
 * @property int $supplier_organization_id
 * @property int $gg_data_inizio
 * @property int $gg_data_fine
 * @property int $user_id
 * @property int $order_id
 * @property string|null $flag_send_mail
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\LoopsDelivery $loops_delivery
 * @property \App\Model\Entity\SupplierOrganization $supplier_organization
 * @property \App\Model\Entity\User $user
 */
class LoopsOrder extends Entity
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
        'loops_delivery_id' => true,
        'supplier_organization_id' => true,
        'gg_data_inizio' => true,
        'gg_data_fine' => true,
        'user_id' => true,
        'order_id' => true,
        'flag_send_mail' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'loops_delivery' => true,
        'supplier_organization' => true,
        'user' => true,
        'order' => true,
    ];
}
