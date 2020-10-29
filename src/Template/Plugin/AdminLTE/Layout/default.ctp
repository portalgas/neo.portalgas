<?php use Cake\Core\Configure;?>

<!DOCTYPE html>
<html>
<head>
  <title><?php echo Configure::read('Theme.title'); ?> | <?php echo $this->fetch('title'); ?></title>
  <?php echo $this->element('include_meta'); ?>
  <?php echo $this->element('include_css'); ?>
  <?php echo $this->fetch('css');?>
</head>
<?php
$class_menu_sidebar = ''; // setta se presentare il menu laterale aperto o chiuso
if(Configure::read('Theme.menu_sidebar')=='close')
  $class_menu_sidebar = 'sidebar-collapse';
?>
<body class="hold-transition skin-<?php echo Configure::read('Theme.skin'); ?> sidebar-mini <?php echo $class_menu_sidebar;?>">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo $this->Url->build('/admin'); ?>" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><?php echo Configure::read('Theme.logo.mini'); ?></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><?php echo Configure::read('Theme.logo.large'); ?></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <?php echo $this->element('nav-top'); ?>
  </header>

  <?php echo $this->element('aside-main-sidebar'); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <?php echo $this->Flash->render(); ?>
    <?php echo $this->Flash->render('auth'); ?>

    <section class="content">
      <?php echo $this->fetch('content'); ?>
    </section>

  </div>
  <!-- /.content-wrapper -->

  <?php echo $this->element('footer'); ?>

  <?php echo $this->element('aside-control-sidebar'); ?>

  <!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<?php echo $this->element('include_js'); ?>
<?php echo $this->fetch('scriptInclude'); ?>
<?php echo $this->fetch('scriptPageInclude'); ?>

<script type="text/javascript">
"use strict";
var objScript;
var csrfToken = <?php echo json_encode($this->request->getParam('_csrfToken')) ?>;
var headers = {
    "Content-Type": "application/json",
    "Accept": "application/json, text/javascript, */*; q=0.01",
    "X-Requested-With": "XMLHttpRequest",
    "X-CSRF-Token": csrfToken
};
var http = axios.create({
    headers: headers
});

$(document).ready(function() {

    var a = $('a[href="<?php echo $this->Url->build() ?>"]');
    if (!a.parent().hasClass('treeview') && !a.parent().parent().hasClass('pagination')) {
        a.parent().addClass('active').parents('.treeview').addClass('active');
    }

    if (Boolean(sessionStorage.getItem('sidebar-toggle-collapsed'))) {
      var body = document.getElementsByTagName('body')[0];
      body.className = body.className + ' sidebar-collapse';
    }

    // Click handler can be added latter, after jQuery is loaded...
    $('.sidebar-toggle').click(function(event) {
      event.preventDefault();
      if (Boolean(sessionStorage.getItem('sidebar-toggle-collapsed'))) {
        sessionStorage.setItem('sidebar-toggle-collapsed', '');
      } else {
        sessionStorage.setItem('sidebar-toggle-collapsed', '1');
      }
    });

    objScript = new Script(); 
    objScript.ping('<?php echo Configure::read('pingAjaxUrl');?>', <?php echo Configure::read('pingTime');?>);

    var anchor = objScript.getUrlAnchor();
    if(anchor!='') {
        $('#'+anchor).css('background-color', 'yellow');
    }    

    /*
    tinymce.init({ mode : 'specific_textareas',
                   editor_selector : "editor", 
                   theme : "silver"
                 });
    */
    var myEditor;
    if($('.editor').length>0) {
      ClassicEditor
            .create(document.querySelector( '.editor' ), {
                toolbar: <?php echo Configure::read('ckeditor5.toolbar');?>  
            }
          )  
          .then( editor => {
              console.log( 'Editor was initialized', editor );
              myEditor = editor;
          })
          .catch( error => {
              console.error( error );
          });      
    }
});
</script>  
  
<?php echo $this->fetch('script'); ?>
<?php echo $this->fetch('scriptBottom'); ?>

</body>
</html>