<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CashesHistory Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $cash_id
 * @property int $user_id
 * @property string|null $nota
 * @property float $importo
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\Cash $cash
 * @property \App\Model\Entity\User $user
 */
class CashesHistory extends Entity
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
        'cash_id' => true,
        'user_id' => true,
        'nota' => true,
        'importo' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'cash' => true,
        'user' => true,
    ];
}
