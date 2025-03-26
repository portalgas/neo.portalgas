<?php use Cake\Core\Configure;

// jQuery 3
echo $this->Html->script('AdminLTE./bower_components/jquery/dist/jquery.min', ['block' => 'scriptInclude']);


// Bootstrap 3.3.7 -->
echo $this->Html->script('AdminLTE./bower_components/bootstrap/dist/js/bootstrap.min', ['block' => 'scriptInclude']);

// AdminLTE App
echo $this->Html->script('AdminLTE.adminlte.min', ['block' => 'scriptInclude']);

// Slimscroll
echo $this->Html->script('AdminLTE./bower_components/jquery-slimscroll/jquery.slimscroll.min', ['block' => 'scriptInclude']);

// FastClick
echo $this->Html->script('AdminLTE./bower_components/fastclick/lib/fastclick', ['block' => 'scriptInclude']);

// Select2 https://select2.github.io/
echo $this->Html->script('AdminLTE./bower_components/select2/dist/js/select2.full.min', ['block' => 'scriptInclude']);

echo $this->Html->script('AdminLTE./bower_components/moment/moment', ['block' => 'scriptInclude']);

// bootstrap datepicker
echo $this->Html->script('AdminLTE./bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min', ['block' => 'scriptInclude']);

// bootstrap-daterangepicker
echo $this->Html->script('AdminLTE./bower_components/bootstrap-daterangepicker/daterangepicker', ['block' => 'scriptInclude']);

// iCheck 1.0.1 for checkbox and radio inputs http://fronteed.com/iCheck/
// conflitto con Vue
echo $this->Html->script('AdminLTE./plugins/iCheck/icheck.min', ['block' => 'scriptInclude']);

// Vue.js v2.6.10
echo $this->Html->script('vue/vue.js', ['block' => 'scriptInclude']);
echo $this->Html->script('vue/router.js', ['block' => 'scriptInclude']);
echo $this->Html->script('vue/vuex.js', ['block' => 'scriptInclude']);
echo $this->Html->script('vue/axios.js', ['block' => 'scriptInclude']);
echo $this->Html->script('moment/moment-with-locales.min.js', ['block' => 'scriptInclude']);

// echo '<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>';
// https://ckeditor.com/docs/ckeditor5/latest/builds/guides/integration/configuration.html
echo $this->Html->script('ckeditor5/ckeditor.js', ['block' => 'scriptInclude']);

// istanzio objScript = new Script();
echo $this->Html->script('scripts.js?v=20250326', ['block' => 'scriptInclude']);

/*
<script type="text/javascript" src='http://maps.google.it/maps/api/js?sensor=false&libraries=places'></script>
<script>
function init()
{
   var input = document.getElementById('address');
   var autocomplete = new google.maps.places.Autocomplete(input);
}
google.maps.event.addDomListener(window, 'load', init);
</script>
*/

// echo $this->Html->script('https://code.jquery.com/ui/1.14.1/jquery-ui.js', ['block' => 'scriptInclude']);
?>

