<?php
echo $this->Html->script('https://code.jquery.com/ui/1.14.1/jquery-ui.js', ['block' => true]);
echo $this->Html->css('https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css', ['block' => 'css']);
?>
<section class="content-header">
  <h1>
    <?php echo __('Cms Menus');?>

    <div class="pull-right"><?php echo $this->Html->link('<i aria-hidden="true" class="fa fa-plus"></i> Aggiungi una nuova voce di menù', ['action' => 'add'], ['class'=>'btn btn-success', 'escape' => false]) ?>
    </div>
  </h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><?php echo __('List'); ?></h3>

          <div class="box-tools">
            <!-- form action="<?php echo $this->Url->build(); ?>" method="POST">
              <div class="input-group input-group-sm" style="width: 150px;">
                <input type="text" name="table_search" class="form-control pull-right" placeholder="<?php echo __('Search'); ?>">

                <div class="input-group-btn">
                  <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                </div>
              </div>
            </form -->
          </div>
        </div>
        <div class="box-body table-responsive no-padding">

            <?php
            if(count($cmsMenus)>0) {
                echo $this->Form->create($cmsMenu, ['role' => 'form']);

                echo '<ul id="sortable">';
                foreach ($cmsMenus as $cmsMenu) {
                    echo '<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>';
                    echo h($cmsMenu->name) . ' - ';

                    echo '<small>tipologia ';
                    switch ($cmsMenu->cms_menu_type->code) {
                        case 'PAGE':
                            echo '<i class="fa fa-file-o"></i> ';
                            break;
                        case 'DOC':
                            echo '<i class="fa fa-file-pdf-o"></i> ';
                            break;
                        case 'LINK_EXT':
                            echo '<i class="fa fa-external-link"></i> ';
                            break;
                    }
                    echo $cmsMenu->cms_menu_type->name . '</small>';

                    if($cmsMenu->cms_menu_type->code=='PAGE' && empty($cmsMenu->cms_pages))
                        echo '  <span class="label label-danger label-danger">Nessuna pagina associata!</span>';
                    else
                    if($cmsMenu->cms_menu_type->code=='DOC' && empty($cmsMenu->cms_menus_docs))
                        echo '  <span class="label label-danger label-danger">Nessun documento associato!</span>';

                    echo $this->Form->control('id', ['type' => 'hidden', 'name' => 'ids[]', 'value' => $cmsMenu->id]);

                    echo '<div class="actions text-right">';
                    if (!$cmsMenu->is_active)
                        echo '<div class="label label-warning label-stato">Non visibile</div>';
                    else {
                        if ($cmsMenu->is_public)
                            echo '<div class="label label-warning label-stato">Visibile a tutti (senza autenticazione)</div>';
                        else
                            echo '<div class="label label-success label-stato">Visibile solo ai propri gasisti autenticati</div>';

                    }
                    // echo $this->Html->link(__('View'), ['action' => 'view', $cmsMenu->id], ['class'=>'btn btn-info']);
                    echo $this->Html->link('<i aria-hidden="true" class="fa fa-edit"></i> '.__('Edit'), ['action' => 'edit', $cmsMenu->id], ['class' => 'btn btn-success', 'style' => 'margin-left: 5px', 'escape' => false]);
                    if (!$cmsMenu->is_system)
                        echo $this->Html->link('<i aria-hidden="true" class="fa fa-trash"></i> '.__('Delete'), ['action' => 'delete', $cmsMenu->id], ['class' => 'btn btn-danger btn-xs-', 'escape' => false]);
                    // echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $cmsMenu->id], ['confirm' => __('Are you sure you want to delete # {0}?', $cmsMenu->id), 'class'=>'btn btn-danger btn-xs']);
                    echo '</div>';
                    echo '</li>';
                }
                echo '</ul>';

                if(count($cmsMenus)>1)
                    echo $this->Form->submit('Salva il nuovo ordinamento', ['class' => 'btn btn-success btn-block']);

                echo $this->Form->end();
            }
            else {
                echo $this->element('msg', ['msg' => "Non ci sono ancora voci di menù", 'class' => 'warning']);
            }
            ?>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>
</section>
<?php
$js = "
$( function() {
    $('#sortable').sortable();
});";
$this->Html->scriptBlock($js, ['block' => true]);
?>
<style>
    .label-stato {
        min-width: 300px;
        display: inline-block;
        font-weight: normal;
        padding: 9px 12px;
    }
    #sortable {
        list-style-type: none;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    #sortable li {
        margin: 0 3px 3px 3px;
        padding: 1em;
        padding-left: 0.4em;
        padding-left: 1.5em;
        font-size: 1.4em;
        height: auto;
    }
    .ui-state-default {
        border: 1px solid #3c8dbc;
        background: #f6f6f6;
        font-weight: normal;
        color: #454545;
    }
    .btn {
        color: #fff !important;
    }
</style>
