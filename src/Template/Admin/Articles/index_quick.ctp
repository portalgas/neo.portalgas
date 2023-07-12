<?php 
use Cake\Core\Configure;
use App\Traits;

$js = "var categories_articles = $js_categories_articles";
$this->Html->scriptBlock($js, ['block' => true]);
echo $this->Html->script('vue/articles', ['block' => 'scriptPageInclude']);

echo $this->Html->script('dropzone/dropzone.min', ['block' => 'scriptInclude']); 
echo $this->Html->css('dropzone/dropzone.min', ['block' => 'css']); 
?>
<section class="content-header">
  <h1>
    <?php echo __('Articles');?>
  </h1>
</section>

<div id="vue-articles">
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <?php 
        echo $this->element('search/articles');
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
                  <th scope="col" colspan="2" class="actions text-center"><?= __('Actions') ?></th>
                  <?php 
                  if(empty($search_supplier_organization_id))
                    echo '<th scope="col">'.__('supplier_organization_id').'</th>';
                  ?>
                  <th scope="col"><?= __('Category') ?></th>
                  <th scope="col"><?= __('bio') ?></th>
                  <th scope="col"><?= __('img1') ?></th>
                  <th scope="col"><?= __('name') ?></th>
                  <th scope="col"><?= __('codice') ?></th>
                  <th scope="col" style="width:100px"><?= __('prezzo') ?></th>
                  <th scope="col" style="width:100px"><?= __('qta') ?></th>
                  <th scope="col" style="width:100px"><?= __('um') ?></th>
                  <th scope="col" style="width:100px"><?= __('um_riferimento') ?></th>
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
                          <!-- {{ article.id }} {{ article.organization_id }} -->
                          <button class="btn btn-info" @click="toggleExtra(index)"><i aria-hidden="true" class="fa fa-search-plus"></i></button>
                        </td>
                        <td class="actions text-center">
                          <button class="btn-block btn" 
                                :class="article.flag_presente_articlesorders=='Y' ? 'btn-success' : 'btn-danger'" 
                                :title="article.flag_presente_articlesorders=='Y' ? 'Articolo ordinabile' : 'Articolo NON ordinabile'" 
                                @click="toggleFlagPresenteArticlesOrders(index)">
                            <span v-if="article.flag_presente_articlesorders=='Y'">Ordinabile</span>
                            <span v-if="article.flag_presente_articlesorders=='N'">Non ordinabile</span>
                          </button>
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
                        ?>
                        <td>
                          <select name="category_article_id" class="form-control extend" :required="true" v-model="article.category_article_id" @change="changeValue(event, index)" :id="'category_article_id-'+article.organization_id+'-'+article.id">
                            <option v-for="(categories_article, id) in categories_articles" :value="id" v-html="$options.filters.html(categories_article)">
                            </option>
                          </select>  
                        </td>
                        <td>
                          <a href="#" @click="toggleIsBio('bio-'+article.organization_id+'-'+article.id, index)" :id="'bio-'+article.organization_id+'-'+article.id">
                            <img :class="article.bio=='N' ? 'no-bio': ''" :title="article.bio=='N' ? 'Articolo non biologico': 'Articolo biologico'" src="/img/is-bio.png" width="35" />
                          </a>
                        </td>
                        <td>
                          <!-- img :src="article.img1" :title="article.img1" width="50" / -->
                          <div class="dropzone" :id="'my-dropzone'+article.id" :data-attr-index="index"></div>
                        </td>
                        <td>
                        <input type="text" class="form-control extend" v-model="article.name" @change="changeValue(event, index)" :id="'name-'+article.organization_id+'-'+article.id" name="name" />
                        </td>
                        <td>
                          <input type="text" class="form-control extend" v-model="article.codice" @change="changeValue(event, index)" :id="'codice-'+article.organization_id+'-'+article.id" name="codice" size="5" />
                        </td>
                        <td>
                          <input type="text" class="form-control" v-model="article.prezzo_" @change="changeValue(event, index)" :id="'prezzo-'+article.organization_id+'-'+article.id" name="prezzo" />
                        </td>
                        <td>
                          <input type="text" class="form-control" v-model="article.qta" @change="changeValue(event, index)" :id="'qta-'+article.organization_id+'-'+article.id" name="qta" />
                        </td>
                        <td>
                          <select class="form-control" :required="true" v-model="article.um" @change="changeValue(event, index)" :id="'um-'+article.organization_id+'-'+article.id">
                            <option v-for="um in ums"
                               v-bind:value="um" >
                              {{ um }}
                            </option>
                          </select>
                        </td>
                        <td>
                          <select class="form-control" :required="true" v-model="article.um_riferimento" @change="changeValue(event, index)" :id="'um_riferimento-'+article.organization_id+'-'+article.id" >
                            <option v-for="um in ums"
                               v-bind:value="um" >
                              {{ um }}
                            </option>
                          </select>                          
                          <div>{{ um_label(index) }}</div>
                        </td>

                      </tr>
                      <!-- extra -->
                      <tr style="display: none;" :class="'extra-'+index">
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col" colspan="4"><?= __('Nota') ?></th>
                        <th scope="col" colspan="3"><?= __('Ingredienti') ?></th>
                        <th scope="col" colspan="3"></th>
                      </tr>
                      <tr style="display: none;" :class="'extra-'+index">
                        <td></td>
                        <td></td>
                        <td colspan="4">
                          <textarea rows="10" class="form-control extend" @change="changeValue(event, index)" name="nota" :id="'nota-'+article.organization_id+'-'+article.id" >{{ article.nota }}</textarea>
                        </td>
                        <td colspan="3">
                          <textarea rows="10" class="form-control extend" @change="changeValue(event, index)" name="ingredienti" :id="'ingredienti-'+article.organization_id+'-'+article.id" >{{ article.ingredienti }}</textarea>
                        </td>
                        <td colspan="3">
                          <div><label><?= __('qta_multipli') ?></label> <input type="number" class="form-control" min="1" v-model="article.qta_multipli" @change="changeValue(event, index)" :id="'qta_multipli-'+article.organization_id+'-'+article.id" name="qta_multipli" /></div>
                          <div><label><?= __('pezzi_confezione') ?></label> <input type="number" class="form-control" min="1" v-model="article.pezzi_confezione" @change="changeValue(event, index)" :id="'pezzi_confezione-'+article.organization_id+'-'+article.id" name="pezzi_confezione" /></div>
                          <div><label><?= __('qta_minima') ?></label> <input type="number" class="form-control" min="1" v-model="article.qta_minima" @change="changeValue(event, index)" :id="'qta_minima-'+article.organization_id+'-'+article.id" name="qta_minima" /></div>
                          <div><label><?= __('qta_massima') ?></label> <input type="number" class="form-control" min="0" v-model="article.qta_massima" @change="changeValue(event, index)" :id="'qta_massima-'+article.organization_id+'-'+article.id" name="qta_massima" /></div>
                          <div><label><?= __('qta_minima_order') ?></label> <input type="number" class="form-control" min="0" v-model="article.qta_minima_order" @change="changeValue(event, index)" :id="'qta_minima_order-'+article.organization_id+'-'+article.id" name="qta_minima_order" /></div>
                          <div><label><?= __('qta_massima_order') ?></label> <input type="number" class="form-control" min="0" v-model="article.qta_massima_order" @change="changeValue(event, index)" :id="'qta_massima_order-'+article.organization_id+'-'+article.id" name="qta_massima_order" /></div> 
                        </td>
                      </tr>
                    </template>

                    <!-- NOT EDIT -->
                    <template v-if="!article.can_edit">
                    <tr :id="'frm-'+article.id" >
                        <td class="actions text-center">
                          <!-- {{ article.id }} {{ article.organization_id }} -->
                          <button class="btn btn-info" @click="toggleExtra(index)"><i aria-hidden="true" class="fa fa-search-plus"></i></button>
                        </td>
                        <td class="actions text-center">
                          <button class="btn-block btn" 
                                :class="article.flag_presente_articlesorders=='Y' ? 'btn-success' : 'btn-danger'" 
                                :title="article.flag_presente_articlesorders=='Y' ? 'Articolo ordinabile' : 'Articolo NON ordinabile'">
                            <span v-if="article.flag_presente_articlesorders=='Y'">Ordinabile</span>
                            <span v-if="article.flag_presente_articlesorders=='N'">Non ordinabile</span>
                          </button>
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
                        ?>
                        <td>
                          {{ article.categories_article.name }}
                        </td>
                        <td>
                          <img :class="article.bio=='N' ? 'no-bio': ''" :title="article.bio=='N' ? 'Articolo non biologico': 'Articolo biologico'" src="/img/is-bio.png" width="35" />
                        </td>
                        <td>
                          <img :src="article.img1" :title="article.img1" width="50" />
                        </td>
                        <td>
                          {{ article.name }}
                        </td>
                        <td>
                          {{ article.codice }}
                        </td>
                        <td>
                          {{ article.prezzo | currency }} &euro;
                        </td>
                        <td>
                          {{ article.qta }}
                        </td>
                        <td>
                          {{ article.um }}
                        </td>
                        <td>
                          {{ article.um_riferimento }}          
                          <div>{{ um_label(index) }}</div>
                        </td>

                      </tr>
                      <!-- extra -->
                      <tr style="display: none;" :class="'extra-'+index">
                        <th scope="col"></th>
                        <th scope="col" colspan="4"><?= __('Nota') ?></th>
                        <th scope="col" colspan="3"><?= __('Ingredienti') ?></th>
                        <th scope="col" colspan="4"></th>
                      </tr>
                      <tr style="display: none;" :class="'extra-'+index">
                        <td></td>
                        <td colspan="4">
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
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>
</section>
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
</style>