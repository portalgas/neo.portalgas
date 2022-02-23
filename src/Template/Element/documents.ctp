<?php
if(empty($documents) || $documents->count()==0)
    echo $this->element('msg', ['msg' => __('MsgDocumentResultsNotFound')]);
else {
?>
<table class="table table-hover">
  <thead>
    <tr>
        <th>N.</th>
        <th><?= $this->Paginator->sort('document_state_id', __('State
        ')) ?></th>
        <th><?= $this->Paginator->sort('document_type_id', __('Type')) ?></th>
        <th><?= __('document_reference_model') ?></th>
        <th><?= __('document_owner_model') ?></th>
        <th><?= $this->Paginator->sort('name') ?></th>
        <th><?= $this->Paginator->sort('file_name') ?></th>
        <th><?= $this->Paginator->sort('is_system') ?></th>
        <th><?= $this->Paginator->sort('is_active') ?></th>
        <th><?= $this->Paginator->sort('created') ?></th>
        <th><?= $this->Paginator->sort('modified') ?></th>
        <th class="actions text-center"><?= __('Actions') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $i=0;
    foreach ($documents as $document):

        $i++;

        echo '<tr>';
        echo '<td>'.$i.'</td>';
        echo '<td>';
        if($document->has('document_state')) {
          echo $this->HtmlCustom->drawState($document->document_state);
        }
        echo '</td>';
        
        echo '<td>';
        echo $document->has('document_type') ? $this->Html->link($document->document_type->name, ['controller' => 'DocumentTypes', 'action' => 'view', $document->document_type->id]) : '';
        echo '</td>';

        /*
         * a chi e' legato il documento
         */
        echo '<td>';
        if($document->has('document_reference_model') && !empty($document->document_reference_id)) {
            echo $document->document_reference_model->name.': ';
            if($document->has($document->document_reference_model->code))
              echo $document->{$document->document_reference_model->code}->name;
        }
        echo '</td>';

        /*
         * a chi e' il proprietario il documento
         */
        echo '<td>';
        if($document->has('document_owner_model') && !empty($document->document_owner_id)) {
            echo $document->document_owner_model->name.': ';
            if($document->has($document->document_owner_model->code))
              echo $document->{$document->document_owner_model->code}->name;
        }
        echo '</td>';
        
        echo '<td>'.h($document->name).'</td>';
        echo '<td>';
        echo $this->HtmlCustom->drawDocumentPreview($document);
        echo '</td>';
        echo '<td>';
        echo $this->HtmlCustom->drawTrueFalse($document, $document->is_system);
        echo '</td>';
        echo '<td>';
        echo $this->HtmlCustom->drawTrueFalse($document, $document->is_active);
        echo '</td>';
        echo '<td>';
        echo h($document->created);
        echo '</td>';
        echo '<td>';
        echo h($document->modified);
        echo '</td>';
        echo '<td class="actions text-right">';
            // $this->Html->link(__('View'), ['action' => 'view', $document->id], ['class'=>'btn btn-info btn-xs']);
            
            echo '<a href="/admin/documents/edit/'.$document->id.'/'.$document->document_reference_id.'/'.$document->document_reference_model_id.'/'.$document->document_owner_id.'/'.$document->document_owner_model_id.'" ';
            echo 'title="'.__('Edit').'" class="btn btn-warning btn-xs">'.__('Edit').'</a>';

            if(!$document->is_system) {
                $datas = [];
                $datas['document_reference_id'] = $document->document_reference_id;
                $datas['document_reference_model_id'] = $document->document_reference_model_id;
                $datas['document_owner_id'] = $document->document_owner_id;
                $datas['document_owner_model_id'] = $document->document_owner_model_id;
                echo $this->Form->postLink(__('Delete'), ['controller' => 'documents', 'action' => 'delete', $document->id], 
                ['confirm' => __('Are you sure you want to delete # {0}?', $document->name), 
                'data' => $datas,
                'class'=>'btn btn-danger btn-xs']);
            } // end if(!$document->is_system)
        echo '</td>';
        echo '</tr>';
    endforeach;
  echo '</tbody>';
echo '</table>';
} // end if(empty($documents))

echo '<p class="pull-right">';


/**
 * $document_reference_id='',         id al quale si riferisce il documento (offer_id)
 * $document_reference_model_id='',   model al quale si riferisce il documento (Offers)
 * $document_owner_id='',             id proprietario del documento (user_id)
 * $document_owner_model_id=''        model proprietario del documento (Users)
 */
if(empty($document_reference_id))
    echo 'document_reference_id empty!';
else
if(empty($document_reference_model_id))
    echo 'document_reference_model_id empty!';
else
if(empty($document_owner_id))
    echo 'document_owner_id empty!';
else
if(empty($document_owner_model_id))
    echo 'document_owner_model_id empty!';
else {   
    echo '<a href="/admin/documents/add/'.$document_reference_id.'/'.$document_reference_model_id.'/'.$document_owner_id.'/'.$document_owner_model_id.'" ';
    echo 'title="'.__('Add Document').'" class="btn btn-primary"><span class="glyphicon glyphicon-book"></span> '.__('Add Document').'</a>';
}
echo '</p>';
?>