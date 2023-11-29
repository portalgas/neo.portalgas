<?php
use Cake\Core\Configure;

echo $this->HtmlCustomSite->boxTitle(['title' => __('Cash'), 'subtitle' => 'massivi'], ['home']);

?>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Gestisci movimenti di cassa (+/-) per tutti i gasisti selezionati</h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php 
          echo $this->Form->create(null, ['id' => 'frm', 'role' => 'form']);
          echo $this->Form->control('user_ids', ['type' => 'hidden', 'id' => 'user_ids']);
          
          echo '<div class="box-body">';

          echo '<div class="row">';
          echo '<div class="col-md-6">';
          echo $this->Form->control('master_user_ids', ['type' => 'select', 'label' => "Elenco gasisti", 
                                                        'id' => 'master_user_ids', 
                                                        'options' => $users, 'multiple' => true, 'size' => 10, 'style="min-height: 300px;"']);
          
          echo $this->Form->button("Seleziona tutti", ['id' => 'users_all_select', 'type' => 'button', 'class' => 'btn btn-primary btn-block']); 

          echo '</div>';
          echo '<div class="col-md-6">';
          echo $this->Form->control('slave_user_ids', ['type' => 'select', 'label' => "Gasisti al quale applicare la spesa di cassa", 'id' => 'slave_user_ids', 'multiple' => true, 'size' => 10, 'style="min-height: 300px;"']);

          echo $this->Form->button("Deseleziona tutti", ['id' => 'users_all_deselect', 'type' => 'button', 'class' => 'btn btn-primary btn-block', 'style="display: none;"']); 

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
    $('#users_all_select').on('click', function (e) {
        $('#slave_user_ids').find('option')
                        .remove()
                        .end();

        $.each(users, function(id, name) 
        {
            $('#slave_user_ids').append($('<option></option>')
                        .attr('value', id)
                        .text(name));
        }); 
        
        $('#master_user_ids').find('option')
                        .remove()
                        .end(); 
                        
        $(this).hide();
        $('#users_all_deselect').show();
    });

    // deseleziona tutti
    $('#users_all_deselect').on('click', function (e) {
        $('#master_user_ids').find('option')
                        .remove()
                        .end();

        $.each(users, function(id, name) 
        {
            $('#master_user_ids').append($('<option></option>')
                        .attr('value', id)
                        .text(name));
        }); 
        
        $('#slave_user_ids').find('option')
                        .remove()
                        .end(); 
                        
        $(this).hide();
        $('#users_all_select').show();
    });

    $('#master_user_ids').on('click', function (e) {
        $('#master_user_ids option:selected').each(function (){			
            $('#slave_user_ids').append($('<option></option>')
                        .attr('value', $(this).val())
                        .text($(this).text()));
            
            $(this).remove();
        });
    });

    $('#slave_user_ids').on('click', function (e) {
        $('#slave_user_ids option:selected').each(function (){			
            $('#master_user_ids').append($('<option></option>')
                                .attr('value', $(this).val())
                                .text($(this).text()));
            
            $(this).remove();
        });
    });

    $('#frm').on('submit', function() {

        if($('#slave_user_ids').find('option').length==0) {
            alert('Scegli almeno un gasista al quale applicare il movimento di cassa');
            return false;
        }

        let minus = $('#minus').val();
        let plus = $('#plus').val();
        if(minus=='' && plus=='') {
            alert('Indica l\'importo da sottrarre o aggiungere per tutti i gasisti selezionati');
            return false;            
        }
        
        let user_ids = [];
        $('#slave_user_ids option').each(function () {
            user_ids.push($(this).val());
            // console.log($(this).val(), 'user_id');     
        });  
        // console.log(user_ids, 'user_ids scelti');  
        if(user_ids.length==0) {
            alert('Scegli almeno un gasista al quale applicare il movimento di cassa');
            return false;
        }
        $('#user_ids').val(user_ids);

        return true; 
    });     
});
";
$this->Html->scriptBlock($js, ['block' => true]);
