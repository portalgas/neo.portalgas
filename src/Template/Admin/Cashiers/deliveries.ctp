<?php
use Cake\Core\Configure;

echo $this->Html->script('vue/cashierDeliveries', ['block' => 'scriptPageInclude']);

if(!empty($deliveries)) {
	
	echo '<div id="vue-cashiers">';

	echo $this->Form->control('delivery_id', ['options' => $deliveries, 'class' => 'form-control select2-', 'escape' => false, 'empty' => Configure::read('HtmlOptionEmpty'), 
		// '@change' => 'getOrdersByDelivery'
		// '@change' => 'getUsersByDelivery'
		'@change' => 'getCompleteUsersByDelivery'
		]);
	?>
	<div class="result-users" style="display:none;">
    <table class="table table-hover">
      <thead class="thead-light">
        <tr>
          <th>gasista</th>
          <th>summaryOrder</th>
          <th>cash.importo</th>
          <th>cash.importo_new</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="user in users"
          :user="user"
          :key="user.id"
        >
          <td>{{ user.name }}</td>
          <td>
            <div v-for="summary_order in user.summary_orders"
            :summary_order="summary_order"
            :key="summary_order.id"
            > 
            <div>{{ summary_order.order.suppliers_organization.name }}</div>
            <div>{{ summary_order.order.state_code }}</div>
            <div v-if="summary_order.importo == null">0,00 &euro;</div>
            <div v-if="summary_order.importo != null">{{ summary_order.importo }} &euro;</div>
            <div>{{ summary_order.importo_pagato }} &euro;</div>            
            </div>
            <p>------------------------------------------------------</p>
            <div>
              tot_importo {{ user.summary_delivery.tot_importo}} &euro;
              tot_importo_pagato {{ user.summary_delivery.tot_importo_pagato}} &euro;
            </div>
          </td>
          <td>
          	<div v-if="user.cash == null">0,00 &euro;</div>
          	<div v-if="user.cash != null">{{ user.cash.importo }} &euro;</div>
          </td>
          <td>{{ user.cash_importo_new }} &euro;</td>
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
          :order="order"
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
	</div>
	<?php
	echo '</div>'; // vue-cashiers
}
else {
	echo $this->element('msg');
}
?>