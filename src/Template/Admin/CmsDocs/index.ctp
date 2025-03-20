<?php
echo $this->Html->script('dropzone/dropzone.min', ['block' => 'scriptInclude']);
echo $this->Html->css('dropzone/dropzone.min', ['block' => 'css']);
echo $this->Html->script('vue/cms-doc-upload.js?v=20250316', ['block' => 'scriptPageInclude']);
echo $this->Html->script('vue/cms-doc.js?v=20250316', ['block' => 'scriptPageInclude']);
?>
<section class="content-header">
    <h1>
        <?php echo __('Cms Docs');?>
        <small><?php echo __('Add'); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> <?php echo __('Home'); ?></a></li>
    </ol>
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
                    echo $this->Form->create($cmsDoc, ['role' => 'form']);
                    echo '<div class="dropzone" id="myDropzoneDoc"></div>';
                    echo $this->Form->end(); ?>

                    <div id="vue-cms-docs" style="margin-top: 20px;">
                        <div v-show="is_found_docs === false" style="display: none;text-align: center;" class="run run-docs"><div class="spinner"></div></div>
                        <div v-show="is_found_docs === true" style="display:none;">

                            <table class="table table-hover">
                                <thead class="thead-light">
                                <tr>
                                    <th colspan="2"><?php echo __('Name');?></th>
                                    <th><?php echo __('Size');?></th>
                                    <th><?php echo __('Cms Menu');?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr
                                    v-for="doc in docs"
                                    :key="doc.id"
                                >
                                    <td>
                                        <a :href="'/admin/cms-docs/download/'+doc.id" target="_blank">
                                            <button class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> <?php echo __('Cms Doc Download');?></button>
                                        </a>
                                    </td>
                                    <td>{{ doc.name }}</td>
                                    <td>{{ doc.size }}</td>
                                    <td>{{ doc.cms_menu }}</td>
                                    <td>
                                        <a :href="'/admin/cms-docs/delete/'+doc.id" target="_blank">
                                            <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>
            <!-- /.box -->
        </div>
    </div>
    <!-- /.row -->
</section>
