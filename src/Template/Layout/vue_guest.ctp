<?php
/*
 * controller src\Controller\PagesController.php
 */
use Cake\Core\Configure;

$config = Configure::read('Config');
$portalgas_fe_url = $config['Portalgas.fe.url'];
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
    <title><?php echo Configure::read('html.title'); ?></title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->AssetMix->css('app') ?>
    <?= $this->AssetMix->script('app') ?>
    <?php echo $this->element('fe/include_css');?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>

    <?php echo $this->element('google_analytics');?>
</head>
<body class="d-flex flex-column min-vh-100">
<main role="main" class="container-fluid">

  <a name="top" id="top"></a>

    <?php echo $this->element('fe/menu_guest', ['config' => $config]);?>

	<noscript><strong>We're sorry but vue doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript>

	<?php echo $this->fetch('content');?>
    
    <?php echo $this->element('fe/footer', ['config' => $config]);?>
    <?php echo $this->element('fe/include_js');?>
    
    <script type="text/javascript">
    "use strict";
    var csrfToken = <?php echo json_encode($this->request->getParam('_csrfToken')) ?>;
    var j_seo;
    var organizationTemplatePayToDelivery;

    var headers = {
        "Content-Type": "application/json",
        "Accept": "application/json, text/javascript, */*; q=0.01",
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-Token": csrfToken
    };
    
    $(document).ready(function() {
        window.setInterval(callPing, <?php echo Configure::read('pingTime');?>);

        var a = $('a[href="<?php echo $this->Url->build() ?>"]');
        // console.log("<?php echo $this->Url->build() ?>");
        if (!a.parent().hasClass('treeview') && !a.parent().parent().hasClass('pagination')) {
            a.parent().addClass('active').parents('.treeview').addClass('active');
        }

        $('[data-toggle="tooltip"]').tooltip()
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