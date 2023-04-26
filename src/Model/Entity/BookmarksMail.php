<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * BookmarksMail Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $user_id
 * @property int $supplier_organization_id
 * @property string $order_open
 * @property string $order_close
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\SupplierOrganization $supplier_organization
 */
class BookmarksMail extends Entity
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
        'supplier_organization_id' => true,
        'order_open' => true,
        'order_close' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'user' => true,
        'supplier_organization' => true,
    ];
}
