<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Table Entity
 *
 * @property int $id
 * @property int $scope_id
 * @property string $name
 * @property string $table_name
 * @property string $entity
 * @property string|null $where_key
 * @property bool $is_system
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Scope $scope
 * @property \App\Model\Entity\QueueTable[] $queue_tables
 */
class Table extends Entity
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
        'scope_id' => true,
        'name' => true,
        'table_name' => true,
        'entity' => true,
        'where_key' => true,
        'is_system' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'scope' => true,
        'queue_tables' => true
    ];
}
