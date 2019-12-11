<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * KArticlesArticlesType Entity
 *
 * @property int $organization_id
 * @property int $article_id
 * @property int $article_type_id
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\Article $article
 * @property \App\Model\Entity\ArticleType $article_type
 */
class ArticlesArticlesType extends Entity
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
        'article_id' => true,
        'article_type_id' => true,
        'organization' => true,
        'article' => true,
        'article_type' => true
    ];
}
