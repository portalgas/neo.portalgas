<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RequestPayment Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int|null $num
 * @property int $user_id
 * @property string $stato_elaborazione
 * @property \Cake\I18n\FrozenDate $stato_elaborazione_date
 * @property string|null $nota
 * @property \Cake\I18n\FrozenDate $data_send
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\User $user
 */
class RequestPayment extends Entity
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
        'num' => true,
        'user_id' => true,
        'stato_elaborazione' => true,
        'stato_elaborazione_date' => true,
        'nota' => true,
        'data_send' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'user' => true,
    ];
}
