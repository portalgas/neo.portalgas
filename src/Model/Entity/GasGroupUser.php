<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GasGroupUser Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $user_id
 * @property int $gas_group_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\GasGroup $gas_group
 */
class GasGroupUser extends Entity
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
        'gas_group_id' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'user' => true,
        'gas_group' => true,
    ];
}
