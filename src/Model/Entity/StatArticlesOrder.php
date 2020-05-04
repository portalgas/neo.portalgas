<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StatArticlesOrder Entity
 *
 * @property int $organization_id
 * @property int $stat_order_id
 * @property int $article_organization_id
 * @property int $article_id
 * @property string|null $name
 * @property string|null $codice
 * @property float $prezzo
 * @property float $qta
 * @property string $um
 * @property string $um_riferimento
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\StatOrder $stat_order
 * @property \App\Model\Entity\ArticleOrganization $article_organization
 * @property \App\Model\Entity\Article $article
 */
class StatArticlesOrder extends Entity
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
        'name' => true,
        'codice' => true,
        'prezzo' => true,
        'qta' => true,
        'um' => true,
        'um_riferimento' => true,
        'organization' => true,
        'stat_order' => true,
        'article_organization' => true,
        'article' => true,
    ];
}
