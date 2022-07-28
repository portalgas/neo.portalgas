<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SocialmarketOrganization Entity
 *
 * @property int $id
 * @property int $supplier_organization_id
 * @property int $organization_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\SupplierOrganization $supplier_organization
 * @property \App\Model\Entity\Organization $organization
 */
class SocialmarketOrganization extends Entity
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
        'supplier_organization_id' => true,
        'organization_id' => true,
        'created' => true,
        'modified' => true,
        'supplier_organization' => true,
        'organization' => true,
    ];
}
