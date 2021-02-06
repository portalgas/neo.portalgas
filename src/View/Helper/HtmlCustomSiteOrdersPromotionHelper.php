<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersPromotionHelper extends FormHelper
{
	private $debug = false;
	public  $helpers = ['Html', 'Form', 'HtmlCustom'];

    public function initialize(array $config)
    {
        // debug($config);
    }

    public function supplierOrganizations($suppliersOrganizations) {
        return $this->Form->control('supplier_organization_id', ['options' => $suppliersOrganizations, '@change' => 'getSuppliersOrganization']);
    }

    public function deliveries($deliveries) {
        return $this->Form->control('delivery_id', ['options' => $deliveries]);
    }

    public function infoParent($results) {

        if(empty($results)) 
            return '';

        if(!isset($prodGasArticlesPromotionShow))
            $prodGasArticlesPromotionShow = false;

        $html = '';
        $html .= '<div>';
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
        $html .= '<span style="text-decoration: line-through;">'.$results->importo_originale.'</span><br />'.$results->importo_scontato;
        $html .= '</td>';
        $html .= '<td>';
        if($results->prodGasPromotionsOrganizations->hasTrasport=='Y')   
            $html .= $results->prodGasPromotionsOrganizations->trasport_e;
        else
            $html .= "Nessun costo di trasporto";
        $html .= '</td>';
        $html .= '<td>';
        if($results->prodGasPromotionsOrganizations->hasCostMore=='Y')   
            $html .= $results->prodGasPromotionsOrganizations->cost_more_e;
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
            $html .= '<div id="collapseProdGasArticlesPromotions" class="panel-collapse collaps ine" aria-expanded="false">';
            $html .= '<div class="box-body">';
             
            $html .= '<div class="table-responsive"><table class="table table-hover">';    
            $html .= '<thead><tr>';   
            $html .= '<th scope="col" colspan="2">'.__('Name').'</th>';    
            $html .= '<th scope="col">'.__('Package').'</th>'; 
            $html .= '<th scope="col" style="text-align:center;">'.__('ProdGasPromotion-Qta').'</th>';    
            $html .= '<th scope="col" style="text-align:center;">'.__('PrezzoUnita').'</th>';      
            $html .= '<th style="text-align:center;">'.__('ImportoOriginale').'</th>'; 
            $html .= '<th scope="col" style="text-align:center;">'.__('ProdGasPromotion-PrezzoUnita').'</th>';
            $html .= '<th scope="col" style="text-align:center;">'.__('ImportoTotaleScontato').'</th>';  
            $html .= '</tr></thead><tbody>';
             
            foreach ($results->prodGasArticlesPromotions as $numResult => $prodGasArticlesPromotion) {
        
                $html .= '<tr>';
                $html .= '<td>';
                if(!empty($prodGasArticlesPromotion->article->img1)) {
                    $html .= '<img class="img-article responsive" src="'.$prodGasArticlesPromotion->article->img1.'" />';
                }       
                $html .= '</td>';           
                $html .= '<td>'.$prodGasArticlesPromotion->article->name.'&nbsp;';
                $html .= $this->HtmlCustom->note($prodGasArticlesPromotion->article->nota);
                $html .= '</td>';
                $html .= '<td style="text-align:center;">';
                $html .= $prodGasArticlesPromotion->article->conf;
                $html .= '</td>';
                $html .= '<td style="text-align:center;">'.$prodGasArticlesPromotion->qta.'</td>';
                $html .= '<td style="text-align:center;"><span style="text-decoration: line-through;">'.$prodGasArticlesPromotion->article->prezzo.'</span></td>';
                $html .= '<td style="text-align:center;"><span style="text-decoration: line-through;">'.$prodGasArticlesPromotion->importo_originale.'&nbsp;&euro;</span></td>';
                $html .= '<td style="text-align:center;">'.$prodGasArticlesPromotion->prezzo_unita.'</td>';
                $html .= '<td style="text-align:center;">';
                $html .= '<span style="text-decoration: line-through;">'.$prodGasArticlesPromotion->importo_originale.'&nbsp;&euro;</span><br />';
                $html .= $prodGasArticlesPromotion->importo.'</td>';
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
}