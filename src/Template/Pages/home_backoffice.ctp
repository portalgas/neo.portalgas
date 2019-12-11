<?php
use Cake\Core\Configure;
if(Configure::read('BootstrapUIEnabled'))  $this->extend('/Layout/TwitterBootstrap/signin');

$this->layout = 'home_backoffice';
?>

<div class="container">
    <div class="row">
    	<div class="col-sm">	
    	    <div><img src="/img/logo.jpg"></div>
    	</div>
    </div>
    <div class="row">
        <div class="col-sm">    
             <div class="text-center">
                <a href="<?php echo $this->Url->build('admin'); ?>">login</a>
              </div>  
        </div>
    </div>


</div>
