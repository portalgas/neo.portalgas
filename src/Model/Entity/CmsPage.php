<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CmsPage Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $cms_menu_id
 * @property string $name
 * @property string|null $body
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\CmsMenu $cms_menu
 * @property \App\Model\Entity\CmsPageImage[] $cms_page_images
 */
class CmsPage extends Entity
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
        'cms_menu_id' => true,
        'name' => true,
        'body' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'cms_menu' => true,
        'cms_page_images' => true,
    ];
}
