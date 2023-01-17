<?php
echo $this->HtmlCustomSite->boxTitle(['title' => "Gestisci il prepagato per produttori", 'subtitle' => 'Escludili o includili']);

if(!empty($results)) {
	
	echo $this->Html->script('supplierOrganizationCashExcludeds', ['block' => 'scriptPageInclude']);

	echo $this->Form->create($results);
	echo '<table class="dataTables table table-striped table-hover">';
	echo '<thead>';
	echo '<tr>';
	// echo '<th scope="col">'.__('N').'</th>';
	echo '<th scope="col">'.__('Supplier-Name').'</th>';
	echo '<th scope="col"></th>';
	echo '<th scope="col"></th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';

	foreach($results as $numResult => $result) {

		//if(!empty($result->supplierOrganizationCashExcludeds))
		//	debug($result->supplierOrganizationCashExcludeds->supplier_organization_id);

		echo '<tr>';
		// echo '<td>'.($numResult + 1).'</td>';
		echo '<td>';
		echo $this->HtmlCustomSite->boxSupplierOrganization($result);
		echo '</td>';
		echo '<td>';

		/* 
		 * produttore NON inserito in supplier_organization_cash_excludeds => sara' considerato con il prepagato
		 */
		$options = [];
		$options['data-attr-id'] = $result->id;
		$options['data-attr-type'] = 'delete-'.$result->id; 
		$options['class'] = 'cashExcludeds btn btn-success button';
		$options['escape'] = false;
		if(!empty($result->supplierOrganizationCashExcludeds)) {
			$options['style'] = 'display:none;';
			// debug($result->supplierOrganizationCashExcludeds->supplier_organization_id);
		}
		else {
			$options['style'] = 'display:block;';
		}		
		// azione futura se clicco
		$label = __('Ora è calcolato con il prepagato,<br />clicca per escluderlo');
		echo $this->Html->link($label, '#', $options);

		/* 
		 * produttore inserito in supplier_organization_cash_excludeds => sara' escluso dal prepagato
		 */		
		$options = [];
		$options['data-attr-id'] = $result->id;
		$options['data-attr-type'] = 'insert-'.$result->id; 
		$options['class'] = 'cashExcludeds btn btn-warning button';
		$options['escape'] = false;
		if(empty($result->supplierOrganizationCashExcludeds)) {
			$options['style'] = 'display:none;';
			// debug($result->supplierOrganizationCashExcludeds);
		}
		else {
			$options['style'] = 'display:block;';
		}
		// azione futura se clicco
		$label = __('Ora è escluso dal prepagato,<br />clicca per includerlo');					
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

	/* 
	 * ordering in conflitto con i loghi
	 */
	echo $this->element('dataTables', ['paging' => 'false', 'ordering' => 'false']);
}
else {
	echo $this->element('msg');
}
?>