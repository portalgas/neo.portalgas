<?php
use Cake\Core\Configure;
?>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
    <?php 
      if($user->acl['isManager'])
        echo $this->element('search/users');
      ?>        
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><?php echo __('List'); ?></h3>

          <div class="box-tools">
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive <?php echo (!empty($results)) ? 'no-padding': '';?>">
          <?php
          if(!empty($results)) {
          ?>
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th scope="col" class=""><?= __('N') ?></th>
                <th scope="col" class=""><?= __('Name') ?></th>
                <th scope="col" class=""><?= __('Mail') ?></th>
                <th scope="col"><?= __('Gas Groups') ?></th>  
                <th scope="col" class="actions text-left"><?= __('Stato') ?></th>  
                <th scope="col" class="actions text-left"><?= __('Actions') ?></th>                
              </tr>
            </thead>
            <tbody>
              <?php 
              $rowspan = (count($gas_groups) + 1);
              foreach ($results as $numResult => $user) { 

                  // debug($user);                

                  echo '<tr>';
                  echo '<td rowspan="'.$rowspan.'">'.($numResult + 1).'</td>'; 
                  echo '<td rowspan="'.$rowspan.'">'.h($user['name']).'</td>'; 
                  echo '<td rowspan="'.$rowspan.'">';
                  echo $this->HtmlCustom->mail($user['email']);
                  echo '</td>'; 
                  foreach($user['gas_groups'] as $gas_group) {
                      echo '<tr>';
                      echo '<td>';
                      echo $gas_group['name'];
                      echo '</td>';
                      
                      if($gas_group['checked']) {
                        echo '<td>';
                        echo '<label class="label label-success">Cassiere del gruppo</label>';
                        echo '</td>';
                        echo '<td>';
                        echo $this->Html->link('Elimina '.h($user['name']).' da cassiere del gruppo '.$gas_group['name'], ['action' => 'delete', $group_id, $user['id'], $gas_group['id'], '?' => ['search_user_id' => $search_user_id]], ['class'=>'btn btn-danger btn-block', 'title' => __('Delete')]);
                        echo '</td>';
                      }
                      else {
                        echo '<td>';
                        echo '<label class="label label-danger">Da associare</label>';
                        echo '</td>';
                        echo '<td>';
                        echo $this->Html->link(h($user['name']).' diventa cassiere del gruppo '.$gas_group['name'], ['action' => 'add', $group_id, $user['id'], $gas_group['id'], '?' => ['search_user_id' => $search_user_id]], ['class'=>'btn btn-success btn-block', 'title' => __('Add')]);
                        echo '</td>';
                      }
                        
                      echo '</tr>';
                  }
                } // end loop
            echo '</tbody>';
            echo '</table>';
            }
            else {
              echo $this->element('msg', ['msg' => __('MsgResultsNotFound'), 'class' => 'warning']);
            } // end if(!empty($users))
          ?>
        </div>
        <!-- /.box-body -->        
      </div>
      <!-- /.box -->

    </div>
  </div>
</section>
