<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * BackupOrdersCart Entity
 *
 * @property int $organization_id
 * @property int $user_id
 * @property int $order_id
 * @property int $article_organization_id
 * @property int $article_id
 * @property int $qta
 * @property string $deleteToReferent
 * @property int $qta_forzato
 * @property float $importo_forzato
 * @property string|null $nota
 * @property string $inStoreroom
 * @property string $stato
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime $date
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Order $order
 * @property \App\Model\Entity\ArticleOrganization $article_organization
 * @property \App\Model\Entity\Article $article
 */
class BackupOrdersCart extends Entity
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
        'qta' => true,
        'deleteToReferent' => true,
        'qta_forzato' => true,
        'importo_forzato' => true,
        'nota' => true,
        'inStoreroom' => true,
        'stato' => true,
        'created' => true,
        'date' => true,
        'organization' => true,
        'user' => true,
        'order' => true,
        'article_organization' => true,
        'article' => true,
    ];
}
