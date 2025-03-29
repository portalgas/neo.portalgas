<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CmsMenuType Entity
 *
 * @property int $id
 * @property string|null $code
 * @property string $name
 * @property string|null $descri
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\CmsMenu[] $cms_menus
 */
class CmsMenuType extends Entity
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
        'code' => true,
        'name' => true,
        'descri' => true,
        'created' => true,
        'is_system' => true,
        'modified' => true,
        'cms_menus' => true,
    ];
}
