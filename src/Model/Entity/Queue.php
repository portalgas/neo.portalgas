<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Queue Entity
 *
 * @property int $id
 * @property int $queue_mapping_type_id
 * @property string $code
 * @property string $name
 * @property string $component
 * @property int $master_scope_id
 * @property int $slave_scope_id
 * @property string|null $descri
 * @property bool $is_system
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\QueueMappingType $queue_mapping_type
 * @property \App\Model\Entity\MasterScope $master_scope
 * @property \App\Model\Entity\SlaveScope $slave_scope
 * @property \App\Model\Entity\Mapping[] $mappings
 * @property \App\Model\Entity\QueueLog[] $queue_logs
 * @property \App\Model\Entity\QueueTable[] $queue_tables
 */
class Queue extends Entity
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
        'queue_mapping_type_id' => true,
        'code' => true,
        'name' => true,
        'component' => true,
        'master_scope_id' => true,
        'master_db_datasource' => true,
        'slave_scope_id' => true,
        'slave_db_datasource' => true,
        'descri' => true,
        'is_system' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'queue_mapping_type' => true,
        'master_scope' => true,
        'slave_scope' => true,
        'mappings' => true,
        'queue_logs' => true,
        'queue_tables' => true
    ];
}
