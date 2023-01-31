<?php 
use Cake\Core\Configure;
use App\Traits;

$config = Configure::read('Config');
$portalgas_bo_url = $config['Portalgas.bo.url'];
$portalgas_fe_url = $config['Portalgas.fe.url']; 
?>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_0_<?php echo $order_id;?>" data-toggle="tab" aria-expanded="true" data-attr-doc-options="to-articles">Doc. con articoli aggregati</a></li>
        <li><a href="#tab_1_<?php echo $order_id;?>" data-toggle="tab" aria-expanded="true" data-attr-doc-options="to-articles-details">Doc. con articoli aggregati per utenti</a></li>
        <li class=""><a href="#tab_2_<?php echo $order_id;?>" data-toggle="tab" aria-expanded="false" data-attr-doc-options="to-users">Doc. con elenco diviso per utente</a></li>
        <li><a href="#tab_3_<?php echo $order_id;?>" data-toggle="tab" data-attr-doc-options="to-articles-weight">Doc. con articoli aggregati per peso</a></li>
        <li><a href="#tab_4_<?php echo $order_id;?>" data-toggle="tab" data-attr-doc-options="related-articles">Articoli associati all'ordine</a></li>
        <!-- li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li -->
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_0_<?php echo $order_id;?>"></div>
        <div class="tab-pane active" id="tab_1_<?php echo $order_id;?>"></div>
        <div class="tab-pane" id="tab_2_<?php echo $order_id;?>"></div>
        <div class="tab-pane" id="tab_3_<?php echo $order_id;?>"></div>
        <div class="tab-pane" id="tab_4_<?php echo $order_id;?>"></div>
    </div>
    <!-- /.tab-content -->
</div>

<?php 
echo '<script>';
echo "
function callReport(target, doc_options) {
    let ajaxUrl = '';
    let htmlResult = $(target);

    htmlResult.html('');
    htmlResult.css('min-height', '50px');
    htmlResult.css('background', 'url(\"/images/cake/ajax-loader.gif\") no-repeat scroll center 0 transparent');

    if(typeof doc_options!=='undefined' && doc_options!='') {
        var a = '';
        var b = '';
        var c = '';
        var d = '';
        var e = '';
        var f = '';
        var g = '';
        var h = '';
        if(doc_options=='to-users-all-modify') {
            a = 'N'; 
        }
        else	
        if(doc_options=='to-users') {
            a = 'Y'; 
            b = 'Y'; 
            c = 'N'; 
            d = 'Y'; 
            e = 'N'; 
            f = 'N'; 
            g = 'Y'; 
            h = 'N'; 
        }
        else
        if(doc_options=='to-users-label') {
            a = 'Y'; 
            b = 'Y'; 
            c = 'N'; 
            d = 'N'; 
            e = 'N'; 
        }
        else
        if(doc_options=='to-articles-monitoring') {
            a = 'N';  
        }
        else
        if(doc_options=='to-articles') {
            a = 'N';  
            b = 'Y';  
        }
        else
        if(doc_options=='to-articles-details') {
            a = 'Y'; 
            b = 'N'; 
            c = 'N'; 
            d = 'Y'; 
            e = 'Y'; 
        }
        
        if(doc_options=='to-articles-weight') 
            ajaxUrl = '".$portalgas_bo_url."/administrator/index.php?option=com_cake&controller=ExportDocs&action=exportToArticlesWeight&delivery_id=".$delivery_id."&order_id=".$order_id."&doc_options='+doc_options+'&doc_formato=PREVIEW&scope=neo&format=notmpl';
        else
        if(doc_options=='related-articles')
            ajaxUrl = '".$portalgas_bo_url."/administrator/index.php?option=com_cake&controller=Ajax&action=view_orders&order_id=".$order_id."&scope=neo&format=notmpl';
        else
            ajaxUrl = '".$portalgas_bo_url."/administrator/index.php?option=com_cake&controller=ExportDocs&action=exportToReferent&delivery_id=".$delivery_id."&order_id=".$order_id."&doc_options='+doc_options+'&doc_formato=PREVIEW&a='+a+'&b='+b+'&c='+c+'&d='+d+'&e='+e+'&f='+f+'&g='+g+'&h='+h+'&scope=neo&format=notmpl';          
    } // end if(doc_options!='')

    /* console.log(ajaxUrl, 'ajaxUrl'); */ 
    if(ajaxUrl!='') {
        $.ajax({url: ajaxUrl,  
            type: 'GET',
            dataType: 'html',
            cache: false,
            xhrFields: {
                withCredentials: true
            },                           
            success: function (response) {
                console.log(response.responseText, 'responseText');
            },
            error: function (e) {
                console.error(e, ajaxUrl);
            },
            complete: function (e) {
                htmlResult.css('background', 'none repeat scroll 0 0 transparent');
                htmlResult.html(e.responseText);
            }
        });    
    } // end if(ajaxUrl!='')       
}


$(function () {
    $('a[data-toggle=\"tab\"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr(\"href\") // activated tab
        let doc_options = $(e.target).attr('data-attr-doc-options');
        console.log(doc_options, 'doc_options');        
        console.log(target, 'target');

        callReport(target, doc_options);
    });

    callReport('#tab_0_$order_id', 'to-articles');
});
";
echo '</script>';
?>