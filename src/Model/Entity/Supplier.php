<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * KSupplier Entity
 *
 * @property int $id
 * @property int $category_supplier_id
 * @property string|null $name
 * @property string|null $nome
 * @property string|null $cognome
 * @property string|null $descrizione
 * @property string|null $indirizzo
 * @property string|null $localita
 * @property string|null $cap
 * @property string|null $provincia
 * @property string $lat
 * @property string $lng
 * @property string|null $telefono
 * @property string|null $telefono2
 * @property string|null $fax
 * @property string|null $mail
 * @property string|null $www
 * @property string|null $nota
 * @property string|null $cf
 * @property string|null $piva
 * @property string|null $conto
 * @property int $j_content_id
 * @property string $img1
 * @property string $can_promotions
 * @property int $delivery_type_id
 * @property int $owner_organization_id
 * @property string $stato
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\CategorySupplier $category_supplier
 * @property \App\Model\Entity\JContent $j_content
 * @property \App\Model\Entity\DeliveryType $delivery_type
 * @property \App\Model\Entity\OwnerOrganization $owner_organization
 */
class Supplier extends Entity
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
        'category_supplier_id' => true,
        'name' => true,
        'nome' => true,
        'cognome' => true,
        'descrizione' => true,
        'indirizzo' => true,
        'localita' => true,
        'cap' => true,
        'provincia' => true,
        'lat' => true,
        'lng' => true,
        'telefono' => true,
        'telefono2' => true,
        'fax' => true,
        'mail' => true,
        'www' => true,
        'nota' => true,
        'cf' => true,
        'piva' => true,
        'conto' => true,
        'j_content_id' => true,
        'img1' => true,
        'can_promotions' => true,
        'delivery_type_id' => true,
        'owner_organization_id' => true,
        'stato' => true,
        'created' => true,
        'modified' => true,
        'category_supplier' => true,
        'j_content' => true,
        'delivery_type' => true,
        'owner_organization' => true
    ];
}
