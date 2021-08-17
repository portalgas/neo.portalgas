<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * KSuppliersVote Entity
 *
 * @property int $id
 * @property int $supplier_id
 * @property int $organization_id
 * @property int $user_id
 * @property string|null $nota
 * @property int $voto
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Supplier $supplier
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\User $user
 */
class SuppliersVote extends Entity
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
        'organization_id' => true,
        'user_id' => true,
        'nota' => true,
        'voto' => true,
        'created' => true,
        'modified' => true,
        'supplier' => true,
        'organization' => true,
        'user' => true,
    ];
}
