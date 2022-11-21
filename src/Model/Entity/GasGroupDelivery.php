<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GasGroupDelivery Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $delivery_id
 * @property string $luogo
 * @property \Cake\I18n\FrozenDate $data
 * @property \Cake\I18n\FrozenTime $orario_da
 * @property \Cake\I18n\FrozenTime $orario_a
 * @property string|null $nota
 * @property string $nota_evidenza
 * @property string $stato_elaborazione
 * @property string $isVisibleFrontEnd
 * @property string $isVisibleBackOffice
 * @property string $sys
 * @property string|null $gcalendar_event_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\Delivery $delivery
 * @property \App\Model\Entity\GcalendarEvent $gcalendar_event
 */
class GasGroupDelivery extends Entity
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
        'delivery_id' => true,
        'luogo' => true,
        'data' => true,
        'orario_da' => true,
        'orario_a' => true,
        'nota' => true,
        'nota_evidenza' => true,
        'stato_elaborazione' => true,
        'gcalendar_event_id' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'delivery' => true,
        'gcalendar_event' => true,
    ];
}
