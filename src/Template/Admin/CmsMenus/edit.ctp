<?php
use Cake\Core\Configure;
?>

<section class="content-header">
    <h1>
      <?php echo __('Cms Menu');?>
      <small><?php echo __('Edit'); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> Elenco <?php echo __('Cms Menus'); ?></a></li>
    </ol>
  </section>

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
                    <?php echo $this->Form->create($cmsMenu, ['role' => 'form']);
                    ?>
                    <div class="box-body">
                        <?php
                        echo $this->Form->control('cms_menu_type_id', ['type' => 'text', 'value' => $cmsMenu->cms_menu_type->name, 'id' => 'cms_menu_type_id', 'label' => __('Cms MenuType'), 'disabled' => 'disabled']);
                        echo $this->Form->control('name', ['label' => __('Cms MenuName'), 'required' => true]);

                        /*
                         * docs
                         */
                        if($cmsMenu->cms_menu_type->code=='DOC') {
                            echo '<div class="row" id="doc">';
                            echo '<div class="col col-md-8">';
                            if(count($cmsDocs->toArray())==0 || !isset($cmsMenu->cms_docs[0]))
                                echo $this->element('msg', ['msg' => "Non ci sono documenti caricati da poter associare alla voce di menù", 'class' => 'warning']);
                            else
                                echo $this->Form->control('cms_doc_id', ['options' => $cmsDocs, 'value' => $cmsMenu->cms_docs[0]->id, 'label' => __('Cms Docs'), 'empty' => Configure::read('HtmlOptionEmpty')]);
                            echo '</div>';
                            echo '<div class="col col-md-4"><br />';
                            echo '<a href="'.$this->Url->build(['controller' => 'cmsDocs', 'action' => 'index']).'" class="btn btn-primary btn-block">'.__('Cms Add Doc').'</a>';
                            echo '</div>';
                            echo '</div>';
                        }

                        /*
                        * pages
                        */
                        if($cmsMenu->cms_menu_type->code=='PAGE') {
                            echo '<div class="row" id="page">';
                            echo '<div class="col col-md-8">';
                            if (count($cmsPages->toArray()) == 0)
                                echo $this->element('msg', ['msg' => "Non ci sono pagine da poter associare alla voce di menù", 'class' => 'warning']);
                            else {
                                $value = null;
                                if(isset($cmsMenu->cms_pages) && isset($cmsMenu->cms_pages[0])) $value = $cmsMenu->cms_pages[0]->id;
                                echo $this->Form->control('cms_page_id', ['options' => $cmsPages, 'value' => $value, 'label' => __('Cms Pages'), 'empty' => Configure::read('HtmlOptionEmpty')]);
                            }
                            echo '</div>';
                            echo '<div class="col col-md-4"><br />';
                            echo '<a href="' . $this->Url->build(['controller' => 'cmsPages', 'action' => 'add']) . '" class="btn btn-primary btn-block">' . __('Cms Add Page') . '</a>';
                            echo '</div>';
                            echo '</div>';
                        }

                        if($cmsMenu->cms_menu_type->code=='LINK_EXT') {
                            echo '<div id="link-ext">';
                            echo $this->Form->control('options', ['type' => 'text', 'label' => __('Cms LinkExt Options'), 'placeholder' => 'https:///www.']);
                            echo '</div>';
                        }

                        echo '<div class="row">';
                        echo '<div class="col col-md-6">';
                        echo $this->Form->control('is_public', ['label' => __('Cms Menu Is Public')]);
                        echo $this->element('msg', ['msg' => __('Note-is-public'), 'class' => 'info']);
                        echo '</div>';
                        echo '<div class="col col-md-6">';
                        if(!$cmsMenu->is_system) {
                            echo $this->Form->control('is_active');
                            echo $this->element('msg', ['msg' => __('Note-is-active'), 'class' => 'info']);
                        }
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
