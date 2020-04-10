<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProdGasPromotion Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property string|null $name
 * @property string|null $img1
 * @property \Cake\I18n\FrozenDate $data_inizio
 * @property \Cake\I18n\FrozenDate $data_fine
 * @property float $importo_originale
 * @property float $importo_scontato
 * @property string|null $nota
 * @property string $contact_name
 * @property string $contact_mail
 * @property string $contact_phone
 * @property string $state_code
 * @property string $stato
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 */
class ProdGasPromotion extends Entity
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
        'name' => true,
        'img1' => true,
        'data_inizio' => true,
        'data_fine' => true,
        'importo_originale' => true,
        'importo_scontato' => true,
        'nota' => true,
        'contact_name' => true,
        'contact_mail' => true,
        'contact_phone' => true,
        'state_code' => true,
        'stato' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
    ];
}
