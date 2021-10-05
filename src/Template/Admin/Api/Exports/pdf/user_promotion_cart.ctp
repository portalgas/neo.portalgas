<?php
/*
 * user passato da Controller perche' IdentityHelper could not be found.
 * $user = $this->Identity->get();
 */
// debug($results);
// debug($user);

$html = '';
if(!empty($results)) {

/*
	   <div v-for="(promotion, index) in results.promotions"
	            :promotion="promotion"
	            :key="promotion.promotion.id"
	    >
			
     		<div class="row">
            <div class="col-sm-12 col-xs-12 col-md-12"> 

              <div class="card mb-3">

                <div class="row no-gutters">
                  <div class="col-md-2"> 
                      <div class="content-img-supplier">
                        <img v-if="promotion.promotion.organization.suppliers_organization.supplier.img1 != ''"
                          class="img-supplier" :src="appConfig.$siteUrl+'/images/organizations/contents/'+promotion.promotion.organization.suppliers_organization.supplier.img1"
                          :alt="promotion.promotion.organization.suppliers_organization.supplier.name">
                      </div>

                  </div>
                  <div class="col-md-10">
                     <div class="card-body type-PROMOTION">
                        <h5 class="card-title">
                            <a v-if="promotion.promotion.organization.suppliers_organization.supplier.www!=''" target="_blank" v-bind:href="promotion.promotion.organization.suppliers_organization.supplier.www" title="vai al sito del produttore">
                              {{ promotion.promotion.organization.suppliers_organization.name }}
                            </a>
                            <span v-if="promotion.promotion.organization.suppliers_organization.supplier.www==''">
                              {{ promotion.promotion.organization.suppliers_organization.name }}
                            </span>
                            <small class="card-text">
                              {{ promotion.promotion.organization.suppliers_organization.supplier.descrizione }}
                            </small>                        
                        </h5>

                        <p v-if="promotion.promotion.id!=null" class="card-text">
                            <b>{{ promotion.promotion.name }}</b>
                            terminerà {{ promotion.promotion.data_fine | formatDate }}
                        </p>

                     </div> <!-- card-body -->
                     <div class="card-footer text-muted bg-transparent-disabled">
                        <strong>Note per la consegna e il pagamento</strong> 
                        <div v-html="$options.filters.html(promotion.promotion.nota)"></div>
                     </div>  <!-- card-footer --> 

                  </div> <!-- col-md-10 -->
                </div> <!-- row -->
        
              </div> <!-- card -->

            </div> <!-- col... -->
          </div> <!-- row -->

	        <user-cart-articles 
      			:order="promotion.order" 
      			:article_orders="promotion.article_orders"
      			></user-cart-articles>

  
    </div> <!-- loop -->
    */

	$totale_promozione = 0;
	foreach($results as $result) {

			$promotion = $result['promotion'];
			$order = $result['order'];
			$article_orders = $result['article_orders'];
			$suppliers_organization = $result['promotion']['organization']['suppliers_organization'];

			$html .= '<h2>Promozione '.$suppliers_organization['supplier']['name'];
			if(!empty($suppliers_organization['supplier']['descrizione']))
				$html .= '<small>'.$suppliers_organization['supplier']['descrizione'].'</small>';
			$html .= '</h2>';

			$html .= '<h3>Note per la consegna e il pagamento</h3>';
			$html .= '<p>';
			$html .= $promotion['nota'];
			$html .= '</p>';

			$html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
			$html .= '<thead>'; // con questo TAG mi ripete l'intestazione della tabella
			$html .= '	<tr>';
			$html .= '			<th width="5%">' . __('Bio') . '</th>';
			$html .= '			<th width="30%" class="text-left">' . __('Name') . '</th>';
			$html .= '			<th width="10%">' . __('Conf') . '</th>';
			$html .= '			<th width="20%">' . __('Prezzo/UM') . '</th>';
			$html .= '			<th width="20%">&nbsp;' . __('PrezzoUnita') . '</th>';
			$html .= '			<th width="5%">' . __('Qta') . '</th>';
			$html .= '			<th width="10%">' . __('Importo') . '</th>';
			$html .= '	</tr>';
			$html .= '	</thead><tbody>';

			$totale_ordine = 0;
			foreach($article_orders as $article_order) {
				
				// debug($article_order);

				$article_order['is_bio'] ? $is_bio = '<img src="'.$img_path.'/is-bio.png" title="bio" width="20" />': $is_bio = '';
				
				$html .= '<tr>';
				$html .= '	<td class="text-center">'.$is_bio.'</td>';
				$html .= '	<td>'.$article_order['name'].'</td>';
				$html .= '	<td class="text-center">'.$article_order['conf'].'</td>';
				$html .= '	<td class="text-center">'.$article_order['um_rif_label'].'</td>';
				$html .= '	<td class="text-center">'.$this->HtmlCustom->importo($article_order['price']).'</td>';
				$html .= '	<td class="text-center">';
				$html .= $article_order['cart']['final_qta'];
				$html .= '  </td>';
				$html .= '	<td class="text-center">';
				$html .= $this->HtmlCustom->importo($article_order['cart']['final_price']);
				$html .= '  </td>';
				$html .= '</tr>';

			  $totale_promozione += $article_order['cart']['final_price']; 
		}

		$html .= '	</tbody>';
		$html .= '	</table>';	

	} // loop orders

	/*
	 * totale consegna
	 */
	$label = __('Totale').' '.$this->HtmlCustom->importo($totale_promozione);
	$html .= '<div class="box-totali">';
	$html .= $label;
	$html .= '</div>';

} // end if(!empty($results))

echo $html;
?>