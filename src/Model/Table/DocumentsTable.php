<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Event\Event;
use Cake\Datasource\EntityInterface;
use ArrayObject;
use Cake\Filesystem\File;

class DocumentsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('documents');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('IsSystem');
        $this->addBehavior('Document');

        // https://github.com/FriendsOfCake/cakephp-upload  
        // http://josediazgonzalez.com/2015/12/05/uploading-files-and-images/
        // $this->Form->create(... , ['type' => 'file']);
        $this->addBehavior('Josegonzalez/Upload.Upload', [
            'file_name' => [
            'path' => 'webroot{DS}files{DS}{model}{DS}{field}{DS}{primaryKey}{DS}',
            //'fileName' => md5(rand(1000, 5000000)) . '.{extension}',
            //'removeFileOnDelete' => true,
            //'removeFileOnUpdate' => FALSE
            //'error' => '' 
        ]]);

        $this->belongsTo('Organizations', [
            'foreignKey' => 'organization_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('DocumentStates', [
            'foreignKey' => 'document_state_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('DocumentTypes', [
            'foreignKey' => 'document_type_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('DocumentReferenceModels', [
            'foreignKey' => 'document_reference_model_id',
            'joinType' => 'LEFT'
        ]);
        $this->belongsTo('DocumentOwnerModels', [
            'foreignKey' => 'document_owner_model_id',
            'joinType' => 'LEFT'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->allowEmptyFile('name');

        $validator
            ->scalar('path')
            ->maxLength('path', 255)
            ->requirePresence('path', 'create')
            ->notEmptyString('path');

        $validator
            ->scalar('file_preview_path')
            ->maxLength('file_preview_path', 255)
            ->allowEmptyFile('file_preview_path');

        $validator
            ->allowEmptyFile('file_name');

        $validator
            ->integer('file_size')
            ->allowEmptyFile('file_size');

        $validator
            ->scalar('file_ext')
            ->maxLength('file_ext', 10)
            ->allowEmptyFile('file_ext');

        $validator
            ->allowEmptyFile('file_type');

        $validator
            ->scalar('descri')
            ->allowEmptyString('descri');

        $validator
            ->boolean('is_system')
            ->notEmptyString('is_system');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        $validator
            ->integer('sort')
            ->notEmptyString('sort');

        // \vendor\josegonzalez\cakephp-upload\docs\validation.rst
        // https://github.com/FriendsOfCake/cakephp-upload/blob/master/docs/validation.rst
        $validator->provider('upload', \Josegonzalez\Upload\Validation\DefaultValidation::class);

        // UPLOAD_ERR_INI_SIZE
        $validator->add('file_name', 'fileUnderPhpSizeLimit', [
            'rule' => 'isUnderPhpSizeLimit',
            'message' => __('This file is too large'),
            'provider' => 'upload'
        ]);

        // UPLOAD_ERR_FORM_SIZE
        $validator->add('file_name', 'fileUnderFormSizeLimit', [
            'rule' => 'isUnderFormSizeLimit',
            'message' => __('This file is too large'),
            'provider' => 'upload'
        ]);

        // UPLOAD_ERR_PARTIAL
        $validator->add('file_name', 'fileCompletedUpload', [
            'rule' => 'isCompletedUpload',
            'message' => __('This file could not be uploaded completely'),
            'provider' => 'upload'
        ]);

        // UPLOAD_ERR_NO_FILE
        $validator->add('file_name', 'fileFileUpload', [
            'rule' => 'isFileUpload',
            'message' => __('There was no file found to upload'),
            'provider' => 'upload'
        ]);

        // UPLOAD_ERR_CANT_WRITE
        $validator->add('file_name', 'fileSuccessfulWrite', [
            'rule' => 'isSuccessfulWrite',
            'message' => __('This upload failed'),
            'provider' => 'upload'
        ]);

        /*
        $limit = 1024;
        $validator->add('file_name', 'fileBelowMaxSize', [
            'rule' => ['isBelowMaxSize', $limit],
            'message' => __('This file is too large'),
            'provider' => 'upload'
        ]);
        
        $validator->add('file', 'fileAboveMinSize', [
            'rule' => ['isAboveMinSize', $limit],
            'message' => __('This file is too small'),
            'provider' => 'upload'
        ]);
        */

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['organization_id'], 'Organizations'));
        $rules->add($rules->existsIn(['document_state_id'], 'DocumentStates'));
        $rules->add($rules->existsIn(['document_type_id'], 'DocumentTypes'));
        /*
        $rules->add($rules->existsIn(['document_reference_model_id'], 'DocumentReferenceModels'));
        */
        $rules->add($rules->existsIn(['document_owner_model_id'], 'DocumentOwnerModels'));

        return $rules;
    }

    public function afterDelete(Event $event, EntityInterface $entity, ArrayObject $options) {

        if(!empty($entity->file_name)) {
            
            $file_path = WWW_ROOT.$entity->path.$entity->file_name;
            // debug($file_path);
        
            $file = new File($file_path);
            $file->delete();            
        }
    }

    /*
     * model = Offers
     */
    public function findByReference($model, $id) {
         
       // $documentTable = TableRegistry::get('DocumentReferenceModels');

        $results = $this->find()
                        ->where(['DocumentReferenceModels.code' => $model,
                                 'Documents.document_reference_id' => $id])
                        ->contain(['DocumentStates', 'DocumentTypes', 'DocumentReferenceModels', 'DocumentOwnerModels'])
                        ->all();

        return $results;
    }

    /*
     * model = Users
     */
    public function findByOwner($model, $id) {
         
        $results = $this->find()
                        ->where(['DocumentOwnerModels.code' => $model,
                                 'Documents.document_owner_id' => $id])
                        ->contain(['DocumentStates', 'DocumentTypes', 'DocumentReferenceModels', 'DocumentOwnerModels'])
                        ->all();

        return $results;
    }

    /*
     * reference_model_id potrebbe arrivare String (Offers)
     */
    public function getDocumentReferenceModel($reference_model_id='', $debug=false) {
        
        $results = 0;

        if($debug) debug('reference_model_id '.$reference_model_id);

        if(empty($reference_model_id))
            return $results;

        /*
         * se string => 0
         */
        $test = (int)$reference_model_id;
        if($test>0)
            $condition = ['id' => $reference_model_id];
        else
            $condition = ['code' => $reference_model_id];
        if($debug) debug($condition);

        $entityTable = TableRegistry::get('DocumentReferenceModels');

        $results = $entityTable->find()
                                ->where($condition)
                                ->first();

        return $results;       
    }

    /*
     * reference_model_id potrebbe arrivare String (Users)
     */
    public function getDocumentOwnerModel($owner_model_id='', $debug=false) {

        $results = 0;
      
        if(empty($owner_model_id))
            return $results;

        /*
         * se string => 0
         */
        $test = (int)$owner_model_id;
        if($test>0)

            $condition = ['id' => $owner_model_id];
        else
            $condition = ['code' => $owner_model_id];

        $entityTable = TableRegistry::get('DocumentOwnerModels');

        $entityResults = $entityTable->find()
                                ->where($condition)
                                ->first();

        return $results;
    }
}