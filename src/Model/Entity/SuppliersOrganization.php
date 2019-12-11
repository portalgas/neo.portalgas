<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * KSuppliersOrganization Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $supplier_id
 * @property string $name
 * @property int $category_supplier_id
 * @property string|null $frequenza
 * @property string $owner_articles
 * @property int $owner_organization_id
 * @property int $owner_supplier_organization_id
 * @property string $can_view_orders
 * @property string $can_view_orders_users
 * @property string $can_promotions
 * @property string $mail_order_open
 * @property string $mail_order_close
 * @property string $stato
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\Supplier $supplier
 * @property \App\Model\Entity\CategorySupplier $category_supplier
 * @property \App\Model\Entity\OwnerOrganization $owner_organization
 * @property \App\Model\Entity\OwnerSupplierOrganization $owner_supplier_organization
 */
class SuppliersOrganization extends Entity
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
        'supplier_id' => true,
        'name' => true,
        'category_supplier_id' => true,
        'frequenza' => true,
        'owner_articles' => true,
        'owner_organization_id' => true,
        'owner_supplier_organization_id' => true,
        'can_view_orders' => true,
        'can_view_orders_users' => true,
        'can_promotions' => true,
        'mail_order_open' => true,
        'mail_order_close' => true,
        'stato' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'supplier' => true,
        'category_supplier' => true,
        'owner_organization' => true,
        'owner_supplier_organization' => true
    ];
}
