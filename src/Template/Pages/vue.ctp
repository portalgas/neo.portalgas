<?php
use Cake\Core\Configure;

// debug($this->Identity->get('organization'));

$config = Configure::read('Config');
$portalgas_fe_url = $config['Portalgas.fe.url'];

$organization = $this->Identity->get('organization');

$this->layout = 'vue';
?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it-it" lang="it-it" dir="ltr" >
<head>
    <?= $this->Html->charset() ?>
    <?php
    if(!Configure::read('Site.robots'))
      echo '<meta name="robots" content="noindex">';
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $this->element('fe/metatag');?>
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?php echo $this->element('fe/include_css');?>

    <style>
	.content-body {
	  overflow: none;
	  height: 100%;
	  margin: 25px 0;
	}
	.bg-primary {
	    background-color: #0a659e !important;
	    color: #fff;
	}	
	.btn-primary {
	    color: #fff;
	    background-color: #0a659e !important;
	    border-color: #0a659e !important;
	}
	.btn-success {
	    color: #fff;
	    background-color: #fa824f !important; /* orange */
	    border-color: #fa824f !important; /* orange */
	}	
	a {
	    color: #0a659e;
	}	
	a:hover {
	    color: #fa824f !important; /* orange */
	}	
    </style>	
</head>
<body>
<main role="main" class="container-fluid">
  
  <a name="top" id="top"></a>

    <?php echo $this->element('fe/menu', ['config' => $config, 'organization' => $organization, 'user' => $this->Identity]);?>

	<div class="content-body">
		<div id="app">
	    	<app></app>
		</div>	
	</div>

	<?php echo $this->element('fe/footer', ['config' => $config, 'organization' => $organization, 'user' => $this->Identity]);?>
	<?php echo $this->element('fe/include_js');?>
							  
</main>
</body>
</html>