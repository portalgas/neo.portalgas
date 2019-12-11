<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * QueueLog Entity
 *
 * @property int $id
 * @property int $queue_id
 * @property string $uuid
 * @property string|null $message
 * @property string|null $log
 * @property string $level
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Queue $queue
 */
class QueueLog extends Entity
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
        'uuid' => true,
        'message' => true,
        'log' => true,
        'level' => true,
        'created' => true,
        'modified' => true,
        'queue' => true
    ];
}
