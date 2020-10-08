<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * KProdGasArticlesPromotion Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $prod_gas_promotion_id
 * @property int $article_id
 * @property int $qta
 * @property float $prezzo_unita
 * @property float $importo
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\ProdGasPromotion $prod_gas_promotion
 * @property \App\Model\Entity\Article $article
 */
class ProdGasArticlesPromotion extends Entity
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
        'prod_gas_promotion_id' => true,
        'article_id' => true,
        'qta' => true,
        'prezzo_unita' => true,
        'importo' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'prod_gas_promotion' => true,
        'article' => true,
    ];
}
