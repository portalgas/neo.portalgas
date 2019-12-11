<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * KOrganization Entity
 *
 * @property int $id
 * @property string $name
 * @property string $descrizione
 * @property string $indirizzo
 * @property string $localita
 * @property string $cap
 * @property string $provincia
 * @property string|null $telefono
 * @property string|null $telefono2
 * @property string|null $mail
 * @property string|null $www
 * @property string $www2
 * @property string $sede_logistica_1
 * @property string $sede_logistica_2
 * @property string $sede_logistica_3
 * @property string $sede_logistica_4
 * @property string|null $cf
 * @property string|null $piva
 * @property string|null $banca
 * @property string $banca_iban
 * @property string $lat
 * @property string $lng
 * @property string $img1
 * @property int $template_id
 * @property int $j_group_registred
 * @property int $j_page_category_id
 * @property string $j_seo
 * @property string|null $gcalendar_id
 * @property string $type
 * @property string $paramsConfig
 * @property string $paramsFields
 * @property string $paramsPay
 * @property string $hasMsg
 * @property string|null $msgText
 * @property string $stato
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Template $template
 * @property \App\Model\Entity\JPageCategory $j_page_category
 * @property \App\Model\Entity\Gcalendar $gcalendar
 */
class OwnerOrganization extends Entity
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
        'template' => true    ];
}
