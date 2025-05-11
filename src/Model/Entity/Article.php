<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

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
        'id' => true,
        'organization_id' => true,
        'supplier_organization_id' => true,
        'category_article_id' => true,
        'parent_id' => true,
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
