<?php
use Cake\Core\Configure;
echo $this->Html->script('vue/articleOrders', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => __('ArticleOrders'), 'subtitle' => __('Management')], ['home']);

echo $this->HtmlCustomSite->boxOrder($order);
?>

<div id="vue-article-orders">
<form>
  <input type="hidden" name="organization_id" value="<?php echo $order->organization_id;?>" />
  <input type="hidden" name="order_type_id" value="<?php echo $order->order_type_id;?>" />
  <input type="hidden" name="order_id" value="<?php echo $order->id;?>" />

<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><?php echo __('List'); ?></h3>

          <div class="box-tools">
            <div v-if="!is_run" class="box-submit">
              <button v-if="!is_save" class="btn btn-primary" @click="save">Salva</button>
              <button v-if="is_save" class="btn btn-primary disabled">In elaborazione...</button>
            </div>             
          </div>
        </div>

        <div v-if="is_run" class="box-body table-responsive no-padding text-center" style="margin: 150px">
          <div><i class="fa-lg fa fa-spinner fa-spin"></i></div>
        </div>        
        <div v-if="!is_run" class="box-body table-responsive no-padding">
                 
          <?php 
          /*
           * articoli gia' associare
           */ 
          echo $this->HtmlCustomSite->boxTitle(['title' => __('Articoli già associati')]);
          ?>

          <?php
          echo '<div v-if="article_orders.length==0">'; 
          echo $this->element('msg', ['msg' => "Non ci sono ancora articoli associati all'ordine", 'class' => 'warning']);
          echo '</div>';
          ?>
            <table class="table table-hover" 
              v-if="article_orders.length>0">
              <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col" class="actions text-center">
                      <a class="btn btn-danger" :class="{ 'btn-deactive': select_article_orders_all }" @click="selectArticleOrdersAll">
                        <i class="fa fa-trash"></i></a>
                    </th>
                    <th scope="col" colspan="2"><?= __('Name') ?></th>
                    <th scope="col"><?= __('Prezzo') ?></th>
                    <th scope="col"><?= __('pezzi_confezione') ?></th>
                    <th scope="col"><?= __('qta_multipli') ?></th>
                    <th scope="col"><?= __('qta_minima') ?></th>
                    <th scope="col"><?= __('qta_massima') ?></th>
                    <th scope="col"><?= __('qta_minima_order') ?></th>
                    <th scope="col"><?= __('qta_massima_order') ?></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(article_order, index) in article_orders"
                  :key="article_order.article_id"
                >
                    <td>{{ (index + 1) }}</td>
                    <td class="actions text-center">
                      <a class="btn btn-danger" :class="{ 'btn-deactive': !article_order.is_select }" title="rimuovi l'articolo dall'ordine" @click="article_order.is_select=!article_order.is_select">
                        <i class="fa fa-trash fa-2xl"></i></a>
                    </td>
                    <td>
                      <img v-if="article_order.img1!=''" :src="article_order.img1" :width="article_order.img1_width" />
                    </td>	
                    <td>{{ article_order.name }}</td>
                    <td>
                    <div class="input-group">
                      <input :disabled="!can_edit" type="text" class="form-control" v-model="article_order.prezzo_" />
                      <span class="input-group-addon"><i class="fa fa-euro"></i></span>
                    </div>
                    </td>
                    <td>
                      <input :disabled="!can_edit" type="number" min="1" class="form-control" v-model="article_order.pezzi_confezione" />
                    </td>
                    <td>
                      <input type="number" min="1" class="form-control" v-model="article_order.qta_multipli" />
                    </td>
                    <td>
                      <input type="number" min="1" class="form-control" v-model="article_order.qta_minima" />
                    </td>
                    <td>
                      <input type="number" min="0" class="form-control" v-model="article_order.qta_massima" />
                    </td>
                    <td>
                      <input type="number" min="0" class="form-control" v-model="article_order.qta_minima_order" />
                    </td>
                    <td>
                      <input type="number" min="0" class="form-control" v-model="article_order.qta_massima_order" />
                    </td>
                  </tr>
              </tbody>
            </table>
          <?php 
          /*
           * articoli da associare
           */ 
          echo $this->HtmlCustomSite->boxTitle(['title' => __('Articoli da associare')]);

          echo '<div v-if="articles.length==0">'; 
          echo $this->element('msg', ['msg' => "Tutti gli articoli sono già associati all'ordine", 'class' => 'warning']);
          echo '</div>';
          ?>
              <table class="table table-hover"
                v-if="articles.length>0">
                <thead>
                  <tr>
                      <th scope="col"></th>
                      <th scope="col" class="actions text-center">
                        <a title="togli l'articolo dall'ordine" class="btn btn-success"  :class="{ 'btn-deactive': select_article_orders_all }" @click="selectArticlesAll">
                          <i class="fa fa-plus"></i></a>
                      </th>
                      <th scope="col" colspan="2"><?= __('Name') ?></th>
                      <th scope="col"><?= __('Prezzo') ?></th>
                      <th scope="col"><?= __('pezzi_confezione') ?></th>
                      <th scope="col"><?= __('qta_multipli') ?></th>
                      <th scope="col"><?= __('qta_minima') ?></th>
                      <th scope="col"><?= __('qta_massima') ?></th>
                      <th scope="col"><?= __('qta_minima_order') ?></th>
                      <th scope="col"><?= __('qta_massima_order') ?></th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(article, index) in articles"
                    :key="article.id"
                   >   
                      <td>{{ (index + 1) }}</td>
                      <td class="actions text-center">
                        <a class="btn btn-success" :class="{ 'btn-deactive': !article.is_select }" title="aggiungi articolo all'ordine" @click="article.is_select=!article.is_select">
                          <i class="fa fa-plus fa-2xl"></i></a>
                      </td>
                      <td>
                        <img v-if="article.img1!=''" :src="article.img1" :width="article.img1_width" />
                      </td>                                     
                      <td>{{ article.name }}</td>
                      <td>
                        <div class="input-group">
                          <input :disabled="!can_edit" type="text" class="form-control" v-model="article.prezzo_" />
                          <span class="input-group-addon"><i class="fa fa-euro"></i></span>
                        </div>
                      </td>
                      <td>
                        <input :disabled="!can_edit" type="number" min="1" class="form-control" v-model="article.pezzi_confezione" />
                      </td>
                      <td>
                        <input type="number" min="1" class="form-control" v-model="article.qta_multipli" />
                      </td>
                      <td>
                        <input type="number" min="1" class="form-control" v-model="article.qta_minima" />
                      </td>
                      <td>
                        <input type="number" min="0" class="form-control" v-model="article.qta_massima" />
                      </td>
                      <td>
                        <input type="number" min="0" class="form-control" v-model="article.qta_minima_order" />
                      </td>
                      <td>
                        <input type="number" min="0" class="form-control" v-model="article.qta_massima_order" />
                      </td>
                    </tr>
                </tbody>
              </table>

              <div class="box-submit">
                <button v-if="!is_save" class="btn btn-primary" @click="save">Salva</button>
                <button v-if="is_save" class="btn btn-primary disabled">In elaborazione...</button>                
              </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>
</section>
</form>
</div>

<style>
.box-submit {
  padding:5px;
  float: right !important;  
}
.btn-deactive {
  opacity: .45;
}
</style>