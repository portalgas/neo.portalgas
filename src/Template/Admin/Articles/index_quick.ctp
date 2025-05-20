<?php
use Cake\Core\Configure;
use App\Traits;

$user = $this->Identity->get();

$js = "var categories_articles = $js_categories_articles;";
/*
  * se l'elenco dei produttori ha un solo elemento (ex produttore) lo imposto gia'
  */
if(!empty($search_supplier_organization_id))
    $js .= "var search_supplier_organization_id_default = $search_supplier_organization_id;";
if(!empty($search_id))
    $js .= "var search_id = $search_id;";
else
    $js .= "var search_id = '';";
$this->Html->scriptBlock($js, ['block' => true]);
echo $this->Html->script('vue/utils.js?v=20250520', ['block' => 'scriptPageInclude']);
echo $this->Html->script('vue/articles.js?v=20250520', ['block' => 'scriptPageInclude']);
echo $this->Html->script('dropzone/dropzone.min', ['block' => 'scriptInclude']);
echo $this->Html->css('dropzone/dropzone.min', ['block' => 'css']);
?>
<section class="content-header">
  <h1>
    Gestione articoli
    <div class="pull-right">
      <a href="<?php echo $this->HtmlCustomSite->jLink('Articles', 'context_articles_add');?>" title="Aggiungi un nuovo articolo" target="_blank">
        <button class="btn btn-success" style="opacity: 0.8;"><i aria-hidden="true" class="fa fa-plus"></i> Aggiungi un nuovo articolo (precedente versione)</button></a>
        <a href="/admin/articles/view/" title="Aggiungi un nuovo articolo">
        <button class="btn btn-success"><i aria-hidden="true" class="fa fa-plus"></i> Aggiungi un nuovo articolo (nuova versione)</button></a>
    </div>
  </h1>
</section>

<div id="vue-articles">
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <?php
        echo $this->element('search/articles', ['search_orders' => $search_orders]);
      ?>
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
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">

          <!-- loader globale -->
          <div class="loader-global" v-if="is_run">
            <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
          </div>

          <?php
          echo '<div v-if="articles.length==0">';
          echo $this->element('msg', ['msg' => "Non sono stati trovati articoli", 'class' => 'warning']);
          echo '</div>';
          ?>

          <table class="table table-hover" v-if="!is_run && articles.length>0">
            <thead>
              <tr>
                  <th scope="col" colspan="2" class="actions text-center"></th>
                  <th scope="col"><?= __('Name') ?></th>
                  <th scope="col"><?= __('Code') ?></th>
                  <?php
                  if(empty($search_supplier_organization_id))
                    echo '<th scope="col">'.__('supplier_organization_id').'</th>';
                  if($user->organization->paramsFields['hasFieldArticleCategoryId']=='Y') {
                    echo '<th scope="col">';
                    echo __('Category');
                    echo ' <label style="cursor: pointer;" class="label label-primary"
                          data-toggle="modal" data-target="#modalCategorie">?</label>';
                    echo '</th>';
                  }
                  ?>
                  <th scope="col"><?= __('Bio') ?></th>
                  <th scope="col"></th>
                  <th scope="col" style="width:100px">
                      <?= __('qta') ?>
                      <label style="cursor: pointer;" class="label label-primary"
                             data-toggle="modal" data-target="#modalQta">?</label>
                  </th>
                  <th scope="col" style="width:100px">
                      <?= __('Prezzo') ?>
                      <br />
                      <label style="cursor: pointer;" class="label"
                            :class="open_box_price ? 'label-success' : 'label-info'"
                            @click="openBoxPrice()">
                        <span v-if="open_box_price">Prezzo senza IVA</span>
                        <span v-if="!open_box_price">Prezzo compreso IVA</span>
                      </label>
                  </th>
                  <th scope="col" style="width:150px"><?= __('UM') ?></th>
              </tr>
            </thead>
            <tbody>
                  <template
                      v-for="(article, index) in articles"
                      :key="article.id"
                    >
                      <!-- EDIT -->
                      <template v-if="article.can_edit">
                      <tr :id="'frm-'+article.id" >
                        <td class="actions text-center">
                          <!-- {{ article.organization_id }} {{ article.id }} -->
                          <div class="btn-group-vertical">
                              <?php
                              if($user->organization->type=='GAS')
                                echo '<button title="dettaglio acquisti" class="btn btn-info" @click="modalInCarts(index)"><i aria-hidden="true" class="fa fa-info"></i></button>';
                              echo '<button title="campi aggiuntivi" class="btn btn-info" @click="toggleExtra(index)"><i aria-hidden="true" class="fa fa-search-plus"></i></button>';
                              echo '<button title="copia aticolo" class="btn btn-success" @click="goToCopy(index)"><i aria-hidden="true" class="fa fa-copy"></i></button>';
                              if($user->organization->type=='GAS')
                                  echo '<button title="elimina articolo" class="btn btn-danger"  @click="goToDelete(index)"><i aria-hidden="true" ><i aria-hidden="true" class="fa fa-trash"></i></button>';
                              ?>
                          </div>
                        </td>
                        <td class="actions text-center">
                          <button class="btn-block btn"
                                :class="article.flag_presente_articlesorders=='Y' ? 'btn-success' : 'btn-danger'"
                                :title="article.flag_presente_articlesorders=='Y' ? 'Articolo ordinabile' : 'Articolo NON ordinabile'"
                                @click="toggleFlagPresenteArticlesOrders('flag_presente_articlesorders-'+article.organization_id+'-'+article.id, index)">
                            <span v-if="article.flag_presente_articlesorders=='Y'">Ordinabile</span>
                            <span v-if="article.flag_presente_articlesorders=='N'">Non ordinabile</span>
                          </button>
                            <?php
                            if($user->organization->type=='GAS') {
                            ?>
                                <button class="btn-block btn"
                                        :class="article.stato=='Y' ? 'btn-success' : 'btn-danger'"
                                        :title="article.stato=='Y' ? 'Articolo visibile' : 'Articolo NON visibile'"
                                        @click="toggleStato('stato-'+article.organization_id+'-'+article.id, index)">
                                    <span v-if="article.stato=='Y'">Visibile</span>
                                    <span v-if="article.stato=='N'">Non visibile</span>
                                </button>
                            <?php
                            }
                            ?>
                        </td>
                        <td>
                          <input type="text" class="form-control extend" v-model="article.name" @change="changeValue(event, index)" :id="'name-'+article.organization_id+'-'+article.id" name="name" />
                          <span class="label label-primary" style="white-space: unset;font-size: 85%;">{{ article.name }}</span>
                        </td>
                        <td>
                          <input type="text" class="form-control extend" v-model="article.codice" @change="changeValue(event, index)" :id="'codice-'+article.organization_id+'-'+article.id" name="codice" size="5" />
                        </td>
                        <?php
                        if(empty($search_supplier_organization_id))
                            echo '<td>
                              {{ article.owner_supplier_organization.name }}
                              <div class="small">
                                <b>'.__('organization_owner_article_short').'</b>:
                                <div v-if="article.can_edit">Il referente del tuo G.A.S.</div>
                                <div v-if="!article.can_edit">{{ article.organization.name }}</div>
                              </div>
                            </td>';

                        if($user->organization->paramsFields['hasFieldArticleCategoryId']=='Y') {
                        ?>
                          <td>
                            <span v-if="article.categories_article!=null">{{ article.categories_article.name }}</span>
                            <select
                                v-if="Object.keys(categories_articles).length>1"
                                name="category_article_id"
                                class="form-control extend"
                                :required="true"
                                v-model="article.category_article_id"
                                @change="changeValue(event, index)" :id="'category_article_id-'+article.organization_id+'-'+article.id">
                              <option v-for="categories_article in categories_articles" :value="categories_article.id" v-html="$options.filters.html(categories_article.name)">
                              </option>
                            </select>
                          </td>
                        <?php
                        }
                        ?>
                        <td>
                          <a @click="toggleIsBio('bio-'+article.organization_id+'-'+article.id, index)" :id="'bio-'+article.organization_id+'-'+article.id" style="cursor: pointer;">
                            <img :class="article.bio=='N' ? 'no-bio': ''" :title="article.bio=='N' ? 'Articolo non biologico': 'Articolo biologico'" src="/img/is-bio.png" width="35" />
                          </a>
                        </td>
                        <td>
                          <!-- img :src="article.img1" :title="article.img1" width="50" / -->
                          <div class="dropzone" :id="'my-dropzone'+article.organization_id+'-'+article.id" :data-attr-index="index"></div>
                        </td>
                          <td>
                              <input type="text" class="form-control" v-model="article.qta" @change="changeValue(event, index)" :id="'qta-'+article.organization_id+'-'+article.id" name="qta" />
                          </td>
                        <!-- price -->
                        <td>
                          <template v-if="!open_box_price">
                            <input type="text" class="form-control"
                                    v-model="article.prezzo_"
                                    @change="changeValue(event, index)"
                                    :id="'prezzo-'+article.organization_id+'-'+article.id"
                                    name="prezzo" />
                          </template>
                          <template v-if="open_box_price">
                            <div class="input form-group">
                              <label for="iva">Prezzo senza IVA</label>
                              <input type="text" class="form-control"
                                    @change="setPriceConIva(index)"
                                    :id="'prezzo_no_iva-'+article.organization_id+'-'+article.id"
                                    name="prezzo_no_iva" />
                            </div>
                            <div class="input form-group select">
                              <!-- label for="iva">Iva</label -->
                              <select name="iva"
                                v-model="iva"
                                @change="setPriceConIva(index)"
                                :id="'iva-'+article.organization_id+'-'+article.id" class="form-control">
                                <!-- option value="0" selected="selected">Compresa nel prezzo</option -->
                                <option value="4">4% iva</option>
                                <option value="10" selected="selected">10% iva</option>
                                <option value="22">22% iva</option>
                              </select>
                            </div>

                            <div class="input form-group">
                              <label for="iva">Prezzo finale</label>
                              <input type="text" class="form-control"
                                      v-model="article.prezzo_"
                                      @change="changeValue(event, index)"
                                      :id="'prezzo-'+article.organization_id+'-'+article.id"
                                      name="prezzo" />
                            </div>
                          </template>
                        </td>
                        <td>
                          <select class="form-control" :required="true" v-model="article.um" @change="changeValue(event, index); changeUM(event, index);" :id="'um-'+article.organization_id+'-'+article.id" name="um">
                            <option v-for="um in ums"
                               v-bind:value="um" >
                              {{ um }}
                            </option>
                          </select>

                          <!-- select class="form-control" :required="true" v-model="article.um_riferimento" @change="changeValue(event, index)" :id="'um_riferimento-'+article.organization_id+'-'+article.id" name="um_riferimento">
                            <option v-for="um in ums"
                               v-bind:value="um" >
                              {{ um }}
                            </option>
                          </select -->

                          <div v-if="article.um_rif_values.length>0">
                            <div class="form-check" v-for="um_rif_value in article.um_rif_values">
                              <input class="form-check-input" type="radio"
                                     name="um_riferimento"
                                     v-model="article.um_riferimento"
                                     @change="changeValue(event, index)"
                                     :id="'um_rif_values-'+um_rif_value.id"
                                     :value="um_rif_value.id">
                              <label class="form-check-label"
                                     :for="'um_rif_values-'+um_rif_value.id"
                                     v-html="$options.filters.html(um_rif_value.value)">
                              </label>
                            </div>
                          </div>
                          <div v-if="article.um_rif_values.length==0">
                            {{ article.um_rif_label }}
                          </div>

                          <!-- select class="form-control"
                              v-if="article.um_rif_values.length>0"
                              :required="true"
                              v-model="article.um_riferimento"
                              @change="changeValue(event, index)"
                              :id="'um_riferimento-'+article.organization_id+'-'+article.id" name="um_riferimento">
                            <option v-for="um_rif_value in article.um_rif_values"
                                    :value="um_rif_value.id"
                                    v-html="$options.filters.html(um_rif_value.value)"></option>
                          </select -->
                        </td>

                      </tr>
                      <!-- extra -->
                      <tr style="display: none;" :class="'extra-'+index">
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col" colspan="<?php echo ($user->organization->paramsFields['hasFieldArticleCategoryId']=='Y') ? '4': '3';?>"><?= __('Nota') ?></th>
                        <th scope="col" colspan="3"><?= __('Ingredienti') ?></th>
                        <th scope="col" colspan="2"></th>
                      </tr>
                      <tr style="display: none;" :class="'extra-'+index">
                        <td></td>
                        <?php
                        if($user->organization->paramsFields['hasFieldArticleCategoryId']=='Y')
                          echo '<td></td>';
                        ?>
                        <td colspan="3">
                          <textarea rows="10" class="form-control extend" @change="changeValue(event, index)" name="nota" :id="'nota-'+article.organization_id+'-'+article.id" >{{ article.nota }}</textarea>
                        </td>
                        <td colspan="3">
                          <textarea rows="10" class="form-control extend" @change="changeValue(event, index)" name="ingredienti" :id="'ingredienti-'+article.organization_id+'-'+article.id" >{{ article.ingredienti }}</textarea>
                        </td>
                        <td colspan="4">
                          <div>
                              <label><?= __('qta_multipli'). ' <span data-toggle="modal" data-target="#qta_multipli" class="badge badge-info"><i aria-hidden="true" class="fa fa-info"></i></span>'; ?></label>&nbsp;
                              <input type="number" class="form-control" min="1" v-model="article.qta_multipli" @change="changeValue(event, index)" :id="'qta_multipli-'+article.organization_id+'-'+article.id" name="qta_multipli" />
                          </div>
                          <div>
                              <label><?= __('pezzi_confezione_long'). ' <span data-toggle="modal" data-target="#pezzi_confezione" class="badge badge-info"><i aria-hidden="true" class="fa fa-info"></i></span>'; ?></label>&nbsp;
                              <input type="number" class="form-control" min="1" v-model="article.pezzi_confezione" @change="changeValue(event, index)" :id="'pezzi_confezione-'+article.organization_id+'-'+article.id" name="pezzi_confezione" /></div>
                          <div><label><?= __('qta_minima') ?></label> <input type="number" class="form-control" min="1" v-model="article.qta_minima" @change="changeValue(event, index)" :id="'qta_minima-'+article.organization_id+'-'+article.id" name="qta_minima" /></div>
                          <div><label><?= __('qta_massima') ?></label> <input type="number" class="form-control" min="0" v-model="article.qta_massima" @change="changeValue(event, index)" :id="'qta_massima-'+article.organization_id+'-'+article.id" name="qta_massima" /></div>
                          <div>
                              <label><?= __('qta_minima_order'). ' <span data-toggle="modal" data-target="#qta_minima_order" class="badge badge-info"><i aria-hidden="true" class="fa fa-info"></i></span>'; ?></label>&nbsp;
                              <input type="number" class="form-control" min="0" v-model="article.qta_minima_order" @change="changeValue(event, index)" :id="'qta_minima_order-'+article.organization_id+'-'+article.id" name="qta_minima_order" /></div>
                          <div>
                              <label><?= __('qta_massima_order'). ' <span data-toggle="modal" data-target="#qta_massima_order" class="badge badge-info"><i aria-hidden="true" class="fa fa-info"></i></span>'; ?></label>&nbsp;
                              <input type="number" class="form-control" min="0" v-model="article.qta_massima_order" @change="changeValue(event, index)" :id="'qta_massima_order-'+article.organization_id+'-'+article.id" name="qta_massima_order" /></div>
                        </td>
                      </tr>
                    </template>

                    <!-- NOT EDIT -->
                    <template v-if="!article.can_edit">
                    <tr :id="'frm-'+article.id" >
                        <td class="actions text-center">
                          <!-- {{ article.id }} {{ article.organization_id }} -->
                          <div class="btn-group-vertical">
                            <button title="dettaglio acquisti" class="btn btn-info" @click="modalInCarts(index)"><i aria-hidden="true" class="fa fa-info"></i></button>
                            <button title="campi aggiuntivi" class="btn btn-info" @click="toggleExtra(index)"><i aria-hidden="true" class="fa fa-search-plus"></i></button>
                          </div>
                        </td>
                        <td class="actions text-center">
                          <button style="cursor: auto !important"
                                class="btn-block btn"
                                :class="article.flag_presente_articlesorders=='Y' ? 'btn-success' : 'btn-danger'"
                                :title="article.flag_presente_articlesorders=='Y' ? 'Articolo ordinabile' : 'Articolo NON ordinabile'">
                            <span v-if="article.flag_presente_articlesorders=='Y'">Ordinabile</span>
                            <span v-if="article.flag_presente_articlesorders=='N'">Non ordinabile</span>
                          </button>
                        </td>
                        <td>
                          {{ article.name }}
                        </td>
                        <td>
                          {{ article.codice }}
                        </td>
                        <?php
                        if(empty($search_supplier_organization_id))
                            echo '<td>
                              {{ article.owner_supplier_organization.name }}
                              <div class="small">
                                <b>'.__('organization_owner_article_short').'</b>:
                                <div v-if="article.can_edit">Il referente del tuo G.A.S.</div>
                                <div v-if="!article.can_edit">{{ article.organization.name }}</div>
                              </div>
                            </td>';
                        if($user->organization->paramsFields['hasFieldArticleCategoryId']=='Y') {
                        ?>
                          <td>
                            <span v-if="article.categories_article!=null">{{ article.categories_article.name }}</span>
                          </td>
                        <?php
                        }
                        ?>
                        <td>
                          <img :class="article.bio=='N' ? 'no-bio': ''" :title="article.bio=='N' ? 'Articolo non biologico': 'Articolo biologico'" src="/img/is-bio.png" width="35" />
                        </td>
                        <td>
                          <img :src="article.img1" :title="article.img1" width="50" />
                        </td>
                        <td>
                          {{ article.prezzo | currency }} &euro;
                        </td>
                        <td>
                          {{ article.qta }}
                        </td>
                        <td>
                          {{ article.um }}
                          <div v-if="article.um_rif_values.length==0">
                            {{ article.um_rif_label }}
                          </div>
                        </td>
                      </tr>
                      <!-- extra -->
                      <tr style="display: none;" :class="'extra-'+index">
                        <th scope="col"></th>
                        <th scope="col" colspan="<?php echo ($user->organization->paramsFields['hasFieldArticleCategoryId']=='Y') ? '4': '3';?>"><?= __('Nota') ?></th>
                        <th scope="col" colspan="3"><?= __('Ingredienti') ?></th>
                        <th scope="col" colspan="4"></th>
                      </tr>
                      <tr style="display: none;" :class="'extra-'+index">
                        <td></td>
                        <td colspan="<?php echo ($user->organization->paramsFields['hasFieldArticleCategoryId']=='Y') ? '4': '3';?>">
                          {{ article.nota }}
                        </td>
                        <td colspan="3">
                          {{ article.ingredienti }}
                        </td>
                        <td colspan="4">
                          <div><label><?= __('qta_multipli') ?></label>: {{ article.qta_multipli }}</div>
                          <div><label><?= __('pezzi_confezione') ?></label>: {{ article.pezzi_confezione }}</div>
                          <div><label><?= __('qta_minima') ?></label>: {{ article.qta_minima }}</div>
                          <div><label><?= __('qta_massima') ?></label>: {{ article.qta_massima }}</div>
                          <div><label><?= __('qta_minima_order') ?></label>: {{ article.qta_minima_order }}</div>
                          <div><label><?= __('qta_massima_order') ?></label>: {{ article.qta_massima_order }}</div>
                        </td>
                      </tr>
                    </template>
                </template>
            </tbody>
          </table>

          <div v-if="is_run_paginator" class="box-spinner">
              <div class="spinner-border text-info" role="status">
                  <span class="sr-only">Loading...</span>
              </div>
          </div>

        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>
</section>

    <?php
    echo $this->HtmlCustom->drawModal('pezzi_confezione', 'Num di pezzi in una confezione', "Se il numero di pezzi per confezione è maggiore di 1 potrai gestire i colli con la funzione di 'validazione ordine'");

    echo $this->HtmlCustom->drawModal('qta_multipli', 'Multipli di', "Gli utenti potranno acquistare nella quantità multipla indicata, per esempio se indichi 2, potranno acquistarne 2 o 4 o 6 ...");
    echo $this->HtmlCustom->drawModal('qta_minima_order', 'Qtà minima rispetto a tutti gli acquisti', "Indicare la quantità minima che l'articolo deve raggiungere nel totale di tutti gli acquisti");
    echo $this->HtmlCustom->drawModal('qta_massima_order', 'Qtà massima rispetto a tutti gli acquisti', "Arrivati alla quantità indicata, l'ordine sull'articolo sarà bloccato");

    ?>

    <?php echo $this->element('helps/modal-article-qta'); ?>

    <!-- Modal categorie -->
    <div class="modal fade" id="modalCategorie" tabindex="-1" aria-labelledby="modalCategorieLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalCategorieLabel">Gestione categorie</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <?php
            if($user->acl['isManager'] || $user->acl['isSuperReferente']) {
              echo '<a href="'.$this->HtmlCustomSite->jLink('CategoriesArticles', 'index').'" title="gestisci le categorie degli articoli" target="_blank">';
              echo '<button class="btn btn-info"><i aria-hidden="true" class="fa fa-tags"></i> Clicca qui se vuoi gestire le categorie degli articoli</button></a>';
            }
            else {
                echo "Contatta il manager del tuo G.A.S. se desideri avere delle categorie di articoli diversi";
            } // if($user->acl['isManager'] || $user->acl['isSuperReferente'])
            ?>
          </div> <!-- modal-body -->
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Chiudi</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal article_in_carts -->
    <div class="modal fade" id="modalArticleInCarts" tabindex="-1" aria-labelledby="modalArticleInCartsLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalArticleInCartsLabel">Gasisti che hanno acquistato l'articolo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">


          <table class="table table-hover" v-if="article_in_carts.length>0">
                <thead>
                  <tr>
                    <th scope="col">Utente</th>
                    <th scope="col" class="text-center">Quantità</th>
                    <th scope="col" class="text-center">Importo</th>
                    <th scope="col">Acquistato il</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-for="(article_in_cart, delivery_id) in article_in_carts"
                          :key="delivery_id">
                    <tr>
                        <td colspan="4" class="trGroup">
                          Consegna: {{ article_in_cart.delivery.label }}
                        </td>
                    </tr>
                    <tr v-for="(cart, index) in article_in_cart.delivery.carts">
                      <td>{{ cart.user.name}}</td>
                      <td class="text-center">{{ cart.final_qta}}</td>
                      <td class="text-center">{{ cart.final_price | currency }} &euro;</td>
                      <td>{{ cart.date_human}}</td>
                    </tr>
                  </template>
                </tbody>
              </table>

              <div class="alert alert-info" v-if="article_in_carts.length==0">
                Non ci sono acquisti associati all'articolo
              </div>

          </div> <!-- modal-body -->
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Chiudi</button>
          </div>
        </div>
      </div>
    </div>


</div> <!-- vue-articles -->

<style>
    .no-bio {
        opacity: 0.1
    }
    .extend:focus{
        width:320px;
    }

    .autocomplete {
        position: relative;
    }
    .autocomplete-results {
        padding: 0;
        margin: 0;
        border: 1px solid #eeeeee;
        height: 120px;
        min-height: 1em;
        max-height: 6em;
        overflow: auto;
        width: 500px;
    }
    .autocomplete-result {
        list-style: none;
        text-align: left;
        padding: 4px 2px;
        cursor: pointer;
    }
    .autocomplete-result.is-active,
    .autocomplete-result:hover {
        background-color:#367fa9;
        color: white;
    }
    .modal-title {
        color: #3c8dbc;
        font-size: 22px;
        font-weight: bold;
    }
    .badge-info {
        cursor: pointer;
        margin-left: 5px;
    }
</style>
