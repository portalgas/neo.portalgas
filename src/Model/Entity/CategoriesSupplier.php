<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CategoriesSupplier Entity
 *
 * @property int $id
 * @property int|null $parent_id
 * @property int|null $lft
 * @property int|null $rght
 * @property string|null $name
 * @property string|null $description
 * @property int $j_category_id
 *
 * @property \App\Model\Entity\ParentCategoriesSupplier $parent_k_categories_supplier
 * @property \App\Model\Entity\JCategory $j_category
 * @property \App\Model\Entity\ChildCategoriesSupplier[] $child_k_categories_suppliers
 */
class CategoriesSupplier extends Entity
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
        'parent_id' => true,
        'lft' => true,
        'rght' => true,
        'name' => true,
        'description' => true,
        'j_category_id' => true,
        'parent_k_categories_supplier' => true,
        'j_category' => true,
        'child_k_categories_suppliers' => true,
    ];
}
