<?php
use Cake\Core\Configure;
$config = Configure::read('Config');
$portalgas_bo_url = $config['Portalgas.bo.url'];

echo $this->Html->script('vue/articleOrders', ['block' => 'scriptPageInclude']);

echo $this->element('menu-order', ['order' => $order]);

echo $this->HtmlCustomSite->boxTitle(['title' => __('ArticleOrders'), 'subtitle' => __('Management')], ['home', 'list-orders'], $order);

echo $this->HtmlCustomSite->boxOrder($order);
?>

<div id="vue-article-orders">
<form>
  <input type="hidden" name="organization_id" value="<?php echo $order->organization_id;?>" />
  <input type="hidden" name="order_type_id" value="<?php echo $order->order_type_id;?>" />
  <input type="hidden" name="order_id" value="<?php echo $order->id;?>" />
  <input type="hidden" name="time" value="<?php echo $time;?>" /> <?php // vale first se la pagina e' richiamata dopo la creazione di un ordine ?>
 
<?php 
if(!empty($previousOrder)) {
  $label = "Prendi gli articoli dall'ordine precedente (con consegna ".$this->HtmlCustom->data($previousOrder->delivery->data)."): trovati ".count($previousOrder->articles_orders);
  echo '<div class="box-tools"><button class="btn btn-primary btn-block" @click="preSavePreviousOrder">'.$label.'</button></div>';
}
?>

<section class="">
  <div class="row">
  
    <!-- loader globale -->
    <div class="loader-global" v-if="is_run_loader_global">
      <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
    </div>

    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><?php echo __('List'); ?></h3>

          <div class="box-tools">
            <div v-if="articles.length>0 || article_orders.length>0"> 
              <button v-if="!is_save" class="btn btn-primary btn-block" @click="preSave">Salva</button>
              <button v-if="is_save" class="btn btn-primary btn-block disabled">In elaborazione...</button>
            </div>             
          </div>
        </div>

        <div v-if="is_run" class="box-body table-responsive no-padding text-center" style="margin: 150px">
          <div><i class="fa-lg fa fa-spinner fa-spin"></i></div>
        </div>        
        <div v-if="!is_run" class="box-body table-responsive no-padding">
                 
          <?php 
          /*
           * articoli gia' associati (con eventuali acquisti)
           */ 
          echo $this->HtmlCustomSite->boxTitle(['title' => __('Articoli già associati')." ({{article_orders.length}})"]);
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
                    <th scope="col" style="width:15px"></th>
                    <th scope="col" colspan="2"><?= __('Name') ?></th>
                    <th scope="col" class="min-width-price"><?= __('Prezzo') ?></th>
                    <th scope="col" class="text-vertical min-width"><?= __('pezzi_confezione') ?></th>
                    <th scope="col" class="text-vertical min-width"><?= __('qta_multipli') ?></th>
                    <th scope="col" class="text-vertical min-width"><?= __('qta_minima') ?></th>
                    <th scope="col" class="text-vertical min-width"><?= __('qta_massima') ?></th>
                    <th scope="col" class="text-vertical min-width"><?= __('qta_minima_order') ?></th>
                    <th scope="col" class="text-vertical min-width"><?= __('qta_massima_order') ?></th>
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
                    <td class="text-center"
                      :class="article_order.carts.length>0 ? 'just-carts' : 'no-carts'"
                      :title="article_order.carts.length>0 ? 'Articolo già acquistato da '+article_order.carts.length+' gasisti!' : 'Articolo non ancora acquistato'"
                    >
                      <a href="#" @click="previewCarts(article_order)" v-if="article_order.carts.length>0">
                        <span class="badge">{{ article_order.carts.length }}</span>
                      </a>
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
                      
                      <a v-if="article_order.prezzo!=article_order.article.prezzo" 
                        title="visualizza l'articolo" 
                        :href="'<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&controller=Articles&action=context_articles_view&id='+article_order.article_id+'&article_organization_id='+article_order.article_organization_id">
                        <div class="label label-warning">
                          nel listino ora è {{ article_order.article.prezzo | currency }} €
                        </div>
                      </a>

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
          echo $this->HtmlCustomSite->boxTitle(['title' => __('Articoli da associare')." ({{articles.length}})"]);

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
                      <th scope="col" class="min-width-price"><?= __('Prezzo') ?></th>
                      <th scope="col" class="text-vertical min-width"><?= __('pezzi_confezione') ?></th>
                      <th scope="col" class="text-vertical min-width"><?= __('qta_multipli') ?></th>
                      <th scope="col" class="text-vertical min-width"><?= __('qta_minima') ?></th>
                      <th scope="col" class="text-vertical min-width"><?= __('qta_massima') ?></th>
                      <th scope="col" class="text-vertical min-width"><?= __('qta_minima_order') ?></th>
                      <th scope="col" class="text-vertical min-width"><?= __('qta_massima_order') ?></th>
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

              <div>

                <!-- messaggio se articoli hanno gia' acquisti -->
                <!-- 
                <div class="modal-dialog">
                  <div class="modal-content" v-if="msgOpen">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title">Attenzione</h4>
                    </div>
                    <div class="modal-body">
                      <p>Alcuni articoli che hai scelto di cancellare sono <b>già stati acquistati</b>:</p>
                      <p>confermi la cancellazione <b>dall'ordine</b> degli articoli e degli acquisti associati?</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-success" @click="msgOpen = !msgOpen">Chiudi e non cancellare gli acquisti</button>
                      <button type="button" class="btn btn-danger" @click="save">Continua a cancella gli acquisti già fatti</button>
                    </div>
                  </div>
                </div>
                -->

                <div v-if="articles.length>0 || article_orders.length>0"> 
                  <button v-if="!is_save" class="btn btn-primary btn-block" @click="preSave">Salva</button>
                  <button v-if="is_save" class="btn btn-primary btn-block disabled">In elaborazione...</button>
                </div>

              </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>
</section>
</form>

<!-- messaggio se articoli hanno gia' acquisti -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Attenzione</h4>
      </div>
      <div class="modal-body">
        <p>Alcuni articoli che hai scelto di cancellare sono <b>già stati acquistati</b>:</p>
        <p>confermi la cancellazione <b>dall'ordine</b> degli articoli e degli acquisti associati?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal">Chiudi e non cancellare gli acquisti</button>
        <button type="button" class="btn btn-danger"  data-dismiss="modal" @click="save">Continua a cancella gli acquisti già fatti</button>
      </div>
    </div>
  </div>
</div>

<!-- dettaglio acquisti (se ordine parent anche i sotto ordini) -->
<div id="myModalPreviewCarts" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Gasisti che hanno acquistato l'articolo</h4>
      </div>
      <div class="modal-body">
        <p id="contentPreviewCarts"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal">Chiudi</button>
      </div>
    </div>
  </div>
</div>



</div>


<style>
.box-submit {
  padding:5px;
  float: right !important;  
}
.btn-deactive {
  opacity: .45;
}
.just-carts {
   background-color:red;
   opacity: 0.6; 
}
.no-carts {
   background-color:green;
   opacity: 0.6;
}
@media only screen and (max-width: 900px) {
  .text-vertical {
    writing-mode: vertical-rl;
    /* text-orientation: upright; */
  }
}
.min-width {
  min-width: 75px
}
.min-width-price {
  min-width: 125px
}
</style>