<?php 
use Cake\Core\Configure; 

// jQuery 3.4.1
echo $this->Html->script('jquery/jquery.min'); 

// Bootstrap 4.5.2
echo $this->Html->script('bootstrap-4/bootstrap.min'); 

echo $this->Html->script('moment/moment-with-locales.min.js');

// istanzio objScript = new Script();
echo $this->Html->script('scripts.js'); 