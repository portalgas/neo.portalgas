<?php
echo $this->Html->script('dropzone/dropzone.min', ['block' => 'scriptInclude']);
echo $this->Html->css('dropzone/dropzone.min', ['block' => 'css']);

echo $this->Html->script('vue/cms-image.js?v=20250316', ['block' => 'scriptPageInclude']);
echo $this->Html->script('vue/cms-doc.js?v=20250316', ['block' => 'scriptPageInclude']);
?>

<section class="content-header">
    <h1>
        <?php echo __('Cms Page');?>
        <small><?php echo __('Add'); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> Elenco  <?php echo __('Cms Pages'); ?></a></li>
    </ol>
</section>


<section class="content">
    <div class="row">
        <div class="col-md-8">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo __('Form'); ?></h3>
                </div>
                <?php
                echo $this->Form->create($cmsPage, ['id' => 'frm', 'role' => 'form']);
                echo $this->Form->control('cms_page_id', ['type' => 'hidden', 'id' => 'cms_page_id', 'value' => '']);
                echo $this->Form->control('img_ids', ['type' => 'hidden', 'id' => 'img_ids', 'value' => '']);
                echo $this->Form->control('doc_ids', ['type' => 'hidden', 'id' => 'doc_ids',  'value' => '']);
                ?>
                <div class="box-body">
                    <?php
                    if(!empty($cmsMenus))
                        echo $this->Form->control('cms_menu_id', ['options' => $cmsMenus, 'empty' => true]);
                    else
                        echo $this->element('msg', ['msg' => __('Nessuna voce di menù disponibile'), 'class' => 'warning']);
                    echo $this->Form->control('name');
                    echo $this->Form->control('body', ['type' => 'textarea', 'class' => 'form-control wysihtml5', 'rows' => 25]);
                    ?>
                </div> <!-- /.box-body -->
                <?php echo $this->Form->submit(__('Submit'), ['class' => 'btn btn-primary btn-block']); ?>
                <?php echo $this->Form->end(); ?>
            </div> <!-- /.box -->
        </div>
        <div class="col-md-4" >

            <div id="vue-cms-images">
                <div v-show="is_found_images === false" style="display: none;text-align: center;" class="run run-images"><div class="spinner"></div></div>
                <div v-show="is_found_images === true" style="display:none;">

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo __('Cms Images'); ?></h3>
                        </div>
                        <div class="box-body">

                            <table class="table table-hover">
                                <tbody>
                                <tr
                                    v-for="image in images"
                                    :key="image.id"
                                >
                                    <td>
                                        <input type="checkbox" name="imgs" v-model="selected_images" :value="image.id" />
                                    </td>
                                    <td><img :src="'/cms/imgs/'+image.organization_id+'/'+image.path" width="150px"></td>
                                    <td>{{ image.name }}</td>
                                </tr>
                                </tbody>
                            </table>
                            <?php
                            echo $this->Form->create(null, ['id' => 'frmImage', 'role' => 'form']);
                            echo '<div class="dropzone" id="myDropzoneImage"></div>';
                            echo $this->Form->end();
                            ?>
                        </div>
                    </div> <!-- /.box -->

                </div>
            </div>


            <div id="vue-cms-docs" style="margin-top: 0px;background-color: #fff">
                <div v-show="is_found_docs === false" style="display: none;text-align: center;" class="run run-docs"><div class="spinner"></div></div>
                <div v-show="is_found_docs === true" style="display:none;">

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo __('Cms Docs'); ?></h3>
                        </div>
                        <div class="box-body">

                            <table class="table table-hover">
                                <thead class="thead-light">
                                <tbody>
                                <tr
                                    v-for="doc in docs"
                                    :key="doc.id"
                                >
                                    <td>
                                        <input type="checkbox" name="docs" v-model="selected_docs" :value="doc.id" />
                                    </td>
                                    <td>
                                        <a :href="'/pages/download/'+doc.uuid" target="_blank">
                                            <button class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> <?php echo __('Cms Doc Download');?></button>
                                        </a>
                                    </td>
                                    <td>{{ doc.name }}</td>
                                </tr>
                                </tbody>
                            </table>
                            <?php
                            echo $this->Form->create(null, ['id' => 'frmDoc', 'role' => 'form']);
                            echo '<div class="dropzone" id="myDropzoneDoc"></div>';
                            echo $this->Form->end();
                            ?>
                        </div>
                    </div> <!-- /.box -->

                </div>
            </div>



        </div>
    </div>
    <!-- /.row -->
</section>


<!-- bootstrap wysihtml5 - text editor -->
<?php echo $this->Html->css('AdminLTE./plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min', ['block' => 'css']); ?>

<!-- Bootstrap WYSIHTML5 -->
<?php echo $this->Html->script('AdminLTE./plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min', ['block' => 'script']); ?>
<?php
// echo $this->Form->control('body', ['class' => 'form-control wysihtml5']);
$js = "
$( function() {
    $('.wysihtml5').wysihtml5({
        toolbar: {
            fa: true,
            html: true,
            bold: true,
            italic: true,
            underline: true,
            link: true,
            image: true,
            lists: true,
            color: true
        },
        locale: 'it-IT'
    });
});
";
$this->Html->scriptBlock($js, ['block' => true]);

$js = "
$( function() {
    $('#frm').on('submit', function (e) {
        // e.preventDefault();
        // let datas = $(this).serializeArray();

        let img_ids = [];
        let imgs = document.querySelectorAll('input[name=\"imgs\"]');
        for(var i = 0; i < imgs.length; i++) {
           if(imgs[i].checked)
             img_ids.push(imgs[i].value);
        }
        // datas.push({name: 'img_ids', value: img_ids});
        document.getElementById('img_ids').value = img_ids.join(',');

        let doc_ids = [];
        let docs = document.querySelectorAll('input[name=\"docs\"]');
        for(var i = 0; i < docs.length; i++) {
           if(docs[i].checked)
             doc_ids.push(docs[i].value);
        }
        // datas.push({name: 'doc_ids', value: doc_ids});
        document.getElementById('doc_ids').value = doc_ids.join(',');

        // console.table(datas, 'datas');

        return true;
    });
});
";
$this->Html->scriptBlock($js, ['block' => true]);
?>
