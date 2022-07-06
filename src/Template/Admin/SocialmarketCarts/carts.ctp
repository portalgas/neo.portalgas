<?php
use Cake\Core\Configure;

$config = Configure::read('Config');
$portalgas_fe_url = $config['Portalgas.fe.url'];
?>
<section class="content-header">
  <h1>
    <?php echo __('Carts');?>

    <div class="pull-right"><?php echo $this->Html->link(__('New'), ['action' => 'add'], ['class'=>'btn btn-success btn-xs']) ?></div>
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
            <form action="<?php echo $this->Url->build(); ?>" method="POST">
              <div class="input-group input-group-sm" style="width: 150px;">
                <input type="text" name="table_search" class="form-control pull-right" placeholder="<?php echo __('Search'); ?>">

                <div class="input-group-btn">
                  <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover">
            <thead>
              <tr>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
                  <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                  <th scope="col"><?= __('Bio') ?></th>
                  <th scope="col" colspan="2"><?= __('Name') ?></th>
                  <th scope="col"><?= __('Codice') ?></th>
                  <th scope="col"><?= __('Prezzo') ?></th>
                  <th scope="col"><?= __('Qta') ?></th>
                  <th scope="col"><?= __('Conf') ?></th>
                  <th scope="col"><?= __('PrezzoUnita') ?></th>
                  <th scope="col"><?= __('Insert') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($carts as $cart) {

                  $cart->article->bio=='Y' ? $is_bio = '<img src="/is-bio.png" title="bio" width="20" />': $is_bio = '';
                  $conf = ($cart->qta.' '.$cart->article->um);
                  $importo = ($cart->qta * $cart->prezzo);

                  echo '<tr>';
                  ?>
                  <td class="actions text-right">
                      <?= $this->Html->link(__('View'), ['action' => 'view', $cart->organization_id], ['class'=>'btn btn-info btn-xs']) ?>
                      <?= $this->Html->link(__('Edit'), ['action' => 'edit', $cart->organization_id], ['class'=>'btn btn-warning btn-xs']) ?>
                      <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $cart->organization_id], ['confirm' => __('Are you sure you want to delete # {0}?', $cart->organization_id), 'class'=>'btn btn-danger btn-xs']) ?>
                  </td>
                  <?php
                  echo '<td>'.h($cart->user->name).'</td>';
                  echo '<td class="text-center">'.$is_bio.'</td>';
                  echo '<td>';
                    if(!empty($cart->article->img1)) {
                    $url = $portalgas_fe_url.Configure::read('Article.img.path.full');

                    $img1_path = sprintf($url, $cart->article->organization_id, $cart->article->img1);
                    echo '<img src="'.$img1_path.'" width="'.Configure::read('Article.img.preview.width').'" />';
                    }
                    echo '</td>';
                    echo '<td>'.h($cart->articles_order->name).'</td>';
                    ?>
                  <td><?= h($cart->article->codice) ?></td>
                    <td><?= $this->Number->format($cart->article->prezzo) ?></td>
                    <td><?= $this->Number->format($cart->article->qta) ?></td>
                    <td><?= $this->Number->format($cart->article->pezzi_confezione) ?></td>
                    <td><?= h($cart->article->um) ?></td>
                  <td><?= h($cart->created) ?></td>
                </tr>
              <?php
              }
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