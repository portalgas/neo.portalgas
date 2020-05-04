<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StatOrder Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $supplier_organization_id
 * @property string|null $supplier_organization_name
 * @property string|null $supplier_img1
 * @property int $stat_delivery_id
 * @property int $stat_delivery_year
 * @property \Cake\I18n\FrozenDate|null $data_inizio
 * @property \Cake\I18n\FrozenDate|null $data_fine
 * @property float $importo
 * @property float|null $tesoriere_fattura_importo
 * @property string|null $tesoriere_doc1
 * @property \Cake\I18n\FrozenDate|null $tesoriere_data_pay
 * @property float|null $tesoriere_importo_pay
 * @property string $request_payment_num
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\SupplierOrganization $supplier_organization
 * @property \App\Model\Entity\StatDelivery $stat_delivery
 */
class StatOrder extends Entity
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
        'supplier_organization_id' => true,
        'supplier_organization_name' => true,
        'supplier_img1' => true,
        'stat_delivery_id' => true,
        'stat_delivery_year' => true,
        'data_inizio' => true,
        'data_fine' => true,
        'importo' => true,
        'tesoriere_fattura_importo' => true,
        'tesoriere_doc1' => true,
        'tesoriere_data_pay' => true,
        'tesoriere_importo_pay' => true,
        'request_payment_num' => true,
        'organization' => true,
        'supplier_organization' => true,
        'stat_delivery' => true,
    ];
}
