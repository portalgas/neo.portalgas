 <?php
use Cake\Core\Configure;

$config = Configure::read('Config');
$google_analytics_isActive = $config['google.analytics.isActive'];

if($google_analytics_isActive) {
?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KPKQD4NFR1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-KPKQD4NFR1');
    </script>
<?php
}
?>