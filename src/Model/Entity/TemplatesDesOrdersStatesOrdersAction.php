<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TemplatesDesOrdersStatesOrdersAction Entity
 *
 * @property int $template_id
 * @property int $group_id
 * @property string $state_code
 * @property int $des_order_action_id
 * @property int $sort
 *
 * @property \App\Model\Entity\Template $template
 * @property \App\Model\Entity\Group $group
 * @property \App\Model\Entity\DesOrderAction $des_order_action
 */
class TemplatesDesOrdersStatesOrdersAction extends Entity
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
        'sort' => true,
        'template' => true,
        'group' => true,
        'des_order_action' => true,
    ];
}
