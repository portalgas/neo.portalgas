<?php
echo $this->Html->script('organizationsPaysIndex', ['block' => 'scriptPageInclude']);
?>
<style>
.width-75 {
  width: 75;
}
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?php __('OrganizationsPays');?>

    <div class="pull-right"><?php echo $this->Html->link(__('New'), ['action' => 'add'], ['class'=>'btn btn-success btn-xs']) ?></div>
  </h1>
</section>

<?php 
echo $this->element('msg', ['msg' => "Se il messaggio è attivato il manager/tesoriere dopo la login visualizzarà il messaggio<br /><br />".__('msg_organization_pay_to_pay')]);
?>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <?php
      echo $this->element('organization_pay_search', ['totResults' => $organizationsPays->count(), 'beneficiario_pay' => $beneficiario_pays, 'hasMsgs' => $hasMsgs, 'type_pays' => $type_pays]);
        ?>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header"> 
          <?php 
          echo '<h3 class="box-title">';
          echo __('List');
          if(!empty($search_year))
            echo ' anno '.$search_year;
          echo '</h3>';
          echo '<div class="box-tools">';

            /*
            <form action="<?php echo $this->Url->build(); ?>" method="POST">
              <div class="input-group input-group-sm" style="width: 150px;">
                <input type="text" name="table_search" class="form-control pull-right" placeholder="<?php echo __('Search'); ?>">

                <!-- div class="input-group-btn">
                  <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                </div -->
              </div>
            </form>
            */
            ?>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover">
            <thead>
              <tr>
                  <th scope="col"><?= $this->Paginator->sort('organization_id') ?></th>
                  <?php
                  if(empty($search_year))
                      echo '<th scope="col">'.$this->Paginator->sort('year').'</th>';
                  ?>
                  <th scope="col"><?= __('mail') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('lastVisitDate') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tot_users') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tot_orders') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tot_suppliers_organizations') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('tot_articles') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('beneficiario_pay') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('importo') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('type_pay') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('data_pay') ?></th>
                  <th scope="col"><?= __('Is Saldato') ?></th>
                  <th scope="col"><?= __('doc') ?></th>
                  <th scope="col" style="width: 85px;"><?= __('Msg attivato') ?></th>
                  <th scope="col" style="width: 50px;"></th>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php 
              foreach ($organizationsPays as $organizationsPay): 

                  echo '<tr>';
                  echo '<td>';
                  $label = $organizationsPay->organization->name.' ('.$organizationsPay->organization->id.')';
                     echo  $label;
                     // echo $this->Html->link($label, ['controller' => 'Organizations', 'action' => 'view', $organizationsPay->organization->id]); 
                    
                  echo '</td>';
                  if(empty($search_year))
                      echo '<td>'.h($organizationsPay->year).'</td>';
                  echo '<td>';
                  echo $this->HtmlCustom->mailIco($organizationsPay->paramsPay['payMail']);
                  echo '</td>';
                  echo '<td>';
                  echo h($organizationsPay->lastVisitDate->lastvisitDate).'<br />';
                  echo h($organizationsPay->lastVisitDate->username);
                  echo '</td>';
                  echo '<td>';
                  echo $this->Number->format($organizationsPay->tot_users);
                  echo '</td>';
                  echo '<td>';
                  echo $this->Number->format($organizationsPay->tot_orders);
                  echo '</td>';
                  echo '<td>';
                  echo $this->Number->format($organizationsPay->tot_suppliers_organizations);
                  echo '</td>';
                  echo '<td>';
                  echo $this->Number->format($organizationsPay->tot_articles);
                  echo '</td>';
                  echo '<td>';
                  echo $this->Form->control('beneficiario_pay', ['label' => false, 
                      'options' => $beneficiario_pays,
                      'default' => $organizationsPay->beneficiario_pay,
                      'class' => 'form-control fieldUpdateAjaxChange', 
                      'data-attr-entity' => 'OrganizationsPays', 
                      'data-attr-field' => 'beneficiario_pay', 
                      'data-attr-id' => $organizationsPay->id
                  ]);
                  echo '</td>';

                  echo '<td>';
                  echo $this->HtmlCustom->importo($organizationsPay->importo);
                  if(!empty($organizationsPay->import_additional_cost))
                    echo ' + '.$this->HtmlCustom->importo($organizationsPay->import_additional_cost);
                  /*
                  echo $this->Form->control('importo', ['type' => 'number', 'label' => false, 'inputmode' => 'numeric', 
                      'class' => 'customFieldUpdateAjax', 
                      'data-attr-entity' => 'OrganizationsPays', 
                      'data-attr-field' => 'importo', 
                      'data-attr-id' => $organizationsPay->id
                  ]);
                  */
                  echo '</td>';
                  echo '<td>';
                  echo $this->Form->control('type_pay', ['label' => false, 
                      'options' => $type_pays,
                      'default' => $organizationsPay->type_pay,
                      'class' => 'form-control fieldUpdateAjaxChange', 
                      'data-attr-entity' => 'OrganizationsPays', 
                      'data-attr-field' => 'type_pay', 
                      'data-attr-id' => $organizationsPay->id
                  ]);
                  echo '</td>';
                  echo '<td>';
                  if($organizationsPay->isSaldato)
                    echo h($organizationsPay->data_pay);
                  echo '</td>';
                  if($organizationsPay->isSaldato)
                     echo '<td class="table-col-label table-col-label-success">Saldato</td>';
                  else
                     echo '<td class="table-col-label table-col-label-alert">Da saldare</td>';
                  
                  echo '<td>';
                  if(!empty($organizationsPay->doc_url)) {
                      echo $this->Html->link(
                          'Pdf',
                          $organizationsPay->doc_url,
                          ['class' => 'button', 'target' => '_blank']
                      );
                  }
                  echo '</td>';

                  $options = ['class' => 'form-control customFieldUpdateAjax', 
                              'data-attr-id' => $organizationsPay->id, 
                              'label' => false, 
                              'value' => $organizationsPay->organization->hasMsg, 
                              'options' => $hasMsgs];

                  if(empty($organizationsPay->doc_url)) 
                    $options += ['disabled' => 'disabled', 'title' => 'File PDF non presente'];

                  echo '<td>';
                  echo $this->Form->control('hasMsg', $options, ['class' => 'width-75']);
                  echo '</td>';
                  echo '<td id="OrganizationsPays-'.$organizationsPay->id.'"></td>';

                  echo '<td class="actions text-right">';
                  echo $this->Html->link(__('Edit'), ['action' => 'edit', $organizationsPay->id], ['class'=>'btn btn-warning btn-xs']);
                  /* 
                  echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $organizationsPay->id], ['confirm' => __('Are you sure you want to delete # {0}?', $organizationsPay->id), 'class'=>'btn btn-danger btn-xs']);
                  */ 
                  echo '</td>';
                echo '</tr>';
              endforeach; 
              ?>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->

    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>

    
    </div>
  </div>
</section>