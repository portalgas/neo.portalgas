<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MailSend Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $tot_users
 * @property string $file_sh
 * @property \Cake\I18n\FrozenDate $data
 * @property string $cron
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Organization $organization
 */
class MailSend extends Entity
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
        'tot_users' => true,
        'file_sh' => true,
        'data' => true,
        'cron' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
    ];
}
