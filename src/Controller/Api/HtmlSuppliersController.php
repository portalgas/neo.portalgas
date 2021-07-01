<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class HtmlSuppliersController extends AppController
{
    public function initialize(): void 
    {
        parent::initialize();
    }

    public function beforeFilter(Event $event): void {
     
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['get']); 
    }

    /* 
     * front-end - dettaglio produttori    
     */
    public function get() {

        $debug = false;

        $results = [];
        
        $user = $this->Authentication->getIdentity();

        $supplier_id = $this->request->getData('supplier_id');

        $suppliersTable = TableRegistry::get('Suppliers'); 

        $where = [];
        $where['Suppliers'] = ['Suppliers.stato' => 'Y'];
        $results = $suppliersTable->getById($user, $supplier_id, $where, $debug);

        $supplierResult = $results;

$js = '';
if(!empty($supplierResult->lat) && !empty($supplierResult->lng)) {

    $name = str_replace("'", "", $supplierResult->name);
    $indirizzo = str_replace("'", "", $supplierResult->indirizzo);
    $localita = str_replace("'", "", $supplierResult->localita);

    $config = Configure::read('Config');
    $portalgas_app_root = $config['Portalgas.App.root'];
    $portalgas_fe_url = $config['Portalgas.fe.url'];

    $img_path_supplier = sprintf(Configure::read('Supplier.img.path.full'), $results->img1);
    $img_path_supplier = $portalgas_app_root.$img_path_supplier;

    $url = '';
    if(!empty($supplierResult->img1) && file_exists($img_path_supplier)) {
        
        $url = sprintf($portalgas_fe_url.Configure::read('Supplier.img.path.full'), $supplierResult->img1);
    }

    $js = "
    <script src='https://maps.googleapis.com/maps/api/js?key=".Configure::read('GoogleApiKey')."&v=3.exp'></script>
    <script type='text/javascript'>
    var icon1 = '".$portalgas_fe_url."/images/cake/puntina.png';
    var icon2 = '".$portalgas_fe_url."/images/cake/puntina03.png';
alert(icon1);
    $(document).ready(function () {

        var map;
        var myOptions = {
            zoom: 6,
            center: new google.maps.LatLng(".$supplierResult->lat.", ".$supplierResult->lng."),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        alert(myOptions);
        map = new google.maps.Map(document.getElementById('map'), myOptions);
            
            var latlng = new google.maps.LatLng(".$supplierResult->lat.", ".$supplierResult->lng.");
            
            var contentString".$supplierResult->id." = '<h3>".$name."</h3>' + 
                    '<p><img width='50' src=\"".$url."\"></p>' +
                    '<p><b>Indirizzo</b> ".$supplierResult." ".$localita."';
                        
            var infowindow".$supplierResult->id." = new google.maps.InfoWindow({
                    content: contentString".$supplierResult->id."
            });

          
            marker[0] = new google.maps.Marker({
                                        position: latlng,
                                        map: map,
                                        icon: icon1,
                                        title: '".$supplierResult->name."'
                                    });
            
            google.maps.event.addListener(marker[0], 'click', function() {
                infowindow".$results->id.".open(map, this);
            });
            
            google.maps.event.addListener(marker[0], 'mouseover', function() {
                this.setIcon(icon2);
            });
            
            google.maps.event.addListener(marker[0], 'mouseout', function() {
                this.setIcon(icon1);
            });
            
        $('.listsUser > li > a').mouseover(function () {
            var supplier_id = $(this).attr('data-attr-id');
            marker[supplier_id].setIcon(icon2);
            return false;
        });
        
        $('.listsUser > li > a').mouseout(function () {
            var supplier_id = $(this).attr('data-attr-id');
            marker[supplier_id].setIcon(icon1);
            return false;
        });
        
        $('.listsUser > li > a').click(function () {
            var supplier_id = $(this).attr('data-attr-id');
            google.maps.event.trigger(marker[supplier_id], 'click');
            return false;
        });
        
    });
    </script>
    ";
} // end if(!empty($results->lat) && !empty($results->lng)) 


        $this->set(compact('results', 'js'));

        $this->viewBuilder()->setLayout('ajax');
    } 
}