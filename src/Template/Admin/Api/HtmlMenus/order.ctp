<?php
use Cake\Core\Configure;

/*
 * per i TEST
$order->state_code = 'WAIT-PROCESSED-TESORIERE';
*/

if(!empty($des_order_id)) {
	echo '<h2>';
	if($desOrdersResults->des_id==$user->des_id)
        echo '<a title="'.__('DesOrder').'" href="'.$this->HtmlCustomSite->jLink('des_orders_organizations', 'index', ['des_order_id' => $des_order_id]).'">'.__('DesOrder').'</a>';
	else
    	echo __('DesOrder');
	echo $this->HtmlCustomSite->drawDesOrdersStateDiv($desOrdersResults);
	echo '<br />';
	echo '<small style="color:#000;">'.__($desOrdersResults->state_code.'-label').'</small>';
	echo '</h2>';
}

if(!empty($order->gas_group_id)) {
	echo '<h2>';
	echo __('Gas Group Orders').' '.$gasGroup->name;
	echo '</h2>';
}

/*
 * menu Orders::index $position_img=='bgLeft'
 */
echo "\r\n";
echo '<ul class="menuLateraleItems">';
echo "\r\n";


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
	echo '<li>';
	if(empty($orderAction['url']))
		echo '<div style="font-weight:bold;color:#003D4C;" title="'.__($orderAction['label']).'" class="'.$position_img.' '.$orderAction['css_class'].'" >'.$label.'</div>';
	else
        echo '<a title="'.$title.'" class="'.$position_img.' '.$orderAction['css_class'].'" href="'.$this->HtmlCustomSite->jLink($orderAction['controller'], $orderAction['action'], $orderAction['qs']).'">'.$label.'</a>';
	echo '</li>';
} // end for foreach($orderActions as $orderAction)

echo '</ul>';

/*
 * gestione O R D E R S - S T A T E S
 */
echo '<div class="clearfix"></div>';
echo '<h3>Ciclo dell\'ordine</h3>';

echo '<ul class="menuLateraleItems">';
foreach($templatesOrdersStates as $templatesOrdersState) {

	echo "\r\n";

	if($order['state_code'] == $templatesOrdersState->state_code) {
		echo '<li class="statoCurrent">';
		echo '<a title="'.__($templatesOrdersState->state_code.'-intro').'" ';
		echo '	 class="'.$position_img.' ';
		echo '	 orderStato'.$templatesOrdersState->state_code.'" ';
		echo '	 style="text-decoration:none;font-weight:bold;cursor:pointer;"';

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
		echo '<li class="statoNotCurrent">';
		echo '<a title="'.__($templatesOrdersState->state_code.'-intro').'" ';
		echo '	 class="'.$position_img.' ';
		echo '	 orderStato'.$templatesOrdersState->state_code.'" ';
		echo '	 style="text-decoration:none;font-weight:normal;cursor:default;';
		echo '">';
		echo __($templatesOrdersState->state_code.'-label');
		echo '</a>';
		echo '</li>';
	}

} // end foreach($orderStates as $orderState)
echo '</ul>';
?>
<style>
h2 {
    color: #e32;
    font-size: 16px;
    border-radius: 0px;
    -moz-border-radius: 0px;
    -webkit-border-radius: 0px;
    min-height: 42px;
    margin: 0px;
    padding: 10px;
    clear: both;
}    
</style>