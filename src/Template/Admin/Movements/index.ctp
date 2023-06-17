<?php
use Cake\Core\Configure;
use Cake\I18n\Time;

$user = $this->Identity->get();

$this->start('tb_actions');
echo '<li class="sidebar-menu-action">';
echo $this->Html->link('<i class="fa fa-plus-circle"></i> <span>'.__('New').'</span>', ['action' => 'add'], ['title' => __('New'), 'escape' => false]);
echo '</li>';
$this->end();
$this->assign('tb_sidebar', $this->fetch('tb_actions')); 

$msg = "Dalla cassa vengono riportati i saldi dei gasisti dell'anno selezionato<br>con tipologia 'Saldo movimento di cassa'";
echo $this->element('msg', ['msg' => $msg, 'class' => 'info']); 
?>

<section class="content-header">
  <h1>
  <?php echo __('Movements').' '.$search_year;?>

    <div class="pull-right">
    <?php
    if(count($movements)>0) 
      echo $this->Html->link(__('Print'), ['action' => 'print', '?' => ['search_year' => $search_year, 'search_movement_type_id' => $search_movement_type_id]], ['class'=>'btn btn-info btn-xs-disabled', 'id' => 'print', 'title' => __('Print'), 'target' => '_blank']);
    ?>
    <?php echo $this->Html->link(__('New'), ['action' => 'add'], ['class'=>'btn btn-success btn-xs-disabled', 'title' => __('New')]) ?>
    </div>
  </h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <?php 
      if($user->acl['isManager']) 
        echo $this->element('search/movements');
      ?>      
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><?php echo __('List'); ?></h3>

          <div class="box-tools">
            <?php
            /*
            <form action="<?php echo $this->Url->build(); ?>" method="POST">
              <div class="input-group input-group-sm" style="width: 150px;">
                <input type="text" name="table_search" class="form-control pull-right" placeholder="<?php echo __('Search'); ?>">

                <div class="input-group-btn">
                  <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                </div>
              </div>
            </form>
            */
            ?>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive <?php echo (count($movements)>0) ? 'no-padding': '';?>">
          <?php
          if(count($movements)>0) {
          ?>
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th scope="col" class="actions text-left"><?= __('Actions') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('year') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('movement_type_id') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('verso chi') ?></th>
                <th scope="col" class=""></th>
                <th scope="col" class=""><?= $this->Paginator->sort('name') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('importo') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('payment_type') ?></th>
                <th scope="col" class=""><?= $this->Paginator->sort('date') ?></th>
               </tr>
            </thead>
            <tbody>
              <?php 
              $totale = 0;
              foreach ($movements as $movement) { 

                // debug($movement);
              
                  echo '<tr>';
                  echo '<td class="actions text-left">';
                  // echo $this->Html->link('', ['action' => 'view', $movement->id], ['class'=>'btn btn-primary glyphicon glyphicon-eye-open', 'title' => __('View')]);
                  if($movement->edit) {
                    echo $this->Html->link('', ['action' => 'edit', $movement->id], ['class'=>'btn btn-primary glyphicon glyphicon-pencil', 'title' => __('Edit')]);
                    if(!$movement->is_system) 
                      echo $this->Form->postLink('', ['action' => 'delete', $movement->id], ['confirm' => __('Are you sure you want to delete # {0}?', $movement->name), 'title' => __('Delete'), 'class' => 'btn btn-danger glyphicon glyphicon-trash']);
                    else
                      echo $this->Html->link('', [], ['title' => __('Delete'), 'class' => 'btn btn-danger glyphicon glyphicon-trash disabled']);
                  }
                  echo '</td>';                   
                  echo '<td>'.$movement->year.'</td>';
                  echo '<td>'.$movement->movement_type->name.'</td>';
                  echo '<td>';
                  echo $movement->verso_chi;
                  echo '</td>';
                  echo '<td>';
                  if(!empty($movement->doc_url)) {
                    $ico = $this->HtmlCustom->drawDocumentIco($movement->doc_url);
                    echo '<a alt="Scarica il documento" title="Scarica il documento" href="' . $movement->doc_url . '" target="_blank"><img src="'.$ico.'" /></a>';
                  }
                  echo '</td>';                  
                  echo '<td>';
                  echo h($movement->name);
                  echo '</td>';
                  echo '<td>'.$this->HtmlCustom->importo($movement->importo).'</td>';
                  echo '<td>';
                  echo $this->Enum->draw($movement->payment_type, $payment_types);
                  echo '</td>';                  
                  echo '<td>'.$movement->date->i18nFormat('eeee d MMMM').'</td>';               
                  echo '</tr>';

                  $totale += $movement->importo;
              } // end loop
            echo '</tbody>';

            echo '<tfooter>';
            echo '<tr>';
            echo '<th></th>';
            echo '<th></th>';
            echo '<th></th>';
            echo '<th></th>';
            echo '<th></th>';
            echo '<th></th>';
            echo '<th>'.$this->HtmlCustom->importo($totale).'</th>';
            echo '<th></th>';
            echo '<th></th>';
            echo '</tr>';
            echo '</tfooter>';

            echo '</table>';
          }
          else {
            echo $this->element('msg', ['msg' => __('MsgResultsNotFound'), 'class' => 'warning']);
          } // end if(!empty($movements))
          ?>
        </div>
        <!-- /.box-body -->        
      </div>
      <!-- /.box -->

      <?php echo $this->element('paginator'); ?>

    </div>
  </div>
</section>