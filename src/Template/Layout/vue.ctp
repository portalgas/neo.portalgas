<?php
use Cake\Core\Configure;

$config = Configure::read('Config');
$portalgas_fe_url = $config['Portalgas.fe.url'];

$organization = $this->Identity->get('organization');
// debug($organization);
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
    
    <?php echo $this->element('fe/footer', ['config' => $config, 'organization' => $organization, 'user' => $this->Identity]);?>
    <?php echo $this->element('fe/include_js');?>
    
    <script type="text/javascript">
    "use strict";
    var csrfToken = <?php echo json_encode($this->request->getParam('_csrfToken')) ?>;
    var j_seo = "<?php echo $organization->j_seo;?>"; // da passa a vue in app.js
    var headers = {
        "Content-Type": "application/json",
        "Accept": "application/json, text/javascript, */*; q=0.01",
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-Token": csrfToken
    };
    
    $(document).ready(function() {
        window.setInterval(callPing, <?php echo Configure::read('pingTime');?>);
    });

    function callPing() {
        var url = '<?php echo Configure::read('pingAjaxUrl');?>';
        /* console.log("Script.callPing "+url);  */

        var httpRequest = new XMLHttpRequest();
        httpRequest.open('GET', url);
        httpRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        httpRequest.setRequestHeader("Content-type", "application/json");
        httpRequest.setRequestHeader('X-CSRF-Token', csrfToken);
        httpRequest.send(null);
   }   
    </script>                           
</main>
   </body>
</html>