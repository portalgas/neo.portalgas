<?php
use Cake\Core\Configure;

$user = $this->Identity->get();

echo $this->element('menu-order', ['order' => $order]);
/*
 * nome dell'istanza dell'helper della tipologia di order
 */
$htmlCustomSiteOrders = $this->HtmlCustomSiteOrders->factory($order_type_id, $user, $parent, $order);
// debug($htmlCustomSiteOrders);

echo $this->Html->script('ordersForm.js?v=20230925', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Order-'.$order_type_id), 'subtitle' => __('Associalo ad una consegna scaduta')], ['home', 'list-orders'], $order);
?>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <?php
      echo $htmlCustomSiteOrders->infoParent();
      ?>
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo __('Dati ordine'); ?></h3>
        </div>
        <?php
          echo $this->Form->create($order, ['role' => 'form', 'id' => 'frm']);
          echo '<div class="box-body">';

              /*
                * passato per OrderValidation
                */
              echo $htmlCustomSiteOrders->hiddenFields();

              /*
                * produttore
                */
              echo $htmlCustomSiteOrders->supplierOrganizations($suppliersOrganizations);

              echo $this->Form->control('delivery_id', ['options' => $delivery_olds, 'escape' => false, 'empty' => false]);

          echo '</div>';  // /.box-body

          echo $this->Form->submit(__('Submit'), ['id' => 'submit', 'class' => 'btn btn-success  pull-right']);

        echo $this->Form->end();

        echo '</div>';  // /.box -->
    echo '</div>';
echo '</div>';  //  /.row -->
echo '</section>';
