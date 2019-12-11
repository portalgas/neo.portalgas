<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * JUsergroup Entity
 *
 * @property int $id
 * @property int $parent_id
 * @property int $lft
 * @property int $rgt
 * @property string $title
 *
 * @property \App\Model\Entity\ParentJUsergroup $parent_j_usergroup
 * @property \App\Model\Entity\ChildJUsergroup[] $child_j_usergroups
 */
class Usergroup extends Entity
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
        'parent_id' => true,
        'lft' => true,
        'rgt' => true,
        'title' => true,
        'parent_j_usergroup' => true,
        'child_j_usergroups' => true
    ];
}
