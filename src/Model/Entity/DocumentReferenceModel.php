<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DocumentReferenceModel Entity
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $descri
 * @property string $url_back_controller
 * @property string $url_back_action
 * @property string|null $url_back_params
 * @property bool $is_system
 * @property bool $is_active
 * @property bool $is_default_ini
 * @property bool $is_default_end
 * @property int $sort
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Document[] $documents
 */
class DocumentReferenceModel extends Entity
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
        'url_back_controller' => true,
        'url_back_action' => true,
        'url_back_params' => true,
        'is_system' => true,
        'is_active' => true,
        'is_default_ini' => true,
        'is_default_end' => true,
        'sort' => true,
        'created' => true,
        'modified' => true,
        'documents' => true,
    ];
}
