<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * StatCart Entity
 *
 * @property int $organization_id
 * @property int $user_id
 * @property int $article_organization_id
 * @property int $article_id
 * @property int $stat_order_id
 * @property int $qta
 * @property float $importo
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\ArticleOrganization $article_organization
 * @property \App\Model\Entity\Article $article
 * @property \App\Model\Entity\StatOrder $stat_order
 */
class StatCart extends Entity
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
        'article_organization_id' => true,
        'qta' => true,
        'importo' => true,
        'organization' => true,
        'user' => true,
        'article_organization' => true,
        'article' => true,
        'stat_order' => true,
    ];
}
