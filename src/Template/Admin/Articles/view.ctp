<?php
use Cake\Core\Configure;

$js = "var article_organization_id = $article_organization_id
var article_id = $article_id";
$this->Html->scriptBlock($js, ['block' => true]);

echo $this->Html->script('vue/utils.js?v=20250519', ['block' => 'scriptPageInclude']);
echo $this->Html->script('vue/articleView.js?v=20250112', ['block' => 'scriptPageInclude']);
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
      <li><a href="<?php echo $this->Url->build(['action' => 'index_quick']); ?>"><i class="fa fa-list"></i> <?php echo __('List Articles'); ?></a></li>
    </ol>
  </section>


  <!-- Main content -->
  <section class="content" id="vue-articles-view">
    <div class="row">
      <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Dati articolo</h3>
          </div>

          <!-- /.box-header -->
          <!-- form start -->
            <div class="loader-global" v-if="is_run">
                <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
            </div>

          <?php
              echo $this->Form->create(null, ['role' => 'form']);

              echo '<div class="box-body">';
              echo '<div class="row">';
              echo '<div class="col-md-8">';
              $options = ['options' => $suppliersOrganizations,
                  'escape' => false,
                  'id' => 'supplier_organization_id',
                  'class' => 'select2 form-control',
                  'label' => __('SupplierOrganization'),
                  'empty' => false,
                  'v-model' => 'article.supplier_organization_id',
                  'required' => true,
                  '@change' => 'getSuppliersOrganization(event); setValidate(event)',  // con select2 non funziona, triggerato nel mount di articlesView.js
                ]; 
              echo $this->Form->control('supplier_organization_id', $options);
              echo '<div class="errors" v-if="errors.supplier_organization_id" v-html="errors.supplier_organization_id"></div>';
              echo '</div>';
              echo '<div class="col-md-4">';
              echo '<img style="max-width:250px; padding: 10px;" class="img-responsive" v-bind:src="supplier_organization.img1" v-if="supplier_organization.name!=null && supplier_organization.supplier.img1!=\'\'" />';
              echo '</div>';
              echo '</div>'; // row

              /*
              * listino articoli NON gestito dal GAS
              */
              echo '<div class="row" v-if="supplier_organization.name!=null && supplier_organization.owner_articles!=\'REFERENT\'">';
              echo '<div class="col-md-12">
                        <div class="alert alert-danger">Il listino di {{supplier_organization.name}} è gestito {{supplier_organization.owner_articles | ownerArticlesLabel}}</div>
                </div>
              </div>'; // row
              echo '<div v-else>';

                echo '<div class="row">';
                echo '<div class="col-md-12">';
                echo $this->Form->control('category_article_id', ['options' => $categoriesArticles, 'v-model' => 'article.category_article_id', 'required' => true]);
                echo '</div>';
                echo '</div>';
                echo '<div class="row">';
                echo '<div class="col-md-12">';
                echo $this->Form->control('name', ['v-model' => 'article.name', 'required' => true, '@change' => 'setValidate(event)']);
                echo '<div class="errors" v-if="errors.name" v-html="errors.name"></div>';
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
                echo '<div class="col-md-3">';
                ?>
                <a @click="toggleIsBio()" style="cursor: pointer;">
                    <img :class="article.bio=='N' ? 'no-bio': ''" :title="article.bio=='N' ? 'Articolo non biologico': 'Articolo biologico'" src="/img/is-bio.png" width="35" />
                    <span v-if="article.bio=='Y'">Articolo biologico</span>
                    <span v-if="article.bio=='N'">Articolo non biologico</span>
                </a>
                <?php
                echo '</div>';
                echo '<div class="col-md-9">';
                foreach ($articlesTypes as $articlesType) {
                    echo $this->Form->checkbox('articles_types_ids[]',  [
                        'value' => $articlesType->id,
                        'id' => 'articlesType-' . $articlesType->id,
                        'v-model' => 'article.articles_types_ids',
                        '@change' => "toggleTypes()"
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

                <div class="row">
                    <div class="col-md-10">
                        <div class="btn btn-primary btn-block" @click="addRow">
                            <i class="fa fa-plus"></i> Aggiungi variante
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#info-variants">
                            <i class="fa fa-info-circle" aria-hidden="true"></i> Come gestire le varianti</button>
                            <?php
                            $html = '<h4>Esempio con i pacchi di pasta</h4>
                                    <div class="row"><div class="col-md-2"><label style="min-height: 45px;">Quantità</label><input type="text" class="form-control" value="0,35" readonly></div><div class="col-md-3"><label style="min-height: 45px;">Unità di misura</label><input type="text" class="form-control" value="GR" readonly></div><div class="col-md-3"><label style="min-height: 45px;">Prezzo finale</label><input type="text" readonly class="form-control" value="0,90"></div><div class="col-md-4"><label style="min-height: 45px;">Prezzo/UM</label><input type="text" class="form-control" readonly value="2,57 al Kg"></div></div>
                                    <br />
                                    <div class="row"><div class="col-md-2"><label style="min-height: 45px;">Quantità</label><input type="text" class="form-control" value="0,50" readonly></div><div class="col-md-3"><label style="min-height: 45px;">Unità di misura</label><input type="text" class="form-control" value="KG" readonly></div><div class="col-md-3"><label style="min-height: 45px;">Prezzo finale</label><input type="text" readonly class="form-control" value="1,10"></div><div class="col-md-4"><label style="min-height: 45px;">Prezzo/UM</label><input type="text" class="form-control" readonly value="2,22 al Kg"></div></div>
                                    <br />
                                    <div class="row"><div class="col-md-2"><label style="min-height: 45px;">Quantità</label><input type="text" class="form-control" value="1,00" readonly></div><div class="col-md-3"><label style="min-height: 45px;">Unità di misura</label><input type="text" class="form-control" value="KG" readonly></div><div class="col-md-3"><label style="min-height: 45px;">Prezzo finale</label><input type="text" readonly class="form-control" value="1,99"></div><div class="col-md-4"><label style="min-height: 45px;">Prezzo/UM</label><input type="text" class="form-control" readonly value="1,99 al Kg"></div></div>
                               ';
                            echo $this->HtmlCustom->drawModal('info-variants', 'Come gestire le varianti', $html);?>
                    </div>
                </div>


                <template v-if="article_variants.length>0">
                    <div class="box-variant"
                          v-for="(article_variant, index) in article_variants" v-bind:key="article_variant.id" >

                        <div class="row">
                            <div class="col-md-6">
                                <?php echo $this->Form->control('codice', ['type' => 'text', 'label' => __('codice'), 'v-model' => 'article_variant.codice']);?>
                            </div>
                            <div class="col-md-2 box-btn">
                                <div class="btn btn-danger btn-block" @click="removeRow(index)" v-if="index>0">
                                    <i class="fa fa-trash"></i>
                                </div>
                            </div>
                            <div class="col-md-2 box-btn">
                                <div class="btn btn-block"
                                     :class="article_variant.stato=='Y' ? 'btn-success' : 'btn-danger'"
                                     :title="article_variant.stato=='Y' ? 'Articolo visibile' : 'Articolo NON visibile'"
                                     @click="toggleStato(index)">
                                    <span v-if="article_variant.stato=='Y'">Visibile</span>
                                    <span v-if="article_variant.stato=='N'">Non visibile</span>
                                </div>
                            </div>
                            <div class="col-md-2 box-btn">
                                <div class="btn btn-block"
                                     :class="article_variant.flag_presente_articlesorders=='Y' ? 'btn-success' : 'btn-danger'"
                                     :title="article.flag_presente_articlesorders=='Y' ? 'Articolo ordinabile' : 'Articolo NON ordinabile'"
                                     @click="toggleFlagPresenteArticlesOrders(index)">
                                    <span v-if="article_variant.flag_presente_articlesorders=='Y'">Ordinabile</span>
                                    <span v-if="article_variant.flag_presente_articlesorders=='N'">Non ordinabile</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <label for="qta">Quantità</label> <label style="cursor: pointer;" class="label label-primary"
                                                                         data-toggle="modal" data-target="#modalQta">?</label>
                                <input type="number" id="qta" name="qta" class="form-control" v-model="article_variant.qta" placeholder="qta" @change="changeArticleVariant(event, index);setValidateVariants(event)" />
                                <div class="errors" v-if="article_variant.errors.qta" v-html="article_variant.errors.qta"></div>
                            </div>
                            <div class="col-md-2">
                                <label for="um">Unità di misura</label>
                                <select class="form-control" :required="true" v-model="article_variant.um" id="um"
                                        @change="changeArticleVariant(event, index);">
                                    <option v-for="um in ums"
                                            v-bind:value="um" >
                                        {{ um }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="prezzo">Prezzo</label>
                                <div class="input-group">
                                    <input type="number" id="prezzo" class="form-control" v-model="article_variant.prezzo" placeholder="Prezzo" @change="changeArticleVariant(event, index);" />
                                    <span class="input-group-addon"><i class="fa fa-euro"></i></span>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <label for="iva">Iva</label>
                                <select id="iva" class="form-control" :required="true" v-model="article_variant.iva" @change="changeArticleVariant(event, index);" >
                                    <option v-for="iva in ivas"
                                            v-bind:value="iva" >
                                        {{ iva }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="prezzo_finale">Prezzo finale</label>
                                <div class="input-group">
                                    <input type="text" id="prezzo_finale" name="prezzo_finale" class="form-control" v-model="article_variant.prezzo_finale" placeholder="Prezzo finale" disabled="disabled" />
                                    <span class="input-group-addon"><i class="fa fa-euro"></i></span>
                                </div>
                                <div class="errors" v-if="article_variant.errors.prezzo_finale" v-html="article_variant.errors.prezzo_finale"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="um_rif_values">Prezzo/UM (Unità di misura di riferimento)</label>
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
                            <div class="col-md-4">
                                <?php
                                $label = __('pezzi_confezione_long'). ' <span data-toggle="modal" data-target="#pezzi_confezione" class="badge badge-info"><i aria-hidden="true" class="fa fa-info"></i></span>';
                                echo $this->Form->control('pezzi_confezione', ['type' => 'number', 'label' => $label, 'min' => 1, 'v-model' => 'article_variant.pezzi_confezione', 'escape' => false]);
                                echo $this->HtmlCustom->drawModal('pezzi_confezione', 'Num di pezzi in una confezione', "Se il numero di pezzi per confezione è maggiore di 1 potrai gestire i colli con la funzione di 'validazione ordine'");
                                ?>
                            </div>
                            <div class="col-md-4">
                                <?php
                                $label = __('qta_minima'); //. ' <span data-toggle="modal" data-target="#qta_minima" class="badge badge-info"><i aria-hidden="true" class="fa fa-info"></i></span>';
                                echo $this->Form->control('qta_minima', ['type' => 'number', 'label' => $label, 'min' => 1, 'v-model' => 'article_variant.qta_minima', 'escape' => false]);
                                // echo $this->HtmlCustom->drawModal('qta_minima', 'Qta minima', "");
                                ?>
                            </div>
                            <div class="col-md-4">
                                <?php
                                $label = __('qta_massima'); //. ' <span data-toggle="modal" data-target="#qta_massima" class="badge badge-info"><i aria-hidden="true" class="fa fa-info"></i></span>';
                                echo $this->Form->control('qta_massima', ['type' => 'number', 'label' => $label, 'min' => 0, 'v-model' => 'article_variant.qta_massima', 'escape' => false]);
                                // echo $this->HtmlCustom->drawModal('qta_massima', 'Qta massima', "");
                                ?>
                            </div>
                        </div> <!-- row -->
                        <div class="row">
                            <div class="col-md-4">
                                <?php
                                $label = __('qta_multipli'). ' <span data-toggle="modal" data-target="#qta_multipli" class="badge badge-info"><i aria-hidden="true" class="fa fa-info"></i></span>';
                                echo $this->Form->control('qta_multipli', ['type' => 'number', 'label' => $label, 'min' => 1, 'v-model' => 'article_variant.qta_multipli', 'escape' => false]);
                                echo $this->HtmlCustom->drawModal('qta_multipli', 'Multipli di', "Gli utenti potranno acquistare nella quantità multipla indicata, per esempio se indichi 2, potranno acquistarne 2 o 4 o 6 ...");
                                ?>
                            </div>
                            <div class="col-md-4">
                                <?php
                                $label = __('qta_minima_order'). ' <span data-toggle="modal" data-target="#qta_minima_order" class="badge badge-info"><i aria-hidden="true" class="fa fa-info"></i></span>';
                                echo $this->Form->control('qta_minima_order', ['type' => 'number', 'label' => $label, 'min' => 0, 'v-model' => 'article_variant.qta_minima_order', 'escape' => false]);
                                echo $this->HtmlCustom->drawModal('qta_minima_order', 'Qtà minima rispetto a tutti gli acquisti', "Indicare la quantità minima che l'articolo deve raggiungere nel totale di tutti gli acquisti");
                                ?>
                            </div>
                            <div class="col-md-4">
                                <?php
                                $label = __('qta_massima_order'). ' <span data-toggle="modal" data-target="#qta_massima_order" class="badge badge-info"><i aria-hidden="true" class="fa fa-info"></i></span>';
                                echo $this->Form->control('qta_massima_order', ['type' => 'number', 'label' => $label, 'min' => 0, 'v-model' => 'article_variant.qta_massima_order', 'escape' => false]);
                                echo $this->HtmlCustom->drawModal('qta_massima_order', 'Qtà massima rispetto a tutti gli acquisti', "Arrivati alla quantità indicata, l'ordine sull'articolo sarà bloccato");
                                ?>
                            </div>
                        </div> <!-- row -->
                    </div>  <!-- v-for -->
                </template>

                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-success btn pull-right" style="margin-top: 25px"
                             @click="frmSubmit(event)">Salva dati dell'articolo
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top:15px">
                    <div class="col-md-12">
                        <div class="alert alert-danger alert-errors clearfix" v-if="display_errors.length>0">
                            <h3>Trovati {{display_errors.length}} errori!</h3>
                            <ul v-for="error in display_errors">
                                <li>{{ error }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                </div> <!-- v-if="supplier_organization.name!=null && supplier_organization.owner_articles!=\'REFERENT\'"

            </div>
            <!-- /.box-body -->

          <?php echo $this->Form->end(); ?>
        </div>
        <!-- /.box -->
      </div>
  </div>
  <!-- /.row -->
</section>

<?php echo $this->element('helps/modal-article-qta'); ?>

<style>
    label {
        margin-left: 5px;
        margin-right: 25px;
        font-weight: normal;
    }
    .errors {
        color: #fff;
        background-color: #dd4b39;
        padding: 5px 10px;
        border-radius: 5px;
        margin: 0 0 10px;
    }
    .alert-errors ul li {
        list-style-type: none;
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
    .badge-info {
        cursor: pointer;
        margin-left: 5px;
    }
    .box-btn {
        margin-top: 25px;
    }
</style>
