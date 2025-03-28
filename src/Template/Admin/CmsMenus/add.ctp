<?php
use Cake\Core\Configure;
?>

  <section class="content-header">
    <h1>
        <?php echo __('Cms Menu');?>
      <small><?php echo __('Add'); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> Elenco <?php echo __('Cms Menus'); ?></a></li>
    </ol>
  </section>

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
          <?php echo $this->Form->create($cmsMenu, ['role' => 'form']); ?>
            <div class="box-body">
              <?php
                echo $this->Form->control('cms_menu_type_id', ['options' => $cmsMenuTypes, 'id' => 'cms_menu_type_id', 'label' => __('Cms MenuType'), 'empty' => Configure::read('HtmlOptionEmpty'), 'required' => true]);
                echo $this->Form->control('name', ['label' => __('Cms MenuName'), 'required' => true]);

                /*
                 * docs
                 */
                echo '<div class="row" id="doc" style="display:none;">';
                echo '<div class="col col-md-8">';
                if(count($cmsDocs->toArray())==0)
                    echo $this->element('msg', ['msg' => "Non ci sono documenti caricati da poter associare alla voce di menù", 'class' => 'warning']);
                else
                    echo $this->Form->control('cms_doc_id', ['options' => $cmsDocs, 'label' => __('Cms Docs'), 'empty' => Configure::read('HtmlOptionEmpty')]);
                echo '</div>';
                echo '<div class="col col-md-4"><br />';
                echo '<a href="'.$this->Url->build(['controller' => 'cmsDocs', 'action' => 'index']).'" class="btn btn-primary btn-block">'.__('Cms Add Doc').'</a>';
                echo '</div>';
                echo '</div>';

                /*
                * pages
                */
                echo '<div class="row" id="page" style="display:none;">';
                echo '<div class="col col-md-8">';
                if(count($cmsPages->toArray())==0)
                    echo $this->element('msg', ['msg' => "Non ci sono pagine da poter associare alla voce di menù", 'class' => 'warning']);
                else
                    echo $this->Form->control('cms_page_id', ['options' => $cmsPages, 'label' => __('Cms Pages'), 'empty' => Configure::read('HtmlOptionEmpty')]);
                echo '</div>';
                echo '<div class="col col-md-4"><br />';
                echo '<a href="'.$this->Url->build(['controller' => 'cmsPages', 'action' => 'add']).'" class="btn btn-primary btn-block">'.__('Cms Add Page').'</a>';
                echo '</div>';
                echo '</div>';

                echo '<div id="link-ext" style="display:none;">';
                echo $this->Form->control('options', ['type' => 'text', 'label' => __('Cms LinkExt Options'), 'placeholder' => 'https:///www.']);
                echo '</div>';

                echo '<div class="row">';
                echo '<div class="col col-md-6">';
                echo $this->Form->control('is_public', ['label' => __('Cms Menu Is Public')]);
                echo $this->element('msg', ['msg' => __('Note-is-public'), 'class' => 'info']);
                echo '</div>';
                echo '<div class="col col-md-6">';
                echo $this->Form->control('is_active', ['checked' => true]);
                echo $this->element('msg', ['msg' => __('Note-is-active'), 'class' => 'info']);
                echo '</div>';
                echo '</div>';

                echo '</div>'; //.box-body

                echo $this->Form->submit(__('Submit'));

          echo $this->Form->end(); ?>
        </div>
        <!-- /.box -->
      </div>
  </div>
  <!-- /.row -->
</section>
<?php
$js = "
$( function() {
    $('#cms_menu_type_id').on('change', function() {
        let cms_menu_type_id = $('#cms_menu_type_id').val();

        $('#doc').hide();
        $('#page').hide();
        $('#link-ext').hide();

        switch(cms_menu_type_id) {
            case '1': // PAGE
                $('#doc').hide();
                $('#page').show();
                $('#link-ext').hide();
                break;
            case '2': // 2 DOC
               $('#doc').show();
                $('#page').hide();
                $('#link-ext').hide();
                break;
            case '3': // 3 LINK-EXT
                $('#doc').hide();
                $('#page').hide();
                $('#link-ext').show();
                break;
        }
    })
});";
$this->Html->scriptBlock($js, ['block' => true]);
?>


