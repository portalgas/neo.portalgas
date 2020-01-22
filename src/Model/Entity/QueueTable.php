<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * QueueTable Entity
 *
 * @property int $id
 * @property int $queue_id
 * @property int $table_id
 * @property int $sort
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Queue $queue
 * @property \App\Model\Entity\Table $table
 */
class QueueTable extends Entity
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
        'table_id' => true,
        'before_save' => true,
        'after_save' => true,
        'sort' => true,
        'created' => true,
        'modified' => true,
        'queue' => true,
        'table' => true
    ];
}
