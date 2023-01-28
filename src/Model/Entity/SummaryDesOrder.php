<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SummaryDesOrder Entity
 *
 * @property int $id
 * @property int $des_id
 * @property int $des_order_id
 * @property int $organization_id
 * @property float $importo_orig
 * @property float $importo
 * @property float $importo_pagato
 * @property string $modalita
 * @property string $nota
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\De $de
 * @property \App\Model\Entity\DesOrder $des_order
 * @property \App\Model\Entity\Organization $organization
 */
class SummaryDesOrder extends Entity
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
        'des_order_id' => true,
        'organization_id' => true,
        'importo_orig' => true,
        'importo' => true,
        'importo_pagato' => true,
        'modalita' => true,
        'nota' => true,
        'created' => true,
        'modified' => true,
        'de' => true,
        'des_order' => true,
        'organization' => true,
    ];
}
