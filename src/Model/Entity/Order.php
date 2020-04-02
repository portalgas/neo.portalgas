<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Order Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $supplier_organization_id
 * @property string $owner_articles
 * @property int $owner_organization_id
 * @property int $owner_supplier_organization_id
 * @property int $delivery_id
 * @property int $prod_gas_promotion_id
 * @property int $des_order_id
 * @property \Cake\I18n\FrozenDate $data_inizio
 * @property \Cake\I18n\FrozenDate $data_fine
 * @property \Cake\I18n\FrozenDate $data_fine_validation
 * @property \Cake\I18n\FrozenDate $data_incoming_order
 * @property \Cake\I18n\FrozenDate $data_state_code_close
 * @property string|null $nota
 * @property string $hasTrasport
 * @property string|null $trasport_type
 * @property float $trasport
 * @property string $hasCostMore
 * @property string|null $cost_more_type
 * @property float $cost_more
 * @property string $hasCostLess
 * @property string|null $cost_less_type
 * @property float $cost_less
 * @property string|null $typeGest
 * @property string $state_code
 * @property string $mail_open_send
 * @property \Cake\I18n\FrozenTime $mail_open_data
 * @property \Cake\I18n\FrozenTime $mail_close_data
 * @property string $mail_open_testo
 * @property string $type_draw
 * @property float $tot_importo
 * @property int $qta_massima
 * @property string|null $qta_massima_um
 * @property string $send_mail_qta_massima
 * @property float $importo_massimo
 * @property string $send_mail_importo_massimo
 * @property string|null $tesoriere_nota
 * @property float $tesoriere_fattura_importo
 * @property string|null $tesoriere_doc1
 * @property \Cake\I18n\FrozenDate $tesoriere_data_pay
 * @property float $tesoriere_importo_pay
 * @property string $tesoriere_stato_pay
 * @property string $inviato_al_tesoriere_da
 * @property string $isVisibleFrontEnd
 * @property string $isVisibleBackOffice
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\SupplierOrganization $supplier_organization
 * @property \App\Model\Entity\OwnerOrganization $owner_organization
 * @property \App\Model\Entity\OwnerSupplierOrganization $owner_supplier_organization
 * @property \App\Model\Entity\Delivery $delivery
 * @property \App\Model\Entity\ProdGasPromotion $prod_gas_promotion
 * @property \App\Model\Entity\DesOrder $des_order
 */
class Order extends Entity
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
        'owner_articles' => true,
        'owner_organization_id' => true,
        'owner_supplier_organization_id' => true,
        'delivery_id' => true,
        'prod_gas_promotion_id' => true,
        'des_order_id' => true,
        'data_inizio' => true,
        'data_fine' => true,
        'data_fine_validation' => true,
        'data_incoming_order' => true,
        'data_state_code_close' => true,
        'nota' => true,
        'hasTrasport' => true,
        'trasport_type' => true,
        'trasport' => true,
        'hasCostMore' => true,
        'cost_more_type' => true,
        'cost_more' => true,
        'hasCostLess' => true,
        'cost_less_type' => true,
        'cost_less' => true,
        'typeGest' => true,
        'state_code' => true,
        'mail_open_send' => true,
        'mail_open_data' => true,
        'mail_close_data' => true,
        'mail_open_testo' => true,
        'type_draw' => true,
        'tot_importo' => true,
        'qta_massima' => true,
        'qta_massima_um' => true,
        'send_mail_qta_massima' => true,
        'importo_massimo' => true,
        'send_mail_importo_massimo' => true,
        'tesoriere_nota' => true,
        'tesoriere_fattura_importo' => true,
        'tesoriere_doc1' => true,
        'tesoriere_data_pay' => true,
        'tesoriere_importo_pay' => true,
        'tesoriere_stato_pay' => true,
        'inviato_al_tesoriere_da' => true,
        'isVisibleFrontEnd' => true,
        'isVisibleBackOffice' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'supplier_organization' => true,
        'owner_organization' => true,
        'owner_supplier_organization' => true,
        'delivery' => true,
        'prod_gas_promotion' => true,
        'des_order' => true,
    ];
}
