<?php
echo $this->Html->script('dropzone/dropzone.min', ['block' => 'scriptInclude']);
echo $this->Html->css('dropzone/dropzone.min', ['block' => 'css']);
echo $this->Html->script('vue/cms-image-upload.js?v=20250316', ['block' => 'scriptPageInclude']);
echo $this->Html->script('vue/cms-image.js?v=20250316', ['block' => 'scriptPageInclude']);
?>
<section class="content-header">
    <h1>
        <?php echo __('Cms Images');?>
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
                    echo $this->Form->create($cmsImage, ['role' => 'form']);
                    echo '<div class="dropzone" id="myDropzoneImage"></div>';
                    echo $this->Form->end(); ?>

                    <div id="vue-cms-images" style="margin-top: 20px;">
                        <div v-show="is_found_images === false" style="display: none;text-align: center;" class="run run-images"><div class="spinner"></div></div>
                        <div v-show="is_found_images === true" style="display:none;">

                            <table class="table table-hover" v-if="images!=null && images.length>0">
                                <thead class="thead-light">
                                <tr>
                                    <th colspan="2"><?php echo __('Name');?></th>
                                    <th><?php echo __('Size');?></th>
                                    <th><?php echo __('Cms Page');?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr
                                    v-for="image in images"
                                    :key="image.id"
                                >
                                    <td>
                                        <a :href="image.path" target="_blank">
                                            <img :src="'/cms/imgs/'+image.organization_id+'/'+image.path" style="width: 150px;" />
                                        </a>
                                    </td>
                                    <td>{{ image.name }}</td>
                                    <td>{{ image.size }} kb</td>
                                    <td>
                                        <div v-if="image.cms_pages_images!=null">
                                            <div v-if="image.cms_pages_images.length==0">
                                                Nessuna pagina
                                            </div>
                                            <div v-else>
                                                <ul style="margin:0px; padding: 0px">
                                                    <li v-for="cms_pages_image in image.cms_pages_images">
                                                        {{ cms_pages_image.cms_page.name }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a :href="'/admin/cms-images/delete/'+image.id">
                                            <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div v-else>
                                <div class="alert alert-warning">Nessuna immagine caricata</div>
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
