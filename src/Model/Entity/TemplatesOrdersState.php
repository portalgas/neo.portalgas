<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TemplatesOrdersState Entity
 *
 * @property int $template_id
 * @property string $state_code
 * @property int $group_id
 * @property string $action_controller
 * @property string $action_action
 * @property string $flag_menu
 * @property int $sort
 *
 * @property \App\Model\Entity\Template $template
 * @property \App\Model\Entity\Group $group
 */
class TemplatesOrdersState extends Entity
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
        'action_controller' => true,
        'action_action' => true,
        'flag_menu' => true,
        'sort' => true,
        'template' => true,
        'group' => true,
    ];
}
