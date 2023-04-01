<?php
/*
 * controller src\Controller\PagesController.php
 */
use Cake\Core\Configure;

$config = Configure::read('Config');
$portalgas_fe_url = $config['Portalgas.fe.url'];
$portalgas_ping_session = $config['Portalgas.ping.session.FE'];

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

    <?php
    if(isset($organization->type) && $organization->type=='SOCIALMARKET')
        echo $this->element('fe/menu-socialmarket', ['config' => $config, 'organization' => $organization, 'user' => $this->Identity]);
    else
        echo $this->element('fe/menu', ['config' => $config, 'organization' => $organization, 'user' => $this->Identity, 'hasGasUsersPromotions' => $hasGasUsersPromotions]);
    ?>

	<noscript><strong>We're sorry but vue doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript>

	<?php echo $this->fetch('content');?>
    
    <?php echo $this->element('fe/footer', ['config' => $config, 'organization' => $organization, 'user' => $this->Identity]);?>
    <?php echo $this->element('fe/include_js'); ?>
    
    <script type="text/javascript">
    "use strict";

    <?php
    /*
     * posso essere in pagina public (ex /site/produttori) ma sono autenticato
     * => gestito in is_logged
     */
    ?>
    var csrfToken = <?php echo json_encode($this->request->getParam('_csrfToken')) ?>;
    var is_logged = <?php echo ($this->Identity->get()!==null) ? 'true' : 'false'; ?>;  // da passa a vue in app.js
    var j_seo = "<?php echo (isset($organization->j_seo)) ? $organization->j_seo : '';?>"; // da passa a vue in app.js
    var organizationTemplatePayToDelivery = "<?php echo (isset($organization) && isset($organization->template)) ? $organization->template->payToDelivery : '';?>"; // da passa a vue in app.js
    var organizationHasFieldCartNote = "<?php echo (isset($organization) && isset($organization->paramsFields) && isset($organization->paramsFields['hasFieldCartNote'])) ? $organization->paramsFields['hasFieldCartNote'] : 'N';?>"; // da passa a vue in app.js
    var headers = {
        "Content-Type": "application/json",
        "Accept": "application/json, text/javascript, */*; q=0.01",
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-Token": csrfToken
    };
    
    $(document).ready(function() {
        window.setInterval(callPing, <?php echo Configure::read('pingTime');?>);
        /* cors domain window.setInterval(callPingJoomla, <?php echo Configure::read('pingTime');?>); */

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
        httpRequest.open('GET', url, true);
        httpRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        httpRequest.setRequestHeader("Content-type", "application/json");
        httpRequest.setRequestHeader('X-CSRF-Token', csrfToken);
        httpRequest.send(null);
    }   
    function callPingJoomla() { 
        var url = '<?php echo $portalgas_ping_session;?>';
        /* console.log("Script.callPingJoomla "+url);  */
        var httpRequest = new XMLHttpRequest();
        httpRequest.open('GET', url, true);
        httpRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        httpRequest.withCredentials = true;
        httpRequest.send(null);
    }   
    </script>                           
</main>
   </body>
</html>