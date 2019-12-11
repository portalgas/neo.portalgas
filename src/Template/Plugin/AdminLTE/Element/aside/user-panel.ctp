<?php
if ($this->Identity->isLoggedIn()) {
?>
	<div class="user-panel">
	    <div class="pull-left image">
	        <?php echo $this->Html->image('avatar5.png', array('class' => 'img-circle', 'alt' => 'User Image')); ?>
	    </div>
	    <div class="pull-left info">
	        <p><?php echo $this->Identity->get('username');?></p>
	        <i class="fa fa-circle text-success"></i> Online
	    </div>
	</div>
<?php
} 
?>