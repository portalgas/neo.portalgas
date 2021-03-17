<?php 
use Cake\Core\Configure; 

// jQuery 3.4.1
echo $this->Html->script('jquery/jquery.min'); 

echo $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js', ['integrity' => 'sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q', 'crossorigin' => 'anonymous']); 
// Bootstrap 4.5.2
echo $this->Html->script('bootstrap-4/bootstrap.min'); 

echo $this->Html->script('moment/moment-with-locales.min');

// istanzio objScript = new Script();
echo $this->Html->script('scripts'); 
echo $this->Html->script('bootstrap-tourist/bootstrap-tourist'); 
echo $this->Html->script('myTour'); 