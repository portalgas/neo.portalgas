<?php
use Cake\Core\Configure;

// debug($this->Identity->get('organization'));

$config = Configure::read('Config');
$portalgas_fe_url = $config['Portalgas.fe.url'];

$organization = $this->Identity->get('organization');
?>
<!DOCTYPE html>
<html lang=en>
<head>
    <?= $this->Html->charset() ?>
   <?php
    if(!Configure::read('Site.robots'))
      echo '<meta name="robots" content="noindex">';
    ?>    
    <?php echo $this->element('fe/metatag');?> 
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->AssetMix->css('app') ?>
    <?= $this->AssetMix->script('app') ?>
    <?php echo $this->element('fe/include_css');?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>	
</head>
<body class="d-flex flex-column min-vh-100">
<main role="main" class="container-fluid">

  <a name="top" id="top"></a>

    <?php echo $this->element('fe/menu', ['config' => $config, 'organization' => $organization, 'user' => $this->Identity]);?>


	<noscript><strong>We're sorry but vue doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript>
	
	<?php echo $this->fetch('content');?>
    
    <script type="text/javascript">
    "use strict";
    var csrfToken = <?php echo json_encode($this->request->getParam('_csrfToken')) ?>;
    </script> 

<?php echo $this->element('fe/footer', ['config' => $config, 'organization' => $organization, 'user' => $this->Identity]);?>
<?php echo $this->element('fe/include_js');?>
                          
</main>
   </body>
</html>