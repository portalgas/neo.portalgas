<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Market Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $img1
 * @property \Cake\I18n\FrozenDate $data_inizio
 * @property \Cake\I18n\FrozenDate $data_fine
 * @property string|null $nota
 * @property string $state_code
 * @property string $is_system
 * @property bool $is_active
 * @property int $sort
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\MarketArticle[] $market_articles
 */
class Market extends Entity
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
        'nota' => true,
        'state_code' => true,
        'is_system' => true,
        'is_active' => true,
        'sort' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'market_articles' => true,
    ];
}
