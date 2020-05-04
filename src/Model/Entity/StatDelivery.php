<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StatDelivery Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property string $luogo
 * @property \Cake\I18n\FrozenDate|null $data
 *
 * @property \App\Model\Entity\Organization $organization
 */
class StatDelivery extends Entity
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
        'data' => true,
        'organization' => true,
    ];
}
