<?php 
use Cake\Core\Configure; 

// Bootstrap 4.5.2
echo $this->Html->css('bootstrap-4/bootstrap.min', ['block' => 'css']); 
echo $this->Html->css('bootstrap-tourist/bootstrap-tourist', ['block' => 'css']);

// Font Awesome 5.11.2
echo $this->Html->css('fe/all.min', ['block' => 'css']);  
// Font Awesome  4.7.0
// echo $this->Html->css('AdminLTE./bower_components/font-awesome/css/font-awesome.min', ['block' => 'css']); 

echo $this->Html->css('fe/my.min', ['block' => 'css']);