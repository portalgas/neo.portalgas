<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DesSuppliersReferent Entity
 *
 * @property int $des_id
 * @property int $des_supplier_id
 * @property int $organization_id
 * @property int $user_id
 * @property int $group_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\De $de
 * @property \App\Model\Entity\DesSupplier $des_supplier
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Group $group
 */
class DesSuppliersReferent extends Entity
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
        'created' => true,
        'modified' => true,
        'de' => true,
        'des_supplier' => true,
        'organization' => true,
        'user' => true,
        'group' => true,
    ];
}
