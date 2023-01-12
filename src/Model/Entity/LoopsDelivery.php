<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * LoopsDelivery Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property string $luogo
 * @property \Cake\I18n\FrozenTime $orario_da
 * @property \Cake\I18n\FrozenTime $orario_a
 * @property string|null $nota
 * @property string $nota_evidenza
 * @property \Cake\I18n\FrozenDate $data_master
 * @property \Cake\I18n\FrozenDate $data_master_reale
 * @property \Cake\I18n\FrozenDate $data_copy
 * @property \Cake\I18n\FrozenDate $data_copy_reale
 * @property int $delivery_id
 * @property int $user_id
 * @property string|null $flag_send_mail
 * @property string|null $rules
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\User $user
 */
class LoopsDelivery extends Entity
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
        'luogo' => true,
        'orario_da' => true,
        'orario_a' => true,
        'nota' => true,
        'nota_evidenza' => true,
        'data_master' => true,
        'data_master_reale' => true,
        'data_copy' => true,
        'data_copy_reale' => true,
        'delivery_id' => true,
        'user_id' => true,
        'flag_send_mail' => true,
        'rules' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'user' => true,
        'delivery' => true,
    ];
}
