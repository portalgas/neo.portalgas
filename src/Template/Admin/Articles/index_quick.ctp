<?php 
use Cake\Core\Configure;
use App\Traits;

echo $this->Html->script('dropzone/dropzone.min', ['block' => 'scriptInclude']); 
echo $this->Html->css('dropzone/dropzone.min', ['block' => 'css']); 
?>
<section class="content-header">
  <h1>
    <?php echo __('Articles');?>
  </h1>
</section>

<!-- Main content -->
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
          <table class="table table-hover">
            <thead>
              <tr>
                  <th scope="col" class="actions text-center"><?= __('Actions') ?></th>
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
              <?php foreach ($articles as $article) { 
     
                echo $this->Form->create(null, ['role' => 'form']);
                echo $this->Form->control('id', ['type' => 'hidden', 'value' => $article['id']]);
                echo $this->Form->control('organization_id', ['type' => 'hidden', 'value' => $article['organization_id']]);
                ?>
                <tr>
                  <td class="actions text-right">
                      <?php 
                      if($article['stato']=='Y') {
                        $label = 'Attivo';
                        $css = 'primary';

                      } 
                      else {
                        $label = 'Non attivo';
                        $css = 'danger';
                      }
                      echo $this->Form->button($label, ['class'=>'btn btn-'.$css]);
                      echo '<br />';

                      if($article['flag_presente_articlesorders']=='Y') {
                        $label = 'Ordinabile';
                        $css = 'primary';

                      } 
                      else {
                        $label = 'Non ordinabile';
                        $css = 'danger';
                      }
                      echo $this->Form->button($label, ['class'=>'btn btn-'.$css]);

                      echo '<br />';
                      echo $this->Form->button('<i class="fa fa-search-plus" aria-hidden="true"></i>', ['class'=>'btn btn-info']);
                      ?>
                  </td>
                  <?php 
                  if(empty($search_supplier_organization_id))
                    echo '<td>'.$article['suppliers_organization']['name'].'</td>';
                  ?>
                  <td><?= $article['categories_article']['name']; ?></td>
                  <td><?= $this->Form->control('bio', ['label' => false, 'type' => 'radio', 'options' => $si_no, 'default' => $article['bio']]) ?></td>
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
                  <td><?= $this->Form->control('um_riferimento', ['label' => false, 'value' => $article['um_riferimento'], 'options' => $ums]) ?></td>
                </tr>

                <tr>
                  <th scope="col"></th>
                  <th scope="col"></th>
                  <th scope="col"></th>
                  <th scope="col"></th>
                  <th scope="col"><?= __('pezzi_confezione') ?></th>
                  <th scope="col"><?= __('qta_minima') ?></th>
                  <th scope="col"><?= __('qta_massima') ?></th>
                  <th scope="col"><?= __('qta_minima_order') ?></th>
                  <th scope="col"><?= __('qta_massima_order') ?></th>
                  <th scope="col"><?= __('qta_multipli') ?></th>
                  <!-- th scope="col"><?= __('alert_to_qta') ?></th -->
                </tr>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
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