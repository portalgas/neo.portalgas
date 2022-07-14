<?php
use Cake\Core\Configure;
// debug($carts);
$config = Configure::read('Config');
$portalgas_fe_url = $config['Portalgas.fe.url'];
?>
<section class="content-header">
  <h1>
    <?php echo __('SocialMarkets');?>
  </h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><?php echo __('List'); ?></h3>

          <div class="box-tools">
          </div>
        </div>
        <!-- /.box-header -->
          <?php
          if($carts->count()>0) {
          ?>
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover">
            <thead>
              <tr>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
                  <th scope="col"><?= __('Bio') ?></th>
                  <th scope="col"><?= __('Codice') ?></th>
                  <th scope="col" colspan="2"><?= __('Name') ?></th>
                  <th class="text-center" scope="col"><?= __('Conf') ?></th>
                  <th class="text-center"scope="col"><?= __('PrezzoUnita') ?></th>
                  <th class="text-center" scope="col"><?= __('Qta') ?></th>
                  <th class="text-center" scope="col"><?= __('Importo') ?></th>
                  <th scope="col"><?= __('Insert') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $user_id_old = 0;
              $user_qta_totale = 0;
              $user_importo_totale = 0;
              foreach ($carts as $cart) {

                  /*
                   * intestazione user
                   */
                  if($user_id_old==0 || $user_id_old!=$cart->user->id) {

                      if($user_importo_totale>0) {
                          echo '<tr>';
                          echo '<th colspan="7"></th>';
                          echo '<th class="text-center">'.$this->Number->format($user_qta_totale).'</th>';
                          echo '<th class="text-center">'.$this->Number->currency($user_importo_totale).'</th>';
                          echo '<th></th>';
                          echo '<tr>';
                      }

                      echo '<tr>';
                      echo '<th colspan="2">';
                      // echo '<input type="checkbox" />&nbsp;';
                      echo $this->Html->link(__('socialmarket-purchase-delivered-all'), ['action' => 'purchaseDelivered', $cart->organization_id, $cart->user_id, $cart->order_id], ['class'=>'btn btn-success']);
                      echo $this->Form->postLink(__('socialmarket-purchase-delete-all'), ['action' => 'purchaseDelete', $cart->organization_id, $cart->user_id, $cart->order_id], ['confirm' => "Sei scuro di volere annullare tutto l'ordine dell'utente ".$cart->user->name, 'class'=>'btn btn-danger']);
                      echo '<th colspan="2">';
                      echo h($cart->user->name);
                      echo '</th>';
                      echo '<th colspan="4">';
                      if(!empty($cart->user_profiles['profile.address']))
                          echo $cart->user_profiles['profile.address'].' ';
                      if(!empty($cart->user_profiles['profile.city']))
                          echo $cart->user_profiles['profile.city'].' ';
                      if(!empty($cart->user_profiles['profile.region']))
                          echo '('.$cart->user_profiles['profile.region'].') ';
                      echo '</th>';
                      echo '<th colspan="3">';
                      if(!empty($cart->user->email))
                          echo $this->HtmlCustom->mail($cart->user->email).' ';
                      if(!empty($cart->user_profiles['profile.phone']))
                          echo $cart->user_profiles['profile.phone'].' ';
                      echo '</th>';
                      echo '</tr>';

                      $user_qta_totale = 0;
                      $user_importo_totale = 0;
                  }

                  $cart->article->bio=='Y' ? $is_bio = '<img src="/img/is-bio.png" title="bio" width="20" />': $is_bio = '';
                  $conf = ($cart->qta.' '.$cart->article->um);
                  $cart_importo = ($cart->articles_order->prezzo * $cart->qta);

                  echo '<tr>';
                  echo '<td class="actions text-right">';
                  // echo '<input type="checkbox" />&nbsp;';
                  echo $this->Html->link(__('socialmarket-purchase-delivered'), ['action' => 'purchaseDelivered', $cart->organization_id, $cart->user_id, $cart->order_id, $cart->article_organization_id, $cart->article_id], ['class'=>'btn btn-success']);
                  echo $this->Html->link(__('socialmarket-purchase-delete'), ['action' => 'purchaseDelete', $cart->organization_id, $cart->user_id, $cart->order_id, $cart->article_organization_id, $cart->article_id], ['class'=>'btn btn-danger']);
                  echo '</td>';
                  echo '<td class="text-center">'.$is_bio.'</td>';
                  echo '<td>'.h($cart->article->codice).'</td>';
                  echo '<td>';
                    if(!empty($cart->article->img1)) {
                    $url = $portalgas_fe_url.Configure::read('Article.img.path.full');

                    $img1_path = sprintf($url, $cart->article->organization_id, $cart->article->img1);
                    echo '<img src="'.$img1_path.'" width="'.Configure::read('Article.img.preview.width').'" />';
                    }
                    echo '</td>';
                    echo '<td>'.h($cart->articles_order->name).'</td>';
                    echo '<td class="text-center">'.$this->Number->format($cart->article->qta).' '.h($cart->article->um).'</td>';
                    echo '<td class="text-center">'.$this->Number->currency($cart->article->prezzo).'</td>';
                    echo '<td class="text-center">'.$this->Number->format($cart->qta).'</td>';
                    echo '<td class="text-center">'.$this->Number->currency($cart_importo).'</td>';
                    echo '<td>'.h($cart->created).'</td>';
                    echo '</tr>';

                  $user_id_old = $cart->user->id;
                  $user_qta_totale += $cart->qta;
                  $user_importo_totale += $cart_importo;
              }

              echo '<tr>';
              echo '<th colspan="7"></th>';
              echo '<th class="text-right">Totale</th>';
              echo '<th>'.$this->Number->currency($user_importo_totale).'</th>';
              echo '<th></th>';
              echo '<tr>';

              echo '</tbody>';
              echo '</table>';
              echo '</div>';
          }
          else {
              echo $this->element('msgResults', ['action_add' =>false]);
          }
          ?>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>
</section>