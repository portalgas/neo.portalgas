<?php
use Cake\Core\Configure;

$csrfToken = $this->request->getParam('_csrfToken');
if(empty($csrfToken))
    $csrfToken = $this->request->getAttribute('csrfToken');
?>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<?= $this->Html->meta('csrfToken', $csrfToken); ?>
