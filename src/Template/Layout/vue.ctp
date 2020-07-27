<?php
$cakeDescription = 'CakeVue Application';
?>
<!DOCTYPE html>
<html lang=en>
<head>
    <?= $this->Html->charset() ?>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->AssetMix->css('app') ?>
    <?= $this->AssetMix->script('app') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>	
</head>
<body>

	<noscript><strong>We're sorry but vue doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript>
	
	<?php echo $this->fetch('content');?>
    
<script type="text/javascript">
"use strict";
var csrfToken = <?php echo json_encode($this->request->getParam('_csrfToken')) ?>;
</script>    
</body>
</html>