<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * BackupOrdersDesOrdersOrganization Entity
 *
 * @property int $id
 * @property int $des_id
 * @property int $des_order_id
 * @property int $organization_id
 * @property int $order_id
 * @property string $luogo
 * @property \Cake\I18n\FrozenDate $data
 * @property \Cake\I18n\FrozenTime $orario
 * @property string $contatto_nominativo
 * @property string $contatto_telefono
 * @property string $contatto_mail
 * @property string $nota
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\De $de
 * @property \App\Model\Entity\DesOrder $des_order
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\Order $order
 */
class BackupOrdersDesOrdersOrganization extends Entity
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
        'order_id' => true,
        'luogo' => true,
        'data' => true,
        'orario' => true,
        'contatto_nominativo' => true,
        'contatto_telefono' => true,
        'contatto_mail' => true,
        'nota' => true,
        'created' => true,
        'modified' => true,
        'de' => true,
        'des_order' => true,
        'organization' => true,
        'order' => true,
    ];
}
