<?php
if(!empty($results)) {
	
	echo $this->Html->script('supplierOrganizationCashExcludeds', ['block' => 'scriptPageInclude']);

	echo $this->Form->create($results);
	echo '<table class="table table-hover">';
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
		echo $this->HtmlCustomSite->boxSupplierOrganization($result);
		echo '</td>';
		echo '<td>';

		/* 
		 * produttore gia' inserito, sara' escluso dal prepagato
		 */
		$options = [];
		$options['data-attr-id'] = $result->id;
		$options['data-attr-type'] = 'delete-'.$result->id; 
		$options['class'] = 'cashExcludeds btn btn-success button';
		if(empty($result['supplierOrganizationCashExcludeds'])) 
			$options['style'] = 'display:none;';
		$label = __('Considera con il prepagato');
		echo $this->Html->link($label, '#', $options);

		/* 
		 * produttore NON inserito, sara' considerato con il prepagato
		 */
		$options = [];
		$options['data-attr-id'] = $result->id;
		$options['data-attr-type'] = 'insert-'.$result->id; 
		$options['class'] = 'cashExcludeds btn btn-warning button';
		if(!empty($result['supplierOrganizationCashExcludeds'])) 
			$options['style'] = 'display:none;';
		$label = __('Escludi dal prepagato');			
		echo $this->Html->link($label, '#', $options);
		
		echo '</td>';

		echo '<td style="min-width:75px;">';
		echo '<div class="msg-'.$result->id.'"></div>';
		echo '</td>';

		echo '</tr>';		
	}	
	echo '</tbody>';
	echo '</table>';
	echo $this->Form->end();
}
else {
	echo $this->element('msg');
}
?>