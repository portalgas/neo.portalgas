<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MovementType Entity
 *
 * @property int $id
 * @property string $name
 * @property bool $is_active
 * @property bool $is_system
 * @property string|null $model
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Movement[] $movements
 */
class MovementType extends Entity
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
        'name' => true,
        'is_active' => true,
        'is_system' => true,
        'model' => true,
        'sort' => true,
        'created' => true,
        'modified' => true,
        'movements' => true,
    ];
}
