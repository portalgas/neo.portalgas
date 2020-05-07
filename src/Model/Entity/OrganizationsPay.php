<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * OrganizationsPay Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property string $year
 * @property \Cake\I18n\FrozenDate $data_pay
 * @property string $beneficiario_pay
 * @property int $tot_users
 * @property int $tot_orders
 * @property int $tot_suppliers_organizations
 * @property int $tot_articles
 * @property float $importo
 * @property float $import_additional_cost
 * @property string $type_pay
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Organization $organization
 */
class OrganizationsPay extends Entity
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
        'year' => true,
        'data_pay' => true,
        'beneficiario_pay' => true,
        'tot_users' => true,
        'tot_orders' => true,
        'tot_suppliers_organizations' => true,
        'tot_articles' => true,
        'importo' => true,
        'import_additional_cost' => true,
        'type_pay' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
    ];
}
