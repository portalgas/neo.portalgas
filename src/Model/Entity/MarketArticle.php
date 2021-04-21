<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MarketArticle Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $market_id
 * @property int $article_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\Market $market
 * @property \App\Model\Entity\Article $article
 */
class MarketArticle extends Entity
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
        'market_id' => true,
        'article_id' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'market' => true,
        'article' => true,
    ];
}
