<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * OrdersAction Entity
 *
 * @property int $id
 * @property string $controller
 * @property string $action
 * @property string|null $state_code_next
 * @property string $permission
 * @property string $permission_or
 * @property string $query_string
 * @property string|null $flag_menu
 * @property string $label
 * @property string $label_more
 * @property string $css_class
 * @property string $img
 */
class OrdersAction extends Entity
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
        'controller' => true,
        'action' => true,
        'neo_url' => true,
        'state_code_next' => true,
        'permission' => true,
        'permission_or' => true,
        'permissions' => true,
        'query_string' => true,
        'flag_menu' => true,
        'label' => true,
        'label_more' => true,
        'css_class' => true,
        'img' => true,
    ];
}
