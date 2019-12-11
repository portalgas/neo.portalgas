<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * KArticle Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $supplier_organization_id
 * @property int $category_article_id
 * @property string|null $name
 * @property string|null $codice
 * @property string|null $nota
 * @property string|null $ingredienti
 * @property float $prezzo
 * @property float $qta
 * @property string $um
 * @property string $um_riferimento
 * @property int $pezzi_confezione
 * @property int $qta_minima
 * @property int $qta_massima
 * @property int $qta_minima_order
 * @property int $qta_massima_order
 * @property int $qta_multipli
 * @property int $alert_to_qta
 * @property string $bio
 * @property string|null $img1
 * @property string $stato
 * @property string $flag_presente_articlesorders
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\SupplierOrganization $supplier_organization
 * @property \App\Model\Entity\CategoryArticle $category_article
 */
class Article extends Entity
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
        'supplier_organization_id' => true,
        'category_article_id' => true,
        'name' => true,
        'codice' => true,
        'nota' => true,
        'ingredienti' => true,
        'prezzo' => true,
        'qta' => true,
        'um' => true,
        'um_riferimento' => true,
        'pezzi_confezione' => true,
        'qta_minima' => true,
        'qta_massima' => true,
        'qta_minima_order' => true,
        'qta_massima_order' => true,
        'qta_multipli' => true,
        'alert_to_qta' => true,
        'bio' => true,
        'img1' => true,
        'stato' => true,
        'flag_presente_articlesorders' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'supplier_organization' => true,
        'categories_article' => true
    ];
}
