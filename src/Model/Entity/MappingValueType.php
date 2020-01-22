<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MappingValueType Entity
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $match
 * @property string|null $factory_force_value
 * @property bool $is_force_value
 * @property bool $is_system
 * @property bool $is_active
 * @property int $sort
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Mapping[] $mappings
 */
class MappingValueType extends Entity
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
        'code' => true,
        'name' => true,
        'match' => true,
        'factory_force_value' => true,
        'is_force_value' => true,
        'is_system' => true,
        'is_active' => true,
        'sort' => true,
        'created' => true,
        'modified' => true,
        'mappings' => true,
    ];
}
