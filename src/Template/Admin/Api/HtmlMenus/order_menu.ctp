<?php
use Cake\Core\Configure;

/*
 * per i TEST
$order->state_code = 'WAIT-PROCESSED-TESORIERE';
*/

$label = __('Order-'.$order->order_type_id);
if(!empty($des_order_id)) {
	$label = '';
	if($desOrdersResults->des_id==$user->des_id)
		$label .= '<a title="'.__('DesOrder').'" href="'.$this->HtmlCustomSite->jLink('des_orders_organizations', 'index', ['des_order_id' => $des_order_id]).'">'.__('DesOrder').'</a>';
	else
		$label .= __('DesOrder');
	$label .=  $this->HtmlCustomSite->drawDesOrdersStateDiv($desOrdersResults);
	$label .= '<br />';
	$label .= '<small>'.__($desOrdersResults->state_code.'-label').'</small>';
}
else if(!empty($order->gas_group_id)) 
	$label = __('Gas Group Order').'<br />'.$gasGroup->name;

echo '
<a href="#">
  <span>'.$label.'</span>
  <span class="pull-right-container">
	<i class="fa fa-angle-left pull-right"></i>
  </span>
  <ul class="treeview-menu menuLateraleItems">
';
foreach($orderActions as $orderAction) {

    $label = strip_tags(__($orderAction['label']));

	/*
	 * dettaglio importo trasport, cost_more, cost_less
	 */
	if(!empty($orderAction['label_more'])) {
        if(isset($order->{$orderAction['label_more']}))
            $importo = $order->{$orderAction['label_more']};
        else {
            $label_more = substr($orderAction['label_more'], 0, strlen($orderAction['label_more'])-1); // da trasport_ (importo formattato) a trasport
            $importo = number_format($order->{$label_more},2,Configure::read('separatoreDecimali'),Configure::read('separatoreMigliaia'));
        }
        $label = $label.' ('.$importo.' €)';

    }

	$title = strip_tags(__($orderAction['label']));

	echo "\r\n";
	echo '<li style="margin:10px 0px;">';
	if(!empty($orderAction['neo_url']))
		echo '<a title="'.$title.'" class="menu-item bgLeft '.$orderAction['css_class'].'-small" href="'.$orderAction['neo_url'].'">'.$label.'</a>';
	else	
	if(empty($orderAction['url']))
		echo '<div title="'.__($orderAction['label']).'" class="menu-item bgLeft '.$orderAction['css_class'].'-small" >'.$label.'</div>';
	else
        echo '<a title="'.$title.'" class="menu-item bgLeft '.$orderAction['css_class'].'-small" href="'.$this->HtmlCustomSite->jLink($orderAction['controller'], $orderAction['action'], $orderAction['qs']).'">'.$label.'</a>';
	echo '</li>';
} // end for foreach($orderActions as $orderAction)

/*
 * gestione O R D E R S - S T A T E S
 */
echo '<li class="header" style="color:#b3b7b9;background:#1a2226;">CICLO D\'ORDINE</li>';
foreach($templatesOrdersStates as $templatesOrdersState) {

	echo "\r\n";

	if($order['state_code'] == $templatesOrdersState->state_code) {
		echo '<li class="statoCurrent" style="margin:10px 0px;">
			<a title="'.__($templatesOrdersState->state_code.'-intro').'" 
			   style="min-height: 40px;color: #fff;"
			   class="bgLeft orderStato'.$templatesOrdersState->state_code.'-small" ';

		/*
		 * eventuale azione successiva
		 */
		if(!empty($templatesOrdersState->action_controller) && !empty($templatesOrdersState->action_action))
			echo 'href="'.$this->HtmlCustomSite->jLink($templatesOrdersState->action_controller, $templatesOrdersState->action_action, ['delivery_id' => $order->delivery_id, 'order_id' => $order->id]).'" ';

		echo '>';
		echo __($templatesOrdersState->state_code.'-label');

		if(!empty($templatesOrdersState->action_controller) && !empty($templatesOrdersState->action_action))
			echo '<br />'.__($templatesOrdersState->state_code.'-action');

		echo '</a>';
		echo '</li>';
	}
	else {
		echo '<li class="statoNotCurrent" style="margin:10px 0px;">';
		echo '<a title="'.__($templatesOrdersState->state_code.'-intro').'" 
				style="min-height: 40px;"
			 	class="bgLeft orderStato'.$templatesOrdersState->state_code.'-small">';
		echo __($templatesOrdersState->state_code.'-label');
		echo '</a>';
		echo '</li>';
	}

} // end foreach($orderStates as $orderState)

echo '</ul> <!-- treeview-menu menuLateraleItems --> ';
echo '</a>
';
?>