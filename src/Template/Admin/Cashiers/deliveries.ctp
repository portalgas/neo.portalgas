<?php
use Cake\Core\Configure;

$config = Configure::read('Config');
$portalgas_fe_url = $config['Portalgas.fe.url'];

echo $this->Html->script('vue/cashierDeliveries', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => "Gestisci pagamenti dell'intera consegna", 'subtitle' => '']);

if(!empty($deliveries)) {
	
	echo '<div id="vue-cashiers">';

  echo $this->Form->create(null, ['role' => 'form']);

  echo '<div class="row">'; 
  echo '<div class="col-md-12">'; 
  echo $this->Form->control('is_cash', ['type' => 'radio', 'label' => 'Prendi in considerazione la cassa', 'options' => $is_cashs, // 'default' => $is_cash_default,
    'v-model' => "is_cash"
   ]);
  echo '</div>';
  echo '</div>';

  echo '<div class="row">'; 
  echo '<div class="col-md-12">'; 
	echo $this->Form->control('delivery_id', ['options' => $deliveries, 'class' => 'form-control select2-', 'escape' => false, 'empty' => Configure::read('HtmlOptionEmpty'), 
		// '@change' => 'getOrdersByDelivery'
		// '@change' => 'getUsersByDelivery'
		//'@change' => 'getCompleteUsersByDelivery'
    '@change' => 'getData'
		]);
  echo '</div>';
  echo '</div>';

  echo '<div v-show="is_found_orders === false" style="display: none;text-align: center;" class="run run-orders"><div class="spinner"></div></div>';
  echo '<div v-show="is_found_orders === true" style="display:none;">';
  echo $this->HtmlCustomSite->boxTitle(['title' => 'Elenco degli ordini nello stato "in carico al cassiere"', 'subtitle' => '']);
  ?>
    <table class="table table-hover">
      <thead class="thead-light">
        <tr>
          <th><?php echo __('Supplier-Name');?></th>
          <th></th>
          <th><?php echo __('StateOrder');?></th>
          <th><?php echo __('ImportoTotaleOrdine');?></th>
          <th><?php echo __('data_inizio');?></th>
          <th><?php echo __('data_fine');?></th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="order in orders"
          :order="order.id"
          :key="order.id"
        > 
          <td> 
          	<img v-if="order.suppliers_organization.supplier.img1 != null" class="img-supplier" width="<?php echo Configure::read('Supplier.img.preview.width');?>" :src="'<?php echo $portalgas_fe_url.Configure::read('Supplier.img.path.fulljs');?>'+order.suppliers_organization.supplier.img1" alt="" /></td>
          <td>{{ order.suppliers_organization.name }}</td>
          <td>{{ order.state_code | orderStateCode }}</td>
          <td>{{ order.tot_importo | formatImportToDb }}</td>
          <td>{{ order.data_inizio | formatDate }}</td>
          <td>{{ order.data_fine | formatDate }}</td>
        </tr>
      </tbody>
    </table>
  <?php
	echo '</div>'; // is_found_orders

  echo '<div v-show="is_found_users === false" style="display: none;text-align: center;"  class="run run-users"><div class="spinner"></div></div>';
  echo '<div v-show="is_found_users === true" style="display:none;">';

  echo '<div v-if="users!=null && users.length>0">';
  echo $this->HtmlCustomSite->boxTitle(['title' => "Elenco gasisti che devono saldare", 'subtitle' => 'Ordini nello stato "in carico al cassiere"']);
    ?>
    <table class="table table-hover">
      <thead class="thead-light">
        <tr>
          <th><?php echo __('User');?></th>
          <th class="hidden-xs"><?php echo __('Orders');?></th>
          <th><?php echo __('ImportoDovuto');?></th>
          <th v-show="is_cash === '1'"><?php echo __('CashIn');?></th>
          <th v-show="is_cash === '1'"><?php echo __('CashImportoUpdate');?></th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="user in users"
          :user="user.id"
          :key="user.id"
        >
          <td>
            <a style="cursor: pointer;" data-toggle="collapse" :data-target="'#user-' + user.id">{{ user.name }}</a>
            <div v-bind:id="['user-'+user.id]" class="collapse box-collapse" 
                  style="max-width: 50%; box-shadow: 5px 5px #e6e1e1;">
                <div v-for="summary_order in user.summary_orders"
                :summary_order="summary_order.id"
                :key="summary_order.id"
                > 
                  <div>{{ summary_order.order.suppliers_organization.name }}</div>
                  <div class="pull-right"><?php echo __('Importo');?> {{ (summary_order.importo - summary_order.importo_pagato) | currency }} &euro;</div>
                  <div class="clearfix"></div>
                </div>
            </div>            
          </td>
          <td class="hidden-xs">
            <div v-for="summary_order in user.summary_orders"
            :summary_order="summary_order.id"
            :key="summary_order.id"
            > 
              <div>{{ summary_order.order.suppliers_organization.name }}</div>
              <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo __('Importo');?> {{ (summary_order.importo - summary_order.importo_pagato) | currency }} &euro;</div>
            </div>
          </td>
          <td>
            {{ (user.summary_delivery.tot_importo - user.summary_delivery.tot_importo_pagato) | currency }} &euro;
          </td>
          <td v-show="is_cash === '1'">
            <div v-if="user.cash == null">0,00 &euro;</div>
            <div v-if="user.cash != null">{{ user.cash.importo | currency }} &euro;</div>
          </td>
          <td v-show="is_cash === '1'">
            <span class="label label-success" v-if="user.cash_importo_new > 0">{{ user.cash_importo_new | currency }} &euro;</span>
            <span class="label label-warning" v-if="user.cash_importo_new == 0">{{ user.cash_importo_new | currency }} &euro;</span>
            <span class="label label-danger" v-if="user.cash_importo_new < 0">{{ user.cash_importo_new | currency }} &euro;</span>
          </td>
        </tr>
      </tbody>
    </table>
    <?php
    echo '<div class="row">'; 
    echo '<div class="col-md-12">'; 
    echo $this->Form->control('nota', ['type' => 'textarea', 'label' => __('Cash-Nota')]);
    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '<div v-if="users==null || users.length==0" class="row">'; 
    echo '<div class="col-md-12">';    
    echo $this->element('msg', ['msg' => "Non sono presenti gasisti che devono saldare"]);
    echo '</div>';
    echo '</div>';

    /*
     * gestisco con disabled perche' se lo includo solo se users!=null e' sempre disabled
     */
    echo $this->Form->submit(__('Salda tutti i gasisti'), ['id' => 'submit', 'class' => 'btn btn-success  pull-right disabled', 'disabled' => 'disabled']);

    echo '</div>'; // is_found_users

	
    echo $this->Form->end();
	  echo '</div>'; // vue-cashiers
}
else {
	echo $this->element('msg');
}
?>