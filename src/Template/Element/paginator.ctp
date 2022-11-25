<?php
$params = $this->Paginator->params();
// debug($params);

if(!empty($params)) {
	echo '<div class="paginator">';
	echo '<ul class="pagination">';
	echo $this->Paginator->first(__('first'));
	echo $this->Paginator->prev('< ' . __('previous'));
	echo $this->Paginator->numbers(['before' => '', 'after' => '']);
	echo $this->Paginator->next(__('next') . ' >');
	echo $this->Paginator->last(__('last'));
	echo '</ul>';
	echo '<p>';
	echo $this->Paginator->counter(['format' => __('Pagina {{page}} di {{pages}}, mostrati {{current}} elementi di {{count}} totali')]);
	echo '</p>';
	echo '</div>';	
}
