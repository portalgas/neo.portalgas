<?php
use Cake\Core\Configure;

echo $this->HtmlCustomSite->boxTitle(['title' => __('Cash'), 'subtitle' => __('massive')], ['home']);

?>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo __('Form'); ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php 
          echo $this->Form->create(null, ['id' => 'frm', 'role' => 'form']);
          echo '<div class="box-body">';

          echo '<div class="row">';
          echo '<div class="col-md-6">';
          echo $this->Form->control('master_user_ids', ['type' => 'select', 'label' => "Elenco gasisti", 
                                                        'id' => 'master_user_ids', 
                                                        'options' => $users, 'multiple' => true, 'size' => 10]);
          
          echo $this->Form->button("Seleziona tutti", ['id' => 'users_all', 'type' => 'button', 'class' => 'btn btn-primary btn-block']); 

          echo '</div>';
          echo '<div class="col-md-6">';
          echo $this->Form->control('user_ids', ['type' => 'select', 'label' => "Gasisti al quale applicare la spesa di cassa", 'id' => 'user_ids', 'multiple' => true, 'size' => 10]);
          echo '</div>';
          echo '</div>';

          echo '<br />';
            
          echo '<div class="row">';
          echo '
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="col-2 control-label">Sarà sottratto l\'importo a tutti i gasisti selezionati</label>
                    <div class="col-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-minus" aria-hidden="true"></i></span>
                            <input type="number" class="form-control" min="0" name="minus" id="minus" placeholder="importo da sottrarre">
                        </div>
                    </div>
                </div>
            </div>';

        echo '
          <div class="col-md-6">        
            <div class="form-group">
                  <label for="name" class="col-2 control-label">Sarà aggiungo l\'importo a tutti i gasisti selezionati</label>
                  <div class="col-10">
                      <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></span>
                          <input type="number" class="form-control" min="0" name="plus" id="plus" placeholder="importo da aggiungere">
                      </div>
                  </div>
              </div>
          </div>';
        echo '</div>'; // row
                      
        echo '<div class="row">';
        echo '<div class="col-md-12">';
        echo $this->Form->control('note', ['type' => 'textarea']);
        echo '</div>';
        echo '</div>'; // row

        echo $this->Form->button(__('Submit'), ['id' => 'submit', 'class' => 'btn btn-primary pull-right', 'style' => 'margin-top:25px']); 
        echo '</div>'; /* .box-body */
        echo $this->Form->end(); ?>
        </div>
        <!-- /.box -->
      </div>
  </div>
  <!-- /.row -->
</section>

<?php 
$js = "
let users = ".json_encode($users).";
console.log(users, 'users');

$( function() {
    // seleziona tutti
    $('#users_all').on('click', function (e) {
        $('#user_ids').find('option')
                        .remove()
                        .end();

        $.each(users, function(id, name) 
        {
            $('#user_ids').append($('<option></option>')
                        .attr('value', id)
                        .text(name));
        }); 
        
        $('#master_user_ids').find('option')
                        .remove()
                        .end();        
    });

    $('#master_user_ids').on('click', function (e) {
        $('#master_user_ids option:selected').each(function (){			
            $('#user_ids').append($('<option></option>')
                        .attr('value', $(this).val())
                        .text($(this).text()));
            
            $(this).remove();
        });
    });

    $('#user_ids').on('click', function (e) {
        $('#user_ids option:selected').each(function (){			
            $('#master_user_ids').append($('<option></option>')
                                .attr('value', $(this).val())
                                .text($(this).text()));
            
            $(this).remove();
        });
    });

    $('#frm').on('submit', function() {
        let user_ids = '';
        return false; 
    });     
});
";
$this->Html->scriptBlock($js, ['block' => true]);
