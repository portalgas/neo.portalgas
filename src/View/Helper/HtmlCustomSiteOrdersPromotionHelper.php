<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersPromotionHelper extends HtmlCustomSiteOrdersHelper
{
	private $debug = false;
	public  $helpers = ['Html', 'Form', 'HtmlCustom'];

    public function initialize(array $config)
    {
        parent::initialize($config);
        // debug($config);
    }

    public function setUser($user) {
        parent::setUser($user);
    }

    public function hiddenFields($organization_id, $parent) {

        $html = '';
        $html .= $this->Form->control('organization_id', ['type' => 'hidden', 'value' => $organization_id, 'required' => 'required']);

        // prod_gas_promotion_id
        $html .= $this->Form->control('parent_id', ['type' => 'hidden', 'value' => $parent->id, 'required' => 'required']);
        
        return $html;
    }

    public function supplierOrganizations($suppliersOrganizations, $options=[]) {
        if(!isset($options['ctrlDesACL'])) $options['ctrlDesACL'] = true;
        if(!isset($options['empty'])) $options['empty'] = true; 
        if(!isset($options['select2'])) $options['select2'] = true;          
        return parent::supplierOrganizations($suppliersOrganizations, $options);
    }

    public function deliveries($deliveries) {
        return $this->Form->control('delivery_id', ['options' => $deliveries]);
    }

    public function data($parent) {

        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">'; 
        $html .= $this->HtmlCustom->datepicker('data_inizio', ['autocomplete' => 'off']);
        $html .= '</div>'; 
        $html .= '<div class="col-md-6">'; 
        $html .= $this->HtmlCustom->datepicker('data_fine', ['autocomplete' => 'off']);
        $html .= '</div>'; 
        $html .= '</div>';

        $msg = "La promozione si chiuderà il ".$this->HtmlCustom->data($parent->data_fine);

        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">'; 
        $html .= $this->HtmlCustom->alert($msg);
        $html .= '</div>'; 
        $html .= '</div>';        

        return $html;
    }

    /*
     * trasport / cost_more / cost_less
     */
    public function extra($order, $parent) {
        return parent::extra($order, $parent);
    }

    /*
     * dettaglio promotion / order des
     */
    public function infoParent($results) {

        if(empty($results)) 
            return '';

        if(!isset($prodGasArticlesPromotionShow))
            $prodGasArticlesPromotionShow = false;

        $html = '';
        $html .= '<div class="info-parent">';
        $html .= '<h1>'.__('ProdGasPromotion').'</h1>';
        
        if(!empty($results->nota)) 
            $html .= $this->HtmlCustom->alert($results->nota);
    
        $html .= '<div class="table-responsive"><table class="table table-hover"><thead>';
        $html .= '<tr>';
        $html .= '<th scope="col" colspan="2">'.__('Supplier').'</th>';
        $html .= '<th scope="col">'.__('Name').'</th>';
        $html .= '<th scope="col">'.__('DataFineMaxPromotion').'</th>';
        $html .= '<th scope="col">'.__('ImportoScontato').'</th>'; 
        $html .= '<th scope="col">'.__('CostTrasport').'</th>'; 
        $html .= '<th scope="col">'.__('CostMore').'</th>';         
        $html .= '</thead></tr>';
        
        $html .= '<tbody><tr>';
            
        $html .= '<td>';
        if(!empty($results->suppliersOrganization->supplier->img1) && file_exists(Configure::read('App.root').Configure::read('App.img.upload.content').'/'.$results->suppliersOrganization->supplier->img1))
            $html .= '<img width="50" class="img-supplier responsive" src="'.Configure::read('App.server').Configure::read('App.web.img.upload.content').'/'.$results->suppliersOrganization->supplier->img1.'" />';    
        $html .= '</td>';
        $html .= '<td>';
        $html .= $results->suppliersOrganization->supplier->name;
        $html .= '</td>';
        $html .= '<td>';
        $html .= $results->name;
        $html .= '</td>';
        $html .= '<td>';
        $html .= $this->HtmlCustom->data($results->data_fine); 
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<span style="text-decoration: line-through;">'.$results->importo_originale.'&nbsp;&euro;</span>';
        $html .= '<br />'.$results->importo_scontato.'&nbsp;&euro;';
        $html .= '</td>';
        $html .= '<td>';
        if($results->prodGasPromotionsOrganizations->hasTrasport=='Y')   
            $html .= $results->prodGasPromotionsOrganizations->trasport.'&nbsp;&euro;';
        else
            $html .= "Nessun costo di trasporto";
        $html .= '</td>';
        $html .= '<td>';
        if($results->prodGasPromotionsOrganizations->hasCostMore=='Y')   
            $html .= $results->prodGasPromotionsOrganizations->cost_more.'&nbsp;&euro;';
        else
            $html .= "Nessun costo agguntivo";
        $html .= '</td>';   
        $html .= '</tr>';
        $html .= '</tbody></table></div>'; 
        
        /* 
         * articoli in promozione
         */
        if(isset($results->prodGasArticlesPromotions)) {
            $html .= '<div class="panel box box-primary">';
            $html .= '<div class="box-header with-border">';
            $html .= '<h4 class="box-title">';
            $html .= '<a data-toggle="collapse" data-parent="#accordion" href="#collapseProdGasArticlesPromotions" aria-expanded="false" class="collapsed">';
            $html .= __('ProdGasArticlesPromotions').' - dettaglio articoli '.count($results->prodGasArticlesPromotions);
            $html .= '</a>';
            $html .= '</h4>';
            $html .= '</div>';
            $html .= '<div id="collapseProdGasArticlesPromotions" class="panel-collapse collaps in" aria-expanded="false">';
            $html .= '<div class="box-body">';
             
            $html .= '<div class="table-responsive"><table class="table table-hover">';    
            $html .= '<thead><tr>';   
            $html .= '<th scope="col" colspan="2">'.__('Name').'</th>';    
            $html .= '<th scope="col">'.__('Package').'</th>'; 
            $html .= '<th scope="col" style="text-align:center;">'.__('ProdGasPromotion-Qta').'</th>';    
            $html .= '<th scope="col" style="text-align:center;">'.__('PrezzoUnita').'</th>';      
            // $html .= '<th style="text-align:center;">'.__('ImportoOriginale').'</th>'; 
            $html .= '<th scope="col" style="text-align:center;">'.__('ProdGasPromotion-PrezzoUnita').'</th>';
            $html .= '<th scope="col" style="text-align:center;">'.__('ImportoTotaleScontato').'</th>';  
            $html .= '</tr></thead><tbody>';
             
            (count($results->prodGasArticlesPromotions)) > 5 ? $open = false : $open = true; 
            foreach ($results->prodGasArticlesPromotions as $numResult => $prodGasArticlesPromotion) {
        
                $html .= '<tr>';
                $html .= '<td>';
                if(!empty($prodGasArticlesPromotion['img1'])) {
                    $html .= '<img class="img-article responsive" src="'.$prodGasArticlesPromotion['img1'].'" />';
                }       
                $html .= '</td>';           
                $html .= '<td>'.$prodGasArticlesPromotion['name'].'&nbsp;';
                $html .= $this->HtmlCustom->noteMore($prodGasArticlesPromotion['nota']);
                $html .= '</td>';
                $html .= '<td style="text-align:center;">';
                $html .= $prodGasArticlesPromotion['conf'];
                $html .= '</td>';
                $html .= '<td style="text-align:center;">'.$prodGasArticlesPromotion['qta'].'</td>';
                $html .= '<td style="text-align:center;"><span style="text-decoration: line-through;">'.$prodGasArticlesPromotion['price_pre_discount'].'&nbsp;&euro;</span></td>';
                // $html .= '<td style="text-align:center;"><span style="text-decoration: line-through;">'.$prodGasArticlesPromotion['importo_originale'].'&nbsp;&euro;</span></td>';
                $html .= '<td style="text-align:center;">'.$prodGasArticlesPromotion['prezzo_unita'].'&nbsp;&euro;</td>';
                $html .= '<td style="text-align:center;">';
                $html .= '<span style="text-decoration: line-through;">'.$prodGasArticlesPromotion['importo_originale'].'&nbsp;&euro;</span><br />';
                $html .= $prodGasArticlesPromotion['importo_scontato'].'&nbsp;&euro;</td>';
                $html .= '</tr>';
                            
            } // end foreach ($results->prodGasArticlesPromotions as $numResult => $prodGasArticlesPromotion)
            
            $html .= '</tbody></table></div>';
             
            $html .= '</div>';  // box-body
            $html .= '</div>';  // box-header
            $html .= '</div>';  // panel box 
        } // end isset($results->prodGasArticlesPromotions->)    
         
        $html .= '</div>';

        return $html;
    }

    public function note() {
        return parent::note();     
    } 

    public function mailOpenTesto() {
        return parent::mailOpenTesto();     
    }  
    
    public function monitoraggio($results) {
        return parent::monitoraggio($results);
    }

    public function typeGest($results) {
        return parent::typeGest($results);
    }    
}