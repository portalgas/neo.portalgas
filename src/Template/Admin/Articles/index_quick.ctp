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
                      <tr :id="'frm-'+article.id">
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
                        <td>{{ article.suppliers_organization.name }}</td>
                        <td>
                          <select name="category_article_id" class="form-control" :required="true" v-model="article.category_article_id" >
                            <option v-for="(categories_article, id) in categories_articles" :value="id" v-html="$options.filters.html(categories_article)">
                            </option>
                          </select>  
                        </td>
                        <td>
                          <a href="#" @click="toggleIsBio(index)">
                            <img :class="article.bio=='N' ? 'no-bio': ''" :title="article.bio=='N' ? 'Articolo non biologico': 'Articolo biologico'" src="/img/is-bio.png" width="35" />
                          </a>
                        </td>
                        <td>
                          <!-- img :src="article.img1" :title="article.img1" width="50" / -->
                          <div class="dropzone" :id="'my-dropzone'+article.id" :data-attr-index="index"></div>
                        </td>
                        <td>
                        <input type="text" class="form-control" v-model="article.name" name="name" />
                        </td>
                        <td>
                          <input type="text" class="form-control" v-model="article.code" name="code" size="5" />
                        </td>
                        <td>
                          <input type="text" class="form-control" v-model="article.prezzo_" name="prezzo" />
                        </td>
                        <td>
                          <input type="text" class="form-control" v-model="article.qta" name="qta" />
                        </td>
                        <td>
                          <select class="form-control" :required="true" v-model="article.um">
                            <option v-for="um in ums"
                               v-bind:value="um" >
                              {{ um }}
                            </option>
                          </select>
                        </td>
                        <td>
                          <select class="form-control" :required="true" v-model="article.um_riferimento">
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
                        <th scope="col"><?= __('Nota') ?></th>
                        <th scope="col"><?= __('Ingredienti') ?></th>
                        <th scope="col"><?= __('qta_multipli') ?></th>
                        <th scope="col"><?= __('pezzi_confezione') ?></th>
                        <th scope="col"><?= __('qta_minima') ?></th>
                        <th scope="col"><?= __('qta_massima') ?></th>
                        <th scope="col"><?= __('qta_minima_order') ?></th>
                        <th scope="col"><?= __('qta_massima_order') ?></th>
                      </tr>
                      <tr style="display: none;" :class="'extra-'+index">
                        <td></td>
                        <td></td>
                        <td>
                          <textarea>{{ article.nota }}</textarea>
                        </td>
                        <td>
                          <textarea>{{ article.ingredienti }}</textarea>
                        </td>
                        <td>
                          <input type="number" class="form-control" min="1" v-model="article.qta_multipli" name="qta_multipli" />
                        </td>                        
                        <td>
                          <input type="number" class="form-control" min="1" v-model="article.pezzi_confezione" name="pezzi_confezione" />
                        <td>
                          <input type="number" class="form-control" min="1" v-model="article.qta_minima" name="qta_minima" />
                        </td>
                        <td>
                          <input type="number" class="form-control" min="0" v-model="article.qta_massima" name="qta_massima" />
                        </td>
                        <td>
                          <input type="number" class="form-control" min="0" v-model="article.qta_minima_order" name="qta_minima_order" />
                        </td>
                        <td>
                          <input type="number" class="form-control" min="0" v-model="article.qta_massima_order" name="qta_massima_order" />
                        </td>
                      </tr>                      
                </template>
                
          <?php 
          /*
                echo $this->Form->create(null, ['role' => 'form']);
                echo $this->Form->control('id', ['id' => 'article_id-'.$article['id'], 'type' => 'hidden', 'value' => $article['id']]);
                echo $this->Form->control('organization_id', ['id' => 'article_id-'.$article['organization_id'], 'type' => 'hidden', 'value' => $article['organization_id']]);

                echo '<tr>';
                echo '<td class="actions text-center">';
                echo $this->Form->button('<i class="fa fa-search-plus" aria-hidden="true"></i>', ['class'=>'btn btn-info', '@click' => 'toggleExtra($event, '.$article['organization_id'].', '.$article['id'].');']);
                echo '</td>';
                echo '<td class="actions text-center">';

                  if($article['stato']=='Y') {
                    $label = 'Attivo';
                    $css = 'primary';

                  } 
                  else {
                    $label = 'Non attivo';
                    $css = 'danger';
                  }
                  // echo $this->Form->button($label, ['class'=>'btn btn-'.$css]);
                  // echo '<br />';

                  if($article['flag_presente_articlesorders']=='Y') {
                    $label = 'Ordinabile';
                    $css = 'primary';

                  } 
                  else {
                    $label = 'Non ordinabile';
                    $css = 'danger';
                  }
                  echo $this->Form->button($label, ['class'=>'btn btn-'.$css]);
              echo '</td>';
              if(empty($search_supplier_organization_id))
                echo '<td>'.$article['suppliers_organization']['name'].'</td>';
              ?>
                  <td><?= $article['categories_article']['name']; ?></td>
                  <td><?= $this->Form->control('bio['.$article['organization_id'].']['.$article['id'].']', ['label' => false, 'type' => 'radio', 'options' => $si_no, 'value' => $article['bio']]) ?></td>
                  <td>
                    <?php 
                      echo $this->element('dropzone_article', ['article' => $article]);
                    ?>                      
                  </td>
                  <td><?= $this->Form->control('name', ['label' => false, 'value' => $article['name']]) ?></td>
                  <td><?= $this->Form->control('codice', ['label' => false, 'value' => $article['codice']]) ?></td>
                  <td><?= $this->Form->control('prezzo', ['label' => false, 'value' => $article['prezzo']]) ?></td>
                  <td><?= $this->Form->control('qta', ['label' => false, 'value' => $article['qta']]) ?></td>
                  <td><?= $this->Form->control('um', ['label' => false, 'value' => $article['um'], 'options' => $ums]) ?></td>
                  <td>
                    <?= $this->Form->control('um_riferimento', ['label' => false, 'value' => $article['um_riferimento'], 'options' => $ums]) ?>
                    <div id="um-label-<?php echo $article['organization_id'];?>-<?php echo $article['id'];?>"></div>
                  </td>
                </tr>
                <!-- extra -->
                <tr style="display: none;" class="extra-<?php echo $article['organization_id'];?>-<?php echo $article['id'];?>">
                  <th scope="col"></th>
                  <th scope="col"></th>
                  <th scope="col"><?= __('Nota') ?></th>
                  <th scope="col"><?= __('Ingredienti') ?></th>
                  <th scope="col"><?= __('pezzi_confezione') ?></th>
                  <th scope="col"><?= __('qta_minima') ?></th>
                  <th scope="col"><?= __('qta_massima') ?></th>
                  <th scope="col"><?= __('qta_minima_order') ?></th>
                  <th scope="col"><?= __('qta_massima_order') ?></th>
                  <th scope="col"><?= __('qta_multipli') ?></th>
                  <!-- th scope="col"><?= __('alert_to_qta') ?></th -->
                </tr>
                <tr style="display: none;" class="extra-<?php echo $article['organization_id'];?>-<?php echo $article['id'];?>">
                  <td></td>
                  <td></td>
                  <td><?= $this->Form->control('nota', ['type' => 'textarea', 'label' => false, 'value' => $article['nota']]) ?></td>
                  <td><?= $this->Form->control('ingredienti', ['type' => 'textarea', 'label' => false, 'value' => $article['ingredienti']]) ?></td>
                  <td><?= $this->Form->control('pezzi_confezione', ['label' => false, 'value' => $article['pezzi_confezione']]) ?></td>
                  <td><?= $this->Form->control('qta_minima', ['label' => false, 'value' => $article['qta_minima']]) ?></td>
                  <td><?= $this->Form->control('qta_massima', ['label' => false, 'value' => $article['qta_massima']]) ?></td>
                  <td><?= $this->Form->control('qta_minima_order', ['label' => false, 'value' => $article['qta_minima_order']]) ?></td>
                  <td><?= $this->Form->control('qta_massima_order', ['label' => false, 'value' => $article['qta_massima_order']]) ?></td>
                  <td><?= $this->Form->control('qta_multipli', ['label' => false, 'value' => $article['qta_multipli']]) ?></td>
                  <!-- td><?= $this->Form->control('alert_to_qta', ['label' => false, 'value' => $article['alert_to_qta']]) ?></td -->
                </tr>
              <?php 
                echo $this->Form->end(); 
              } // end foreach ($articles as $article)
              */
               ?>
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
</style>

<style>
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