<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PriceType Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $order_id
 * @property string $code
 * @property string $name
 * @property string|null $descri
 * @property string $type
 * @property float $value
 * @property bool|null $is_system
 * @property bool|null $is_active
 * @property int $sort
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\Order $order
 */
class PriceType extends Entity
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
        'order_id' => true,
        'code' => true,
        'name' => true,
        'descri' => true,
        'type' => true,
        'value' => true,
        'is_system' => true,
        'is_active' => true,
        'sort' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'order' => true,
    ];
}
