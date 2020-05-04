<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * BackupArticlesOrder Entity
 *
 * @property int $organization_id
 * @property int $order_id
 * @property int $article_organization_id
 * @property int $article_id
 * @property int $qta_cart
 * @property string|null $name
 * @property float $prezzo
 * @property int $pezzi_confezione
 * @property int $qta_minima
 * @property int $qta_massima
 * @property int $qta_minima_order
 * @property int $qta_massima_order
 * @property int $qta_multipli
 * @property int $alert_to_qta
 * @property string $send_mail
 * @property string $flag_bookmarks
 * @property string $stato
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\Order $order
 * @property \App\Model\Entity\ArticleOrganization $article_organization
 * @property \App\Model\Entity\Article $article
 */
class BackupArticlesOrder extends Entity
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
        'order_id' => true,
        'article_organization_id' => true,
        'article_id' => true,
        'qta_cart' => true,
        'name' => true,
        'prezzo' => true,
        'pezzi_confezione' => true,
        'qta_minima' => true,
        'qta_massima' => true,
        'qta_minima_order' => true,
        'qta_massima_order' => true,
        'qta_multipli' => true,
        'alert_to_qta' => true,
        'send_mail' => true,
        'flag_bookmarks' => true,
        'stato' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'order' => true,
        'article_organization' => true,
        'article' => true,
    ];
}
