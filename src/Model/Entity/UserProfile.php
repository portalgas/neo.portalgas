<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * JUserProfile Entity
 *
 * @property int $user_id
 * @property string $profile_key
 * @property string $profile_value
 * @property int $ordering
 *
 * @property \App\Model\Entity\User $user
 */
class JUserProfile extends Entity
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
        'user_id' => true,
        'profile_key' => true,
        'profile_value' => true,
        'ordering' => true,
        'user' => true
    ];
}
