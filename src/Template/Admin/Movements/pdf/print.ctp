<section class="content-header">
  <h1>
    <?php echo __('Movements').' '.$search_year;?>
  </h1>
</section>

<div class="box-body table-responsive <?php echo (count($movements)>0) ? 'no-padding': '';?>">
  <?php
  if(count($movements)>0) {
  // if(!empty($movements)) {
  ?>
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
      <thead>
        <tr>
          <th><?= __('Year') ?></th>
          <th><?= __('Movement Type') ?></th>
          <th><?= __('Verso chi') ?></th>
          <th><?= __('Name') ?></th>
          <th><?= __('Importo') ?></th>
          <th><?= __('Payment Types') ?></th>
          <th><?= __('Date') ?></th>
          </tr>
      </thead>
      <tbody>
        <?php 
        $totale = 0;
        foreach ($movements as $movement) { 

          // debug($movement);
        
            echo '<tr>';
            echo '<td>'.$movement->year.'</td>';
            echo '<td>'.$movement->movement_type->name.'</td>';
            echo '<td>';            
            echo $movement->verso_chi;
            echo '</td>';
            echo '<td>';
            echo h($movement->name);
            echo '</td>';
            echo '<td class="text-center">'.$this->HtmlCustom->importo($movement->importo).'</td>';
            echo '<td class="text-center">';
            echo $this->Enum->draw($movement->payment_type, $payment_types);
            echo '</td>';                  
            echo '<td>'.$movement->date->i18nFormat('eeee d MMMM').'</td>';               
            echo '</tr>';

            $totale += $movement->importo;
        } // end loop
      echo '</tbody>';

      echo '<tfoot>';
      echo '<tr>';
      echo '<th></th>';
      echo '<th></th>';
      echo '<th></th>';
      echo '<th></th>';
      echo '<th>'.$this->HtmlCustom->importo($totale).'</th>';
      echo '<th></th>';
      echo '<th></th>';
      echo '</tr>';
      echo '</tfoot>';

      echo '</table>';
    }
    else {
      echo __('MsgResultsNotFound');
    } // end if(!empty($movements))
    ?>
  </div>
  <!-- /.box-body -->        
</div>
<!-- /.box -->