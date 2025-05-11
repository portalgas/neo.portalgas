<?php
use Cake\Core\Configure;

$js = "var article_organization_id = $article_organization_id
var article_id = $article_id";
$this->Html->scriptBlock($js, ['block' => true]);

echo $this->Html->script('vue/articleView.js?v=20250505', ['block' => 'scriptPageInclude']);
echo $this->Html->script('dropzone/dropzone.min', ['block' => 'scriptInclude']);
echo $this->Html->css('dropzone/dropzone.min', ['block' => 'css']);
?>
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Article
      <small><?php echo __('Add'); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> <?php echo __('Home'); ?></a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content" id="vue-articles-view">
    <div class="row">
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo __('Form'); ?></h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create($article, ['role' => 'form']); ?>

            <pre>{{ article }} </pre>

            <div class="box-body">
              <?php
              echo '<div class="row">';
              echo '<div class="col-md-8">';
              $options = ['options' => $suppliersOrganizations,
                  'escape' => false,
                  'class' => 'select2- form-control',
                  'label' => __('SupplierOrganization'),
                  'empty' => Configure::read('HtmlOptionEmpty'),
                  'v-model' => 'article.supplier_organization_id',
                  '@change' => 'getSuppliersOrganization(event)'];
              echo $this->Form->control('supplier_organization_id', $options);
              echo '</div>';
              echo '<div class="col-md-4">';
              echo '<img style="max-width:250px" class="img-responsive" v-bind:src="supplier_organization.img1" v-if="supplier_organization.name!=null && supplier_organization.supplier.img1!=\'\'" />';
              echo '</div>';
              echo '</div>'; // row

              /*
              * listino articoli NON gestito dal GAS
              */
              echo '<div class="row" v-if="supplier_organization.name!=null && supplier_organization.owner_articles!=\'REFERENT\'">';
              echo '<div class="col-md-12">
                        <div class="alert alert-danger">Listino del produttore {{supplier_organization.name}} perchè è gestito {{supplier_organization.owner_articles | ownerArticlesLabel}}</div>
                </div>
              </div>'; // row

                echo '<div class="row">';
                echo '<div class="col-md-12">';
                echo $this->Form->control('category_article_id', ['options' => $categoriesArticles, 'v-model' => 'article.category_article_id']);
                echo '</div>';
                echo '</div>';
                echo '<div class="row">';
                echo '<div class="col-md-12">';
                echo $this->Form->control('name', ['v-model' => 'article.name']);
                echo '</div>';
                echo '</div>';
                echo '<div class="row">';
                echo '<div class="col-md-12">';
                echo $this->Form->control('nota', ['type' => 'textarea', 'v-model' => 'article.nota']);
                echo '</div>';
                echo '</div>';
                echo '<div class="row">';
                echo '<div class="col-md-12">';
                echo $this->Form->control('ingredienti', ['type' => 'textarea', 'v-model' => 'article.ingredienti']);
                echo '</div>';
                echo '</div>';
                echo '<div class="row">';
                echo '<div class="col-md-2">';
                ?>
                <a @click="toggleIsBio()" style="cursor: pointer;">
                    <img :class="article.bio=='N' ? 'no-bio': ''" :title="article.bio=='N' ? 'Articolo non biologico': 'Articolo biologico'" src="/img/is-bio.png" width="35" />
                    <span v-if="article.bio=='Y'">Articolo biologico</span>
                    <span v-if="article.bio=='N'">Articolo non biologico</span>
                </a>
                <?php
                echo '</div>';
                echo '<div class="col-md-10">';
                foreach ($articlesTypes as $articlesType) {
                    echo $this->Form->checkbox('articles_types_ids[]',  [
                      'value' => $articlesType->id,
                      'id' => 'articlesType-' . $articlesType->id,
                    ]);
                    echo $this->Form->label('articles_types_ids[]', $articlesType->label, ['for' => 'articlesType-' . $articlesType->id]);
                }
                echo '</div>';
                echo '</div>';

                echo '<div class="row" style="margin:15px 0 15px;">';
                echo '<div class="col-md-12">';
                echo '<div class="dropzone" id="myDropzoneImage"></div>';
                echo '<small>Estensioni dei file consentiti: .png .jpg</small>';
                echo '</div>';
                echo '</div>'; // row
?>

<pre>{{ article_variants }} </pre>
                <div class="btn btn-primary btn-block" @click="addRow">
                    <i class="fa fa-plus"></i> Aggiungi variante
                </div>

                <template v-if="article_variants.length>0">
                    <div class="box-variant"
                          v-for="(article_variant, index) in article_variants" v-bind:key="article_variant.id" >

                        <div class="row">
                            <div class="col-md-1 text-center">
                                <div class="btn btn-danger btn-custom" @click="removeRow(index)" v-if="index>0">
                                    <i class="fa fa-trash"></i>
                                </div>
                            </div>
                            <div class="col-md-11">
                                <?php echo $this->Form->control('codice', ['type' => 'text', 'label' => __('codice'), 'v-model' => 'article_variant.codice']);?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-2"><b>Quantità</b></div>
                            <div class="col-md-2"><b>Unità di misura</b></div>
                            <div class="col-md-2"><b>Prezzo</b></div>
                            <div class="col-md-1"><b>Iva</b></div>
                            <div class="col-md-2"><b>Prezzo finale</b></div>
                            <div class="col-md-2"><b>Prezzo/UM<br />(Unità di misura di riferimento)</b></div>
                        </div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" v-model="article_variant.qta" placeholder="qta" @change="changeUM(event, index);" />
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" :required="true" v-model="article_variant.um"
                                        @change="changeUM(event, index);">
                                    <option v-for="um in ums"
                                            v-bind:value="um" >
                                        {{ um }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" v-model="article_variant.prezzo" placeholder="Prezzo" @change="setPrezzoFinale(event, index)" />
                            </div>
                            <div class="col-md-1">
                                <select class="form-control" :required="true" v-model="article_variant.iva" @change="setPrezzoFinale(event, index)" >
                                    <option v-for="iva in ivas"
                                            v-bind:value="iva" >
                                        {{ iva }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" v-model="article_variant.prezzo_finale" placeholder="Prezzo finale" @change="changeUM(event, index);" />
                            </div>
                            <div class="col-md-2">
                                <div v-if="article_variant.um_rif_values.length>0">
                                    <div class="form-check" v-for="um_rif_value in article_variant.um_rif_values">
                                        <input class="form-check-input" type="radio"
                                               name="um_riferimento"
                                               v-model="article_variant.um_riferimento">
                                        <label class="form-check-label"
                                               :for="'um_rif_values-'+um_rif_value.id"
                                               v-html="$options.filters.html(um_rif_value.value)">
                                        </label>
                                    </div>
                                </div>
                                <div v-if="article_variant.um_rif_values.length==0">
                                    {{ article_variant.um_rif_label }}
                                </div>

                            </div>
                        </div> <!-- row -->

                        <div class="row">
                            <div class="col-md-1 text-center">
                                <div class="btn-custom btn"
                                        :class="article_variant.stato=='Y' ? 'btn-success' : 'btn-danger'"
                                        :title="article_variant.stato=='Y' ? 'Articolo visibile' : 'Articolo NON visibile'"
                                        @click="toggleStato(index)">
                                    <span v-if="article_variant.stato=='Y'">Visibile</span>
                                    <span v-if="article_variant.stato=='N'">Non visibile</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <?php echo $this->Form->control('pezzi_confezione', ['type' => 'number', 'label' => __('pezzi_confezione_long'), 'v-model' => 'article_variant.pezzi_confezione']);?>
                            </div>
                            <div class="col-md-3">
                                <?php echo $this->Form->control('qta_minima', ['type' => 'number', 'label' => __('qta_minima'), 'v-model' => 'article_variant.qta_minima']);?>
                            </div>
                            <div class="col-md-4">
                                <?php echo $this->Form->control('qta_massima', ['type' => 'number', 'label' => __('qta_massima'), 'v-model' => 'article_variant.qta_massima']);?>
                            </div>
                        </div> <!-- row -->
                        <div class="row">
                            <div class="col-md-1 text-center">
                                <div class="btn-custom btn"
                                        :class="article_variant.flag_presente_articlesorders=='Y' ? 'btn-success' : 'btn-danger'"
                                        :title="article.flag_presente_articlesorders=='Y' ? 'Articolo ordinabile' : 'Articolo NON ordinabile'"
                                        @click="toggleFlagPresenteArticlesOrders(index)">
                                    <span v-if="article_variant.flag_presente_articlesorders=='Y'">Ordinabile</span>
                                    <span v-if="article_variant.flag_presente_articlesorders=='N'">Non ordinabile</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <?php echo $this->Form->control('qta_minima_order', ['type' => 'number', 'label' => __('qta_minima_order'), 'v-model' => 'article_variant.qta_minima_order']);?>
                            </div>
                            <div class="col-md-3">
                                <?php echo $this->Form->control('qta_multipli', ['type' => 'number', 'label' => __('qta_multipli'), 'v-model' => 'article_variant.qta_multipli']);?>
                            </div>
                            <div class="col-md-4">
                                <?php echo $this->Form->control('qta_massima_order', ['type' => 'number', 'label' => __('qta_massima_order'), 'v-model' => 'article_variant.qta_massima_order']);?>
                            </div>
                        </div> <!-- row -->
                    </div>  <!-- v-for -->
                </template>

                <div class="btn-success btn pull-right" style="margin-top: 25px"
                     @click="frmSubmit(event)">Salva dati dell'articolo
                </div>

            </div>
            <!-- /.box-body -->

          <?php echo $this->Form->end(); ?>
        </div>
        <!-- /.box -->
      </div>
  </div>
  <!-- /.row -->
</section>

<style>
    label {
        margin-left: 5px;
        margin-right: 25px;
        font-weight: normal;
    }
    .no-bio {
        opacity: 0.1
    }
    .box-variant {
        margin-top: 5px;
        border: 2px solid #3c8dbc;
        border-radius: 5px;
        padding: 5px;
    }
    .btn-custom {
        min-width: 110px;
        margin-top: 25px;
    }
</style>
