<?php
use Cake\Core\Configure;

$config = Configure::read('Config');
$portalgas_ping_session = $config['Portalgas.ping.session.BO'];
$portalgas_bo_url = $config['Portalgas.bo.url'];        
?>

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
var objScript = null;
var portalgas_bo_url = '<?php echo $portalgas_bo_url;?>';
var csrfToken = <?php echo json_encode($this->request->getParam('_csrfToken')) ?>;
var orderNotaMaxLen = <?php echo Configure::read('OrderNotaMaxLen');?>;
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
    /* cors domain 
     * objScript.ping('<?php echo $portalgas_ping_session;?>', <?php echo Configure::read('pingTime');?>);
     */
    window.setInterval(callPingJoomla, <?php echo Configure::read('pingTime');?>, '<?php echo $portalgas_ping_session;?>');
    
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

function callPingJoomla(ajaxUrl) {
    $.ajax({url: ajaxUrl,  
      type: 'GET',
      dataType: 'html',
      cache: false,
      xhrFields: {
          withCredentials: true
      },                           
      success: function (response) {
          // console.log(response, 'callPingJoomla');
      },
      error: function (e) {
          console.error(e, ajaxUrl);
      }
    });
} 
</script>  
  
<?php echo $this->fetch('script'); ?>
<?php echo $this->fetch('scriptBottom'); ?>

<?php echo $this->fetch('bottom'); // order::add/edit delivery per modal ?>

<div id="modalHelps" class="modal fade">
 <div class="modal-dialog modal-lg">
  <div class="modal-content">
   <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"></h4>
   </div->
   <div class="modal-body" style="overflow: auto;">
    <p><img src="" id="modalImgHelp" /></p>
   </div>
   <div class="modal-footer">
    <button type="button" class="btn btn-primary" data-dismiss="modal">Chiudi</button>
   </div>
  </div>
 </div>
</div>

</body>

<script>
/* torna in alto */
$("body").append("<div id=\"scroll_to_top\"><a href=\"#top\">Torna su</a></div>");
$("#scroll_to_top a").css({	'display' : 'block', 'z-index' : '9', 'position' : 'fixed', 'top' : '100%', 'width' : '110px', 'margin-top' : '-30px', 'right' : '50%', 'margin-left' : '-50px', 'height' : '20px', 'padding' : '3px 5px', 'font-size' : '14px', 'text-align' : 'center', 'padding' : '3px', 'color' : '#FFFFFF', 'background-color' : '#625043', '-moz-border-radius' : '5px', '-khtml-border-radius' : '5px', '-webkit-border-radius' : '5px', 'opacity' : '.8', 'text-decoration' : 'none'});
$('#scroll_to_top a').click(function(){
  $('html, body').animate({scrollTop:0}, 'slow');
});
var scroll_timer;
var displayed = false;
var top = $(document.body).children(0).position().top;
$(window).scroll(function () {
  window.clearTimeout(scroll_timer);
  scroll_timer = window.setTimeout(function () {
    if($(window).scrollTop() <= top)
    {
      displayed = false;
      $('#scroll_to_top a').fadeOut(500);
    }
    else if(displayed == false)
    {
      displayed = true;
      $('#scroll_to_top a').stop(true, true).show().click(function () { $('#scroll_to_top a').fadeOut(500); });
    }
  }, 100);
});
</script>
</html>