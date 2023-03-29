<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Movement Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $movement_type_id
 * @property int|null $user_id
 * @property int|null $supplier_organization_id
 * @property int $year
 * @property string $name
 * @property string|null $descri
 * @property float $importo
 * @property \Cake\I18n\FrozenDate $date
 * @property string|null $type
 * @property bool $is_system
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\MovementType $movement_type
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\SupplierOrganization $supplier_organization
 */
class Movement extends Entity
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
        'movement_type_id' => true,
        'user_id' => true,
        'supplier_organization_id' => true,
        'year' => true,
        'name' => true,
        'descri' => true,
        'importo' => true,
        'date' => true,
        'type' => true,
        'is_system' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'movement_type' => true,
        'user' => true,
        'supplier_organization' => true,
    ];
}
