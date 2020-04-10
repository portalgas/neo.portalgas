<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DesOrder Entity
 *
 * @property int $id
 * @property int $des_id
 * @property int $des_supplier_id
 * @property string $luogo
 * @property string $nota
 * @property string $nota_evidenza
 * @property \Cake\I18n\FrozenDate $data_fine_max
 * @property string $hasTrasport
 * @property float $trasport
 * @property string $hasCostMore
 * @property float $cost_more
 * @property string $hasCostLess
 * @property float $cost_less
 * @property string $state_code
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $organization_id
 * @property int $order_id
 *
 * @property \App\Model\Entity\De $de
 * @property \App\Model\Entity\DesSupplier $des_supplier
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\Order $order
 */
class DesOrder extends Entity
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
        'des_id' => true,
        'des_supplier_id' => true,
        'luogo' => true,
        'nota' => true,
        'nota_evidenza' => true,
        'data_fine_max' => true,
        'hasTrasport' => true,
        'trasport' => true,
        'hasCostMore' => true,
        'cost_more' => true,
        'hasCostLess' => true,
        'cost_less' => true,
        'state_code' => true,
        'created' => true,
        'modified' => true,
        'organization_id' => true,
        'order_id' => true,
        'de' => true,
        'des_supplier' => true,
        'organization' => true,
        'order' => true,
    ];
}
