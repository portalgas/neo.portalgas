<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SummaryPayment Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $user_id
 * @property int $request_payment_id
 * @property float $importo_dovuto
 * @property float $importo_richiesto
 * @property float $importo_pagato
 * @property string $modalita
 * @property string $stato
 * @property \Cake\I18n\FrozenDate|null $data_send
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\RequestPayment $request_payment
 */
class SummaryPayment extends Entity
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
        'user_id' => true,
        'request_payment_id' => true,
        'importo_dovuto' => true,
        'importo_richiesto' => true,
        'importo_pagato' => true,
        'modalita' => true,
        'stato' => true,
        'data_send' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'user' => true,
        'request_payment' => true,
    ];
}
