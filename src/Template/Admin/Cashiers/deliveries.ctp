<?php
use Cake\Core\Configure;

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
		'@change' => 'getCompleteUsersByDelivery'
		]);
  echo '</div>';
  echo '</div>';

	echo '<div class="result-users" style="display:none;">';

    echo $this->HtmlCustomSite->boxTitle(['title' => "Elenco gasisti che devono saldare", 'subtitle' => 'Ordini nello stato "in carico al cassiere"']);
    ?>
    <table class="table table-hover" v-show="is_found === true">
      <thead class="thead-light">
        <tr>
          <th>gasista</th>
          <th>Orders</th>
          <th>Importo dovuto</th>
          <th v-show="is_cash === '1'">In cassa</th>
          <th v-show="is_cash === '1'">Nuovo importo cassa</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="user in users"
          :user="user.id"
          :key="user.id"
        >
          <td>{{ user.name }}</td>
          <td>
            <div v-for="summary_order in user.summary_orders"
            :summary_order="summary_order"
            :key="summary_order.id"
            > 
              <div>{{ summary_order.order.suppliers_organization.name }}</div>
              <div>importo {{ (summary_order.importo - summary_order.importo_pagato) | currency }} &euro;</div>
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
	</div>

	<div class="result-orders" style="display:none;">
    <table class="table table-hover">
      <thead class="thead-light">
        <tr>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="order in orders"
          :order="order.id"
          :key="order.id"
        >
          <td> 
          	<img v-if="order.suppliers_organization.supplier.img1 != null" class="img-thumbnail" :src="'<?php echo Configure::read('Supplier.img.path.fulljs');?>'+order.suppliers_organization.supplier.img1" alt="" /></td>
          <td>{{ order.suppliers_organization.name }}</td>
          <td>{{ order.data_inizio }}</td>
          <td>{{ order.data_fine }}</td>
        </tr>
      </tbody>
    </table>
	
	<?php
  echo '</div>';
  echo $this->Form->submit(__('Salda tutti i gasisti'), ['id' => 'submit', 'class' => 'btn btn-success  pull-right disabled']);
  echo $this->Form->end();
	echo '</div>'; // vue-cashiers
}
else {
	echo $this->element('msg');
}
?>