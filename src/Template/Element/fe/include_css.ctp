<?php 
use Cake\Core\Configure; 

// Bootstrap 4.5.2
echo $this->Html->css('bootstrap-4/bootstrap.min', ['block' => 'css']); 

// Font Awesome 5.11.2
echo $this->Html->css('/font/fontawesome/css/all', ['block' => 'css']);  

echo $this->Html->css('my.css', ['block' => 'css']);  