<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Scope Entity
 *
 * @property int $id
 * @property string $name
 * @property string $namespace
 * @property bool $is_system
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Table[] $tables
 */
class Scope extends Entity
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
        'namespace' => true,
        'db_datasource' => true,
        'is_system' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'tables' => true
    ];
}
