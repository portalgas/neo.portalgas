<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CmsMenusDoc Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int $cms_menu_id
 * @property int $cms_doc_id
 * @property int $sort
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\CmsMenu $cms_menu
 * @property \App\Model\Entity\CmsDoc $cms_doc
 */
class CmsMenusDoc extends Entity
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
        'cms_doc_id' => true,
        'sort' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'cms_menu' => true,
        'cms_doc' => true,
    ];
}
