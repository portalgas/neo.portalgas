<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Mapping Entity
 *
 * @property int $id
 * @property int $queue_id
 * @property string $name
 * @property string|null $descri
 * @property int $master_scope_id
 * @property int|null $master_table_id
 * @property string|null $master_column
 * @property string|null $master_xml_xpath
 * @property int|null $master_csv_num_col
 * @property int $slave_scope_id
 * @property int $slave_table_id
 * @property string $slave_column
 * @property int $mapping_type_id
 * @property int|null $queue_table_id
 * @property string|null $value
 * @property string|null $value_default
 * @property string|null $parameters
 * @property bool $is_required
 * @property bool $is_active
 * @property int|null $sort
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Queue $queue
 * @property \App\Model\Entity\MasterScope $master_scope
 * @property \App\Model\Entity\MasterTable $master_table
 * @property \App\Model\Entity\SlaveScope $slave_scope
 * @property \App\Model\Entity\SlaveTable $slave_table
 * @property \App\Model\Entity\MappingType $mapping_type
 * @property \App\Model\Entity\QueueTable $queue_table
 */
class Mapping extends Entity
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
        'queue_id' => true,
        'name' => true,
        'descri' => true,
        'master_scope_id' => true,
        'master_table_id' => true,
        'master_column' => true,
        'master_xml_xpath' => true,
        'master_csv_num_col' => true,
        'slave_scope_id' => true,
        'slave_table_id' => true,
        'slave_column' => true,
        'mapping_type_id' => true,
        'queue_table_id' => true,
        'value' => true,
        'value_default' => true,
        'mapping_value_type_id' => true,
        'parameters' => true,
        'is_required' => true,
        'is_active' => true,
        'sort' => true,
        'created' => true,
        'modified' => true,
        'queue' => true,
        'master_scope' => true,
        'master_table' => true,
        'slave_scope' => true,
        'slave_table' => true,
        'mapping_value_type' => true,
        'mapping_type' => true,
        'queue_table' => true
    ];
}
