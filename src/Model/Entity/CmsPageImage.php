<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CmsPageImage Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $cms_page_id
 * @property string $name
 * @property string|null $path
 * @property string $ext
 * @property int|null $sort
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\CmsPage $cms_page
 */
class CmsPageImage extends Entity
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
        'cms_page_id' => true,
        'name' => true,
        'path' => true,
        'ext' => true,
        'sort' => true,
        'created' => true,
        'modified' => true,
        'cms_page' => true,
    ];
}
