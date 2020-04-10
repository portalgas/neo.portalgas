<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Template Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $descri
 * @property string $descri_order_cycle_life
 * @property string $payToDelivery
 * @property string $orderForceClose
 * @property string|null $orderUserPaid
 * @property string|null $orderSupplierPaid
 * @property int $ggArchiveStatics
 * @property string $hasCassiere
 * @property string $hasTesoriere
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class Template extends Entity
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
        'name' => true,
        'descri' => true,
        'descri_order_cycle_life' => true,
        'payToDelivery' => true,
        'orderForceClose' => true,
        'orderUserPaid' => true,
        'orderSupplierPaid' => true,
        'ggArchiveStatics' => true,
        'hasCassiere' => true,
        'hasTesoriere' => true,
        'created' => true,
        'modified' => true,
    ];
}
