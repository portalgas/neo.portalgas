<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Organization extends Entity
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
        'parent_id' => true,
        'descrizione' => true,
        'indirizzo' => true,
        'localita' => true,
        'cap' => true,
        'provincia' => true,
        'telefono' => true,
        'telefono2' => true,
        'mail' => true,
        'www' => true,
        'www2' => true,
        'sede_logistica_1' => true,
        'sede_logistica_2' => true,
        'sede_logistica_3' => true,
        'sede_logistica_4' => true,
        'cf' => true,
        'piva' => true,
        'banca' => true,
        'banca_iban' => true,
        'lat' => true,
        'lng' => true,
        'img1' => true,
        'template_id' => true,
        'j_group_registred' => true,
        'j_page_category_id' => true,
        'j_seo' => true,
        'gcalendar_id' => true,
        'type' => true,
        'paramsConfig' => true,
        'paramsFields' => true,
        'paramsPay' => true,
        'hasMsg' => true,
        'msgText' => true,
        'stato' => true,
        'created' => true,
        'modified' => true,
        'template' => true
    ];
}
