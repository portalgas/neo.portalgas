<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Storeroom Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int|null $delivery_id
 * @property int $user_id
 * @property int $article_id
 * @property int $article_organization_id
 * @property string $name
 * @property int $qta
 * @property float $prezzo
 * @property string $stato
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\Delivery $delivery
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Article $article
 * @property \App\Model\Entity\ArticleOrganization $article_organization
 */
class Storeroom extends Entity
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
        'user_id' => true,
        'article_id' => true,
        'article_organization_id' => true,
        'name' => true,
        'qta' => true,
        'prezzo' => true,
        'stato' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'delivery' => true,
        'user' => true,
        'article' => true,
        'article_organization' => true,
    ];
}
