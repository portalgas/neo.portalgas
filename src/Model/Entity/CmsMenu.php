<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CmsMenu Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $cms_menu_type_id
 * @property string $name
 * @property string|null $options
 * @property int|null $sort
 * @property bool $is_public
 * @property bool $is_system
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\CmsMenuType $cms_menu_type
 * @property \App\Model\Entity\CmsDoc[] $cms_docs
 * @property \App\Model\Entity\CmsPage[] $cms_pages
 */
class CmsMenu extends Entity
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
        'cms_menu_type_id' => true,
        'name' => true,
        'options' => true,
        'sort' => true,
        'is_public' => true,
        'is_system' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'cms_menu_type' => true,
        'cms_docs' => true,
        'cms_pages' => true,
    ];
}
