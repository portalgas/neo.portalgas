<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;
use App\Traits;

class HtmlCustomSiteHelper extends FormHelper
{
    use Traits\UtilTrait;

	private $debug = false;
	public  $helpers = ['Url', 'Html', 'Form', 'HtmlCustom'];

    public function initialize(array $config)
    {
        // debug($config);
        $config = Configure::read('Config');
    }

    public function translateMappingTypeCode($mapping, $options=[]) {

    	$results = '';
        switch($mapping->mapping_type->code) {
            case 'CURRENTDATE':
              $results = date('Y-m-d');
            break;
            case 'CURRENTDATETIME':
              $results = date('Y-m-d H:i:s');
            break;
            case 'FUNCTION':
                if(!empty($mapping->master_json_xpath))
                    $results .= 'Da <b>json</b> '.$mapping->master_json_xpath.' => ';
                else            
                if(!empty($mapping->master_xml_xpath))
                    $results .= 'Da <b>xml</b> '.$mapping->master_xml_xpath.' => ';
                else         
                if(!empty($mapping->master_csv_num_col))
                    $results .= 'Da <b>col</b> '.$mapping->master_csv_num_col.' => ';

                $results .= '<b>Function</b> '.$mapping->value;
            break;
            case 'DEFAULT':
                if(!empty($mapping->master_json_xpath))
                    $results = '<b>json</b> '.$mapping->master_json_xpath;
                else
                if(!empty($mapping->master_xml_xpath))
                    $results = '<b>xml</b> '.$mapping->master_xml_xpath;
                else
                if(!empty($mapping->master_csv_num_col))
                    $results = '<b>col</b> '.$mapping->master_csv_num_col;
                else                    
                    $results = $mapping->value;
            break;
            case 'PARAMETER-EXT':
                $results = '<b>Param</b> '.$mapping->value;
            break;
            case 'INNER_TABLE_PARENT':
                if($mapping->has('queue_table'))
                   $results = $mapping->queue_table->table->name.' ('.$mapping->queue_table->table->id.')';
                else
                   $results = '<span class="label label-danger">dato inconsitente</span>'; 
            break;
            default:
                $results = ("mapping type [".$mapping->mapping_type->code."] non consentito");
            break;
        }

        return $results;
    }       

    public function drawDeliveryLabel($delivery) {
        return $this->getDeliveryLabel($delivery);
    }

    public function drawDeliveryDateLabel($delivery) {
        return $this->getDeliveryDateLabel($delivery);
    }

    public function drawOrderDateLabel($delivery) {
        return $this->getOrderDateLabel($delivery);
    }

    public function drawOrdersStateDiv($order) {

        $str = '';   	
        $str .= '<div class="action orderStato'.$order->state_code.'" title="'.__($order->state_code.'-intro').'"></div>';
             
        return $str;
    }

    public function drawDesOrdersStateDiv($des_order) {

        $str = '';   	
        $str .= '<div class="action orderStato'.$des_order->state_code.'" title="'.__($des_order->state_code.'-intro').'"></div>';
             
        return $str;
    }

    public function boxOrder($results, $options=[]) {

        if(empty($results))
            return '';

        $delivery_label = $this->getDeliveryLabel($results->delivery);

        $url = '';
        if(!empty($results->suppliers_organization->supplier->img1)) {
            $config = Configure::read('Config');
            $img_path = sprintf(Configure::read('Supplier.img.path.full'), $results->suppliers_organization->supplier->img1);
            $portalgas_app_root = $config['Portalgas.App.root'];
            $path = $portalgas_app_root.$img_path;
    
            if(file_exists($path)) {
                $portalgas_fe_url = $config['Portalgas.fe.url'];
                $url = sprintf($portalgas_fe_url.Configure::read('Supplier.img.path.full'), $results->suppliers_organization->supplier->img1);
            }   
        }

        $html = '
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">'.__('Order-'.$results->order_type_id).'</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div> <!-- /.box-header -->
            <div class="box-body no-padding-disabled">

                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="padding-top:0px">'.__('Delivery').'</label>
                        <div class="col-sm-4">'.$delivery_label.'</div>
                        
                        <label class="col-sm-2 control-label" style="padding-top:0px">'.__('Order').'</label>
                        <div class="col-sm-4">Dal '.$results->data_inizio.' al '.$results->data_fine.'</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="padding-top:0px">'.__('SupplierOrganization').'</label>
                        <div class="col-sm-4">
                            <a href="/site/produttore/'.$results->suppliers_organization->supplier->slug.'" target="_blank" title="vai al sito del produttore">'.$results->suppliers_organization->name.'</a></div>
                        
                        <label class="col-sm-2 control-label" style="padding-top:0px"></label>
                        <div class="col-sm-4">';
            if(!empty($url)) 
                $html .= '<img src="'.$url.'" alt="'.$results->suppliers_organization->name.'" title="'.$results->suppliers_organization->name.'" width="'.Configure::read('Supplier.img.preview.width').'" class="img-supplier" />';

                $html .= '<div class="box-owner">'.__('organization_owner_articles').': <span class="label label-info">'.__('ArticlesOwner'.$results->suppliers_organization->owner_articles).'</span></div>';
                        
                $html .= '</div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" style="padding-top:0px">'.__('StatoElaborazione').'</label>
                        <div class="col-sm-10">
                            <div style="padding-left:45px;min-height:48px;" class="action orderStato'.$results->order_state_code->code.'" title="'.$results->order_state_code->name.'">'.$results->order_state_code->descri.'</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>';

        return $html;
    }

    public function boxSupplierOrganization($suppliers_organization, $options=[]) {

        $config = Configure::read('Config');
        $img_path = sprintf(Configure::read('Supplier.img.path.full'), $suppliers_organization->supplier->img1);

        $portalgas_app_root = $config['Portalgas.App.root'];
        $path = $portalgas_app_root.$img_path;

        $html = '';
        $html .= '<div class="box-supplier-organization">';
        // $html .= $results->id;
        if(!empty($suppliers_organization->supplier->img1) && file_exists($path)) {
            $portalgas_fe_url = $config['Portalgas.fe.url'];
            $url = sprintf($portalgas_fe_url.Configure::read('Supplier.img.path.full'), $suppliers_organization->supplier->img1);

            $html .= '<span class="box-img"><img src="'.$url.'" alt="'.$suppliers_organization->supplier->name.'" title="'.$suppliers_organization->supplier->name.'" width="'.Configure::read('Supplier.img.preview.width').'" class="img-supplier" /></span> ';
        }
        $html .= '<span class="box-name">';
        $html .= '<a href="/site/produttore/'.$suppliers_organization->supplier->slug.'" target="_blank" title="vai al sito del produttore">';
        $html .= $suppliers_organization->name;
        $html .= '</a>'; 
        $html .= '</span>';
        $html .= "</div>";

        return $html;
    }

    public function boxArticleImg($results, $options=[]) {

        $config = Configure::read('Config');
        $img_path = sprintf(Configure::read('Article.img.path.full'), $results->article->organization_id, $results->article->img1);

        $portalgas_app_root = $config['Portalgas.App.root'];
        $path = $portalgas_app_root.$img_path;

        $html = '';
        $html .= '<div class="box-article-img">';
        // $html .= $results->id;
        if(!empty($results->article->img1) && file_exists($path)) {
            $portalgas_fe_url = $config['Portalgas.fe.url'];
            $url = sprintf($portalgas_fe_url.Configure::read('Article.img.path.full'), $results->article->organization_id, $results->article->img1);

            $html .= '<span class="box-img"><img src="'.$url.'" alt="'.$results->article->name.'" title="'.$results->article->name.'" width="'.Configure::read('Article.img.preview.width').'" class="img-article" /></span> ';
        }
        $html .= '<span class="box-name">'.$results->name.'</span>';
        $html .= "</div>";

        return $html;
    }

    public function boxArticleImgOnly($results, $options=[]) {

        $config = Configure::read('Config');
        $portalgas_fe_url = $config['Portalgas.fe.url'];
        $url = $portalgas_fe_url.Configure::read('Article.img.path.full');

        $html = '';
        $html .= '<div class="box-article-img">';
        // $html .= $results->id;
        if(!empty($results->article->img1)) {
            $img1_path = sprintf($url, $results->article->organization_id, $results->article->img1);
            $html .= '<span class="box-img"><img src="'.$img1_path.'" class="img-article" /></span> ';
        }
        $html .= "</div>";

        return $html;
    }

    /*
     * REFERENTI 
     */
    public function boxVerticalSupplierOrganizationreferents($results, $options=[]) {

        if(isset($options['pdf_img_path']))
            $img_path = $options['pdf_img_path'];
        else
            $img_path = '/img';

        $html = '';
        $html .= '<dl class="row">';
        foreach ($results as $referent) {

            $html .= '<dt class="col-sm-3">';
            if($referent['type']!='referente')
                $html .= '('.$referent['type'].') ';
            $html .= $referent['name'].' ';
            $html .= '</dt>';

            $html .= '<dd class="col-sm-9">';
            if(!empty($referent['email']))
                $html .= '&nbsp;'.$this->HtmlCustom->mail($referent['email']);   

            if(isset($referent['phone_satispay'])) {
                $html .= '&nbsp;'.$referent['phone_satispay'];
                $html .= '<img src="'.$img_path.'/satispay-ico.png" title="il referente ha Satispy" />';
            }
            else 
            if(isset($referent['phone'])) 
                $html .= '&nbsp;'.$referent['phone'];
           
            $html .= '</dd>';
        }
        $html .= '</dl>';

        return $html;
    }

    public function boxOrizontalSupplierOrganizationreferents($results, $options=[]) {

        if(isset($options['pdf_img_path']))
            $img_path = $options['pdf_img_path'];
        else
            $img_path = '/img';

        $html = '';
        $html .= '<ul class="list-inline">';
        foreach ($results as $numResult => $referent) {

            $html .= '<li class="list-inline-item">';
            if($referent['type']!='referente')
                $html .= '('.$referent['type'].') ';
            $html .= $referent['name'].' ';

            if(!empty($referent['email']))
                $html .= '&nbsp;'.$this->HtmlCustom->mail($referent['email']);   

            if(isset($referent['phone_satispay'])) {
                $html .= '<span>';
                $html .= '&nbsp;'.$referent['phone_satispay'];
                $html .= '<img src="'.$img_path.'/satispay-ico.png" title="il referente ha Satispy" />';
                $html .= '</span>';
            }
            else 
            if(isset($referent['phone'])) 
                $html .= '&nbsp;'.$referent['phone'];
           
            $html .= '</li>';

            if(isset($options['br'])) {
                if(($numResult%2)!=0)
                    $html .= '<br />';                
            }
        }
        $html .= '</ul>';

        return $html;
    }

    public function boxTitle($results, $breadcrumbs=[], $order=null) {

        if(!isset($results['title']))
            $results['title'] = ''; 

        $html = '';
        $html .= '<section class="content-header">';
        $html .= '<h1>';
        $html .= __($results['title']);
        if(isset($results['subtitle']) && !empty($results['subtitle'])) {
            $html .= '<small>';
            $html .= __($results['subtitle']);
            $html .= '</small>';
        }            
        $html .= '</h1>';

        (!empty($order)) ? $order_type_id = $order->order_type_id: $order_type_id = 0;

        if(!empty($breadcrumbs)) {
            $html .= '<ol class="breadcrumb">';
            foreach($breadcrumbs as $breadcrumb) {
                if(is_string($breadcrumb)) {
                    switch($breadcrumb) {
                        case 'home':
                            $html .= '<li><a href="'.$this->Url->build('/admin').'"><i class="fa fa-dashboard"></i> '.__('Home').'</a></li>';
                        break;
                        case 'list':
                            $label = __('List').' '.__('Orders-'.$order_type_id);
                            $html .= '<li><a href="'.$this->Url->build(['controller' => 'orders', 'action' => 'index', $order_type_id]).'"><i class="fa fa-list"></i> '.$label.'</a></li>';
                        break;
                    }
                }
            }
            
            if(!empty($order)) {
                $html .= '<li><a href="'.$this->Url->build(['controller' => 'orders', 'action' => 'home', $order_type_id, $order->id]).'"><i class="fa fa-home"></i> '.__('Order home').'</a></li>';
            }
        
            $html .= '</ol>';
        }

        $html .= "</section>";

        return $html;
    }

    public function orderPriceTypes($price_type_enums, $options=[]) {

        $html = '';
        $html .= '<div class="panel panel-primary">';
        $html .= '<div class="panel-heading"><h3 class="panel-title">'.__('priceTypes').'</h3></div>';
        
        $html .= '<div class="panel-body box_order_price_types" id="vue-order-price-types" style="display:none;">'; 

        $html .= '<div v-if="spinner_run_type_prices === true" class="run run-type-prices"><div class="spinner"></div></div>'; 
        $html .= '<div v-if="spinner_run_type_prices === false">'; 

        /*
         * fields new row
         */ 
        $html .= '<div class="row" id="frm">';
        $html .= '<div class="col-xs-2">';
        $html .= '<label for="name">'.__('Name').'</label>';
        $html .= '  <input v-model="row.name" placeholder="name" type="text" class="form-control" />';
        $html .= '  <span class="text-danger" v-if="validationErrors.name" v-text="validationErrors.name"></span>';
        $html .= '</div>';
        $html .= '<div class="col-xs-3">';
        $html .= '<label for="name">'.__('Descri').'</label>';
        $html .= '  <textarea v-model="row.descri" class="form-control"></textarea>';
        $html .= '</div>';

        $html .= '<div class="col-xs-2">';
        $html .= '<label for="name">'.__('Price Type').'</label>';
        $html .= $this->Form->radio('type', $price_type_enums, ['v-model' => 'row.type']);
        $html .= '  <span class="text-danger" v-if="validationErrors.type" v-text="validationErrors.type"></span>';
        $html .= '</div>';

        $html .= '<div class="col-xs-2">';
        $html .= '<label for="date">'.__('Value').'</label>';
        $html .= '<input v-model="row.value" placeholder="value" type="text" class="form-control" />';
        $html .= '  <span class="text-danger" v-if="validationErrors.value" v-text="validationErrors.value"></span>';
        $html .= '</div>';

        $html .= '<div class="col-xs-1">';
        $html .= '<label for="date">'.__('Sort').'</label>';
        $html .= '<input v-model="row.sort" type="number" class="form-control"/>';
        $html .= '</div>';

        $html .= '<div class="col-xs-2">';
        $html .= '<button button type="button" class="btn btn-primary" @click="addTableRow">'.__('Add row').'</button>';
        $html .= '</div>';
        $html .= '</div>'; // row

        /*
         * table
         */ 
        $html .= '<div class="table-responsive"><table class="table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>'.__('Name').'</th>';
        $html .= '<th>'.__('Descri').'</th>';
        $html .= '<th>'.__('Price Type').'</th>';
        $html .= '<th>'.__('Value').'</th>';
        $html .= '<th>'.__('Sort').'</th>';
        $html .= '<th></th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        /*
         * riga aggiunta con Vue
         * ogni volta che si aggiunge una riga devo fare il bind per valorizzare i campo hidden con i checkbox scelti
         */
        $html .= '<tr v-for="(row, index) in rows">
                  <td>{{row.name}}</td>
                  <td>{{row.descri}}</td>
                  <td>{{row.type | priceTypeLabel}}</td>
                  <td>{{row.value}}</td>
                  <td>{{row.sort}}</td>
                  <td>
                    <a href="" class="text-center btn btn-danger" v-on:click="removeTableRow(index)"><i class="fa fa-trash"></i></a>
                    <input name="priceTypes.name[]" type="hidden" v-bind:value="row.name" />
                    <input name="priceTypes.descri[]" type="hidden" v-bind:value="row.descri" />
                    <input name="priceTypes.type[]" type="hidden" v-bind:value="row.type" />
                    <input name="priceTypes.value[]" type="hidden" v-bind:value="row.value" />
                    <input name="priceTypes.sort[]" type="hidden" v-bind:value="row.sort" />
                  </td>
                </tr>'; 

        $html .= '</tbody>';
        $html .= '</table></div>';

        $html .= '</div>'; // results
        $html .= '</div>'; // box_order_price_types
        $html .= '</div>'; // panel panel-primary

        return $html;
    }

    public function drawSupplierImage($supplier, $options=[]) {

        $html = '';
        if(!empty($supplier->img1)) {

            if(isset($options['max-width']))
                $max_width = $options['max-width'];
            else 
                $max_width = '100px';

            $config = Configure::read('Config');
            $this->_portalgas_fe_url = $config['Portalgas.fe.url'];
            $this->_portalgas_app_root = $config['Portalgas.App.root'];
            $img_path_supplier = sprintf(Configure::read('Supplier.img.path.full'), $supplier->img1);
            $img_path_supplier = $this->_portalgas_app_root . $img_path_supplier;
    
            $url = '';
            if(file_exists($img_path_supplier)) {
                $url = sprintf($this->_portalgas_fe_url.Configure::read('Supplier.img.path.full'), $supplier->img1);
                $html .= '<img style="max-width:'.$max_width.'" src="'.$url.'" title="'.$supplier->name.'" alt="'.$supplier->name.'" />';
            }
        }
        return $html;
    }

	public function drawOrderStateNext($order) {
		
		$str = '';
		
		if(isset($order->orderStateNext) && !empty($order->orderStateNext)) {
			foreach($order->orderStateNext as $orderStateNext)
				$str .= '<a class="'.$orderStateNext['class'].'" title="'.$orderStateNext['label'].'" href="'.$this->jLink($orderStateNext['controller'], $orderStateNext['action'], $orderStateNext['qs']).'">'.$orderStateNext['label'].'</a>';
		}
		
		return $str;
	}

	public function drawOrderBtnPaid($order, $isRoot=false, $isTesoriereGeneric=false) {
		
		$str = '';

		/*
		 * saldato da gasisti 
		 * solo per Organization.orderUserPaid = 'Y'
		 */
		if(isset($order->PaidUsers['totalSummaryOrder'])) {
			if($order->PaidUsers['totalSummaryOrder']>0) {
				if($order->PaidUsers['totalSummaryOrderNotPaid']==0) {
					$label = __('Saldato da tutti i gasisti ').' ('.$order->PaidUsers['totalSummaryOrderPaid'].')';
					$str .= '<a class="label label-info" title="'.$label.'" href="'.$this->jLink('orderLifeCycles', 'summary_order', ['order_id' => $order->id]).'">'.$label.'</a>';
				}
				else {
					if($order->PaidUsers['totalSummaryOrderNotPaid']==$order->PaidUsers['totalSummaryOrder']) {
						$label = __('Devono saldare tutti i gasisti');
						$str .= '<a class="label label-danger" title="'.$label.'" href="'.$this->jLink('orderLifeCycles', 'summary_order', ['order_id' => $order->id]).'">'.$label.'</a>';                        
					}	
					else {
						if($order->PaidUsers['totalSummaryOrderNotPaid']==1) 
							$label = 'Deve saldare ancora '.$order->PaidUsers['totalSummaryOrderNotPaid'].' gasista';
						else 
							$label = 'Devono saldare ancora '.$order->PaidUsers['totalSummaryOrderNotPaid'].' gasisti';
						
						$str .= '<a class="label label-danger" title="'.$label.'" href="'.$this->jLink('orderLifeCycles', 'summary_order', ['order_id' => $order->id]).'">'.$label.'</a>';
					}
				}
			}
			else {
				$label = __('Non ci sono acquisti');
				$str .= '<span class="label label-danger" title="'.$label.'">'.$label.'</span>';
			}
		}	
					
		/*
		 * pagamento al produttore
		 * solo per Organization.orderSupplierPaid = 'Y'
		 */
		if(isset($order->PaidSupplier['isPaid'])) { 
		
			$str .= '<p></p>';
		
			if($order->PaidSupplier['isPaid']) {	
				$label = __('Pagato al produttore');
				if($isTesoriereGeneric) 
					$str .= '<a class="label label-info" title="'.$label.'" href="'.$this->jLink('tesoriere', 'pay_suppliers', ['delivery_id' => $order->delivery_id,'order_id' => $order->id]).'">'.$label.'</a>';
				else
					$str .= '<a class="label label-info" title="'.$label.'" href="'.$this->jLink('orderLifeCycles', 'pay_suppliers', ['order_id' => $order->id]).'">'.$label.'</a>';
			}
			else {
				$label = __('Non pagato al produttore');
				if($isTesoriereGeneric) 
                    $str .= '<a class="label label-danger" title="'.$label.'" href="'.$this->jLink('tesoriere', 'pay_suppliers', ['delivery_id' => $order->delivery_id,'order_id' => $order->id]).'">'.$label.'</a>';
                else
                    $str .= '<a class="label label-danger" title="'.$label.'" href="'.$this->jLink('orderLifeCycles', 'pay_suppliers', ['order_id' => $order->id]).'">'.$label.'</a>';
			}
		}
		
		return $str;
	}
		
	public function drawOrderMsgGgArchiveStatics($order) {
		
		$str = '';
		$label = $order->msgGgArchiveStatics['mgs'];

		if(isset($order->msgGgArchiveStatics['mailto']))
			$str .= '<a href="mailto:'.$order->msgGgArchiveStatics['mailto'].'">';
		
		$str .= '<span class="'.$order->msgGgArchiveStatics['class'].'" title="'.$label.'">'.$label.'</span>';

		if(isset($order->msgGgArchiveStatics['mailto']))
			$str .= '</a>';
		
		return $str;
	}

    /*
     * tipologie di legende Orders / DesOrders / ProdGasPromotions
     */    
	public function drawLegenda($user, $states, $debug=false) {	

		$htmlLegenda = '';

		if(empty($states))
			return $htmlLegenda;
		
        $isTemplatesOrdersState = false;
        foreach($states as $state) {
            if($state instanceof \App\Model\Entity\TemplatesOrdersState) 
                $isTemplatesOrdersState = true;
        }            
		$colsWidth = floor(100/count($states));
			
		$htmlLegenda = '<div class="legenda">';
		$htmlLegenda .= '<div class="table-responsive"><table class="table">';
	
		/*
		 * solo per gli ordini
 		 */
        if($isTemplatesOrdersState) {
			$htmlLegenda .= "\r\n";
			$htmlLegenda .= '<tr>';
			foreach($states as $state) {
				
				/*
				 * differenzio lo stato CLOSE tra Tesoriere e Cassiere passandogli il group_id
				 */
				$target = __($state->state_code.'-target');
				if($target==$state->state_code.'-target')  // e' == perche' non viene trovato
					$target = __($state->state_code.'-target-PAY'.$user->organization->template->payToDelivery);
					
				$htmlLegenda .= "\r\n";
				$htmlLegenda .= '<td width="'.$colsWidth.'%"><h4>';
				$htmlLegenda .= $target;
				$htmlLegenda .= '</h4></td>';
			}
			$htmlLegenda .= '</tr>';			
		}
        
		$htmlLegenda .= "\r\n";
		$htmlLegenda .= '<tr>';
		foreach($states as $state) {
			$htmlLegenda .= "\r\n";
			$htmlLegenda .= '<td id="icoOrder'.$state->state_code.'" class="tdLegendaOrdersStateIco">';
			$htmlLegenda .= '<div style="padding-left:45px;width:80%;cursor:pointer;height:auto;min-height:48px;" class="action orderStato'.$state->state_code.'" title="'.__($state->state_code.'-intro').'">'.__($state->state_code.'-label').'</div>&nbsp;';
			$htmlLegenda .= '</td>';
	
		}
		$htmlLegenda .= '</tr>';
	
		$htmlLegenda .= '<tr>';
		$htmlLegenda .= '<td id="tdLegendaOrdersStateTesto" colspan="'.count($states).'" style="border-bottom:none;background-color:#FFFFFF;height:50px;">';
	
		$htmlLegenda .= "\r\n";
		foreach($states as $state) {
			$htmlLegenda .= "\r\n";
			$htmlLegenda .= '<div class="alert alert-info testoLegendaTesoriereStato" role="alert" id="testoOrder'.$state->state_code.'" style="display:none;">';
			$htmlLegenda .= __($state->state_code.'-descri');
			$htmlLegenda .= '</div>';
		}
		$htmlLegenda .= '</td>';
		$htmlLegenda .= '</tr>';
	
		$htmlLegenda .= '</table></div>';
        $htmlLegenda .= '</div>';
	
        $jsLegenda = '';
		$jsLegenda .= 'function bindLegenda() {';
		$jsLegenda .= "\r\n";
		foreach($states as $state) {
			$jsLegenda .= "\r\n";
			$jsLegenda .= '$( ".orderStato'.$state->state_code.'" ).mouseenter(function () {';
			$jsLegenda .= "\r\n";
			$jsLegenda .= '	$(".tdLegendaOrdersStateIco").css("background-color","#ffffff").css("border-radius","0px");';
			$jsLegenda .= "\r\n";
			$jsLegenda .= '	$(".testoLegendaTesoriereStato").hide();';
			$jsLegenda .= "\r\n";
			$jsLegenda .= '	$("#icoOrder'.$state->state_code.'").css("background-color","yellow").css("border-radius","15px 15px 15px 15px");';
			$jsLegenda .= "\r\n";
			$jsLegenda .= '	$(".tdLegendaOrdersStateTesto").css("background-color","#F0F0F0");';
			$jsLegenda .= "\r\n";
			$jsLegenda .= '	$("#testoOrder'.$state->state_code.'").show();';
			$jsLegenda .= "\r\n";
			$jsLegenda .= '});';
	
			$jsLegenda .= "\r\n";
			$jsLegenda .= '$( ".orderStato'.$state->state_code.'" ).mouseleave(function () {';
			$jsLegenda .= "\r\n";
			$jsLegenda .= '	$(".tdLegendaOrdersStateIco").css("background-color","#ffffff");';
			$jsLegenda .= "\r\n";
			$jsLegenda .= '	$(".testoLegendaTesoriereStato").hide();';
			$jsLegenda .= "\r\n";
			$jsLegenda .= '});';
	
		}
		$jsLegenda .= "\r\n";
		$jsLegenda .= '}';
		
		$jsLegenda .= "\r\n";
		$jsLegenda .= '$(document).ready(function() {bindLegenda();});';
		$jsLegenda .= "\r\n";
	
		return ['htmlLegenda' => $htmlLegenda, 'jsLegenda' => $jsLegenda];
	}

    /*
     * link a joomla
     * /administrator/index.php?option=com_cake&controller=Orders&action=close&delivery_id=22&order_id=11
     */
    public function jLink($controller, $action, $qs=[]) {
        return $this->drawjLink($controller, $action, $qs); // in UtilTrait
    }
}