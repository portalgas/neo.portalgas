<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * JContent Entity
 *
 * @property int $id
 * @property int $asset_id
 * @property string $title
 * @property string $alias
 * @property string $title_alias
 * @property string $introtext
 * @property string $fulltext
 * @property int $state
 * @property int $sectionid
 * @property int $mask
 * @property int $catid
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property string $created_by_alias
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $modified_by
 * @property int $checked_out
 * @property \Cake\I18n\FrozenTime $checked_out_time
 * @property \Cake\I18n\FrozenTime $publish_up
 * @property \Cake\I18n\FrozenTime $publish_down
 * @property string $images
 * @property string $urls
 * @property string $attribs
 * @property int $version
 * @property int $parentid
 * @property int $ordering
 * @property string $metakey
 * @property string $metadesc
 * @property int $access
 * @property int $hits
 * @property string $metadata
 * @property int $featured
 * @property string $language
 * @property string $xreference
 *
 * @property \App\Model\Entity\Asset $asset
 * @property \App\Model\Entity\KSupplier[] $k_suppliers
 */
class Content extends Entity
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
        'asset_id' => true,
        'title' => true,
        'alias' => true,
        'title_alias' => true,
        'introtext' => true,
        'fulltext' => true,
        'state' => true,
        'sectionid' => true,
        'mask' => true,
        'catid' => true,
        'created' => true,
        'created_by' => true,
        'created_by_alias' => true,
        'modified' => true,
        'modified_by' => true,
        'checked_out' => true,
        'checked_out_time' => true,
        'publish_up' => true,
        'publish_down' => true,
        'images' => true,
        'urls' => true,
        'attribs' => true,
        'version' => true,
        'parentid' => true,
        'ordering' => true,
        'metakey' => true,
        'metadesc' => true,
        'access' => true,
        'hits' => true,
        'metadata' => true,
        'featured' => true,
        'language' => true,
        'xreference' => true,
        'asset' => true,
        'suppliers' => true,
    ];
}
