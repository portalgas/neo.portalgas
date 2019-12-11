<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CategoriesArticle Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int|null $parent_id
 * @property int|null $lft
 * @property int|null $rght
 * @property string|null $name
 * @property string|null $description
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\ParentCategoriesArticle $parent_k_categories_article
 * @property \App\Model\Entity\ChildCategoriesArticle[] $child_k_categories_articles
 */
class CategoriesArticle extends Entity
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
        'parent_id' => true,
        'lft' => true,
        'rght' => true,
        'name' => true,
        'description' => true,
        'organization' => true,
        'article' => true,
        'parent_k_categories_article' => true,
        'child_k_categories_articles' => true
    ];
}
