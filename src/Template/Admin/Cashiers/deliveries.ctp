<?php
use Cake\Core\Configure;

echo $this->Html->script('vue/cashierDeliveries', ['block' => 'scriptPageInclude']);

if(!empty($deliveries)) {
	
	echo '<div id="vue-cashiers">';

	echo $this->Form->control('delivery_id', ['options' => $deliveries, 'class' => 'form-control select2-', 'escape' => false, 'empty' => Configure::read('HtmlOptionEmpty'), '@change' => 'getOrdersByDelivery']);

	echo '<div class="result-users" style="display:none;">';
?>
    <table class="table table-hover">
      <thead class="thead-light">
        <tr>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="user in users"
          :article="user"
          :key="user.id"
        >
          <td>{{ user.name }}</td>
        </tr>
      </tbody>
    </table>
<?php	
	echo '</div>';

	echo '<div class="result-orders" style="display:none;">';
?>
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
          :article="order"
          :key="order.id"
        >
          <td> 
          	<img v-if="order.suppliers_organization.supplier.img1 != null" class="img-thumbnail" :src="'<?php echo Configure::read('Supplier.img.path.full.js');?>'+order.suppliers_organization.supplier.img1" alt="" /></td>
          <td>{{ order.suppliers_organization.name }}</td>
          <td>{{ order.data_inizio }}</td>
          <td>{{ order.data_fine }}</td>
        </tr>
      </tbody>
    </table>
<?php
	echo '</div>';

	echo '</div>'; // vue-cashiers

	/*
	echo $this->Form->create($results);
	echo '<table class="table table-striped table-hover">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col">'.__('N').'</th>';
	echo '<th scope="col">'.__('Supplier-Name').'</th>';
	echo '<th scope="col"></th>';
	echo '<th scope="col"></th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';

	foreach($results as $numResult => $result) {

		// debug($result);

		echo '<tr>';
		echo '<td>'.($numResult + 1).'</td>';
		echo '<td>';
		echo $result->luogo;
		echo '</td>';
		echo '<td>';
	}	
	echo '</tbody>';
	echo '</table>';
	echo $this->Form->end();
	*/
}
else {
	echo $this->element('msg');
}
?>