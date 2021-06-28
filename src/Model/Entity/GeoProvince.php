<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GeoProvince Entity
 *
 * @property int $id
 * @property int|null $geo_region_id
 * @property string $name
 * @property string|null $sigla
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\GeoRegion $geo_region
 */
class GeoProvince extends Entity
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
        'geo_region_id' => true,
        'name' => true,
        'sigla' => true,
        'created' => true,
        'modified' => true,
        'geo_region' => true,
    ];
}
