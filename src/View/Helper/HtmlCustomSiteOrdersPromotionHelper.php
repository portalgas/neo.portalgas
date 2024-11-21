<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;

class HtmlCustomSiteOrdersPromotionHelper extends HtmlCustomSiteOrdersHelper
{
	private $debug = false;
	public $helpers = ['Html', 'Form', 'HtmlCustom', 'HtmlCustomSite'];

    public function initialize(array $config)
    {
        parent::initialize($config);
        // debug($config);
    }

    // eventuale msg in index
    public function msg() {
        return '';
    }

    public function hiddenFields() {

        $html = '';
        $html .= $this->Form->control('organization_id', ['type' => 'hidden', 'value' => $this->_user->organization->id, 'required' => 'required']);

        // prod_gas_promotion_id
        $html .= $this->Form->control('parent_id', ['type' => 'hidden', 'value' => $this->_parent->id, 'required' => 'required']);

        return $html;
    }

    public function supplierOrganizations($suppliersOrganizations, $options=[]) {
        if(!isset($options['ctrlDesACL'])) $options['ctrlDesACL'] = true;
        if(!isset($options['empty'])) $options['empty'] = true;
        if(!isset($options['select2'])) $options['select2'] = true;
        return parent::supplierOrganizations($suppliersOrganizations, $options);
    }

    public function deliveries($deliveries, $options=[]) {
        return parent::deliveries($deliveries, $options);
    }

    public function deliveryOlds($order_type_id, $order, $parent, $delivery_olds) {
        return parent::deliveryOlds($order_type_id, $order, $parent, $delivery_olds);
    }

    public function data() {

        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
        $html .= $this->HtmlCustom->datepicker('data_inizio', ['autocomplete' => 'off']);
        $html .= '</div>';
        $html .= '<div class="col-md-6">';
        $html .= $this->HtmlCustom->datepicker('data_fine', ['autocomplete' => 'off']);
        $html .= '</div>';
        $html .= '</div>';

        $msg = "La promozione si chiuderà il ".$this->HtmlCustom->data($this->_parent->data_fine);

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
    public function extra() {
        return parent::extra();
    }

    /*
     * dettaglio promotion / order des
     */
    public function infoParent() {

        if(empty($this->_parent))
            return '';

        if(!isset($prodGasArticlesPromotionShow))
            $prodGasArticlesPromotionShow = false;

        $config = Configure::read('Config');
        $portalgas_fe_url = $config['Portalgas.fe.url'];

        $html = '';
        $html .= '<div class="info-parent">';
        $html .= '<h1>'.__('ProdGasPromotion').'</h1>';

        if(!empty($this->_parent->nota))
            $html .= $this->HtmlCustom->alert($this->parent->nota);

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
        if(!empty($this->_parent->suppliersOrganization->supplier->img1) && file_exists(Configure::read('App.root').Configure::read('App.img.upload.content').'/'.$this->_parent->suppliersOrganization->supplier->img1))
            $html .= '<img width="50" class="img-supplier responsive" src="'.$portalgas_fe_url.Configure::read('App.web.img.upload.content').'/'.$this->_parent->suppliersOrganization->supplier->img1.'" />';
        $html .= '</td>';
        $html .= '<td>';
        $html .= $this->_parent->suppliersOrganization->supplier->name;
        $html .= '</td>';
        $html .= '<td>';
        $html .= $this->_parent->name;
        $html .= '</td>';
        $html .= '<td>';
        $html .= $this->HtmlCustom->data($this->_parent->data_fine);
        $html .= '</td>';
        $html .= '<td>';
        $html .= '<span style="text-decoration: line-through;">'.$this->_parent->importo_originale.'&nbsp;&euro;</span>';
        $html .= '<br />'.$this->_parent->importo_scontato.'&nbsp;&euro;';
        $html .= '</td>';
        $html .= '<td>';
        if($this->_parent->prodGasPromotionsOrganizations->hasTrasport=='Y')
            $html .= $this->_parent->prodGasPromotionsOrganizations->trasport.'&nbsp;&euro;';
        else
            $html .= "Nessun costo di trasporto";
        $html .= '</td>';
        $html .= '<td>';
        if($this->_parent->prodGasPromotionsOrganizations->hasCostMore=='Y')
            $html .= $this->_parent->prodGasPromotionsOrganizations->cost_more.'&nbsp;&euro;';
        else
            $html .= "Nessun costo agguntivo";
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</tbody></table></div>';

        /*
         * articoli in promozione
         */
        if(isset($this->_parent->prodGasArticlesPromotions)) {
            $html .= '<div class="panel box box-primary">';
            $html .= '<div class="box-header with-border">';
            $html .= '<h4 class="box-title">';
            $html .= '<a data-toggle="collapse" data-parent="#accordion" href="#collapseProdGasArticlesPromotions" aria-expanded="false" class="collapsed">';
            $html .= __('ProdGasArticlesPromotions').' - dettaglio articoli '.count($this->_parent->prodGasArticlesPromotions);
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

            (count($this->_parent->prodGasArticlesPromotions)) > 5 ? $open = false : $open = true;
            foreach ($this->_parent->prodGasArticlesPromotions as $numResult => $prodGasArticlesPromotion) {

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

            } // end foreach ($this->_parent->prodGasArticlesPromotions as $numResult => $prodGasArticlesPromotion)

            $html .= '</tbody></table></div>';

            $html .= '</div>';  // box-body
            $html .= '</div>';  // box-header
            $html .= '</div>';  // panel box
        } // end isset($this->_parent->prodGasArticlesPromotions->)

        $html .= '</div>';

        return $html;
    }

    public function note() {
        return parent::note();
    }

    public function mailOpenTesto() {
        return parent::mailOpenTesto();
    }

    public function monitoraggio() {
        return parent::monitoraggio();
    }

    public function typeGest() {
        return parent::typeGest();
    }
}
