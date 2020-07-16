<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Document Entity
 *
 * @property int $id
 * @property int $organization_id
 * @property int|null $document_state_id
 * @property int|null $document_type_id
 * @property int|null $document_reference_model_id
 * @property int|null $document_reference_id
 * @property int|null $document_owner_model_id
 * @property int|null $document_owner_id
 * @property string|null $name
 * @property string $path
 * @property string|null $file_preview_path
 * @property string|null $file_name
 * @property int|null $file_size
 * @property string|null $file_ext
 * @property string|null $file_type
 * @property string|null $descri
 * @property bool $is_system
 * @property bool $is_active
 * @property int $sort
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Organization $organization
 * @property \App\Model\Entity\DocumentState $document_state
 * @property \App\Model\Entity\DocumentType $document_type
 * @property \App\Model\Entity\DocumentReferenceModel $document_reference_model
 * @property \App\Model\Entity\DocumentReference $document_reference
 * @property \App\Model\Entity\DocumentOwnerModel $document_owner_model
 * @property \App\Model\Entity\DocumentOwner $document_owner
 */
class Document extends Entity
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
        'document_state_id' => true,
        'document_type_id' => true,
        'document_reference_model_id' => true,
        'document_reference_id' => true,
        'document_owner_model_id' => true,
        'document_owner_id' => true,
        'name' => true,
        'path' => true,
        'file_preview_path' => true,
        'file_name' => true,
        'file_size' => true,
        'file_ext' => true,
        'file_type' => true,
        'descri' => true,
        'is_system' => true,
        'is_active' => true,
        'sort' => true,
        'created' => true,
        'modified' => true,
        'organization' => true,
        'document_state' => true,
        'document_type' => true,
        'document_reference_model' => true,
        'document_reference' => true,
        'document_owner_model' => true,
        'document_owner' => true,
    ];
}
