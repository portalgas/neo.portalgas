<?php
echo $this->Html->script('dropzone/dropzone.min', ['block' => 'scriptInclude']);
echo $this->Html->css('dropzone/dropzone.min', ['block' => 'css']);
echo $this->Html->script('vue/cms-doc-upload.js?v=20250316', ['block' => 'scriptPageInclude']);
echo $this->Html->script('vue/cms-doc.js?v=20251012', ['block' => 'scriptPageInclude']);
?>
<section class="content-header">
    <h1>
        <?php echo __('Cms Docs');?>
        <small><?php echo __('Add'); ?></small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo __('Form'); ?></h3>
                </div>
                <div class="box-body">
                    <?php
                    echo $this->Form->create(null, ['role' => 'form']);
                    echo '<div class="dropzone" id="myDropzoneDoc"></div>';
                    echo $this->Form->end(); ?>

                    <div id="vue-cms-docs" style="margin-top: 20px;">
                        <div v-show="is_found_docs === false" style="display: none;text-align: center;" class="run run-docs"><div class="spinner"></div></div>
                        <div v-show="is_found_docs === true" style="display:none;">

                            <table class="table table-hover" v-if="docs!=null && docs.length>0">
                                <thead class="thead-light">
                                <tr>
                                    <th colspan="2"><?php echo __('Name');?></th>
                                    <th><?php echo __('Size');?></th>
                                    <th><?php echo __('Cms Menu');?></th>
                                    <th><?php echo __('Cms Page');?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr
                                    v-for="doc in docs"
                                    :key="doc.id"
                                >
                                    <td>
                                        <div v-if="doc.file_exists">
                                            <a :href="'/pages/download/'+doc.uuid" target="_blank">
                                                <button class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> <?php echo __('Cms Doc Download');?></button>
                                            </a>
                                        </div>
                                        <div v-else class="alert alert-danger">
                                            <?php echo __('File does not exist');?>
                                        </div>
                                    </td>
                                    </td>
                                    <td>{{ doc.name }}</td>
                                    <td>{{ doc.size }} kb</td>
                                    <td>
                                        <div v-if="doc.cms_menu">{{ doc.cms_menu.name }} </div>
                                    <td>
                                        <ul v-if="doc.cms_pages_docs.length>0" style="margin:0px; padding:0px;">
                                            <li v-for="cms_pages_doc in doc.cms_pages_docs">{{ cms_pages_doc.cms_page.name }} </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <a :href="'/admin/cms-docs/delete/'+doc.id">
                                            <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div v-else>
                                <div class="alert alert-warning">Nessun documento caricato</div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            <!-- /.box -->
        </div>
    </div>
    <!-- /.row -->
</section>
