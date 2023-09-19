<?php 
use Cake\Core\Configure;
use App\Traits;
?>

<h3 class="box-title">Elenco ordini dei gruppi associati all'ordine titolare</h3>

<?php 
if(count($order->child_orders)==0) {
    echo $this->element('msg', ['msg' => "Non ci sono ancora ordini dei gruppi associati", 'class' => 'warning']);
}
else {
?>
<div>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col"><?php echo __('Gas Group');?></th>
                <th scope="col" colspan="2"><?php echo __('Delivery');?></th>
                <th scope="col"><?php echo __('StatoElaborazione');?></th>
                <th scope="col">Si chiuderà</th>
                <th scope="col"><?php echo __('List Supplier Organization Referents');?></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach($order->child_orders as $child_order) {
                
                // dd($child_order);
                $delivery_label = $this->HtmlCustomSite->drawDeliveryLabel($child_order->delivery);
                $delivery_data = $this->HtmlCustomSite->drawDeliveryDateLabel($child_order->delivery);
                

                echo '<tr>';
                echo '<td>'.$child_order->gas_group->name.'</td>';
                echo '<td>'.$delivery_label.'</td>';
                echo '<td>'.$delivery_data.'</td>';
                echo '<td>';
                echo $this->HtmlCustomSite->drawOrdersStateDiv($child_order);
                echo '&nbsp;';
                echo __($child_order->state_code.'-label');                
                echo '</td>';
                echo '<td>';
                echo $child_order->data_fine->i18nFormat('eeee d MMMM');
                if($child_order->data_fine_validation!=Configure::read('DB.field.date.empty2'))	
                    echo '<br />Riaperto fino a '.$child_order->data_fine_validation->i18nFormat('eeee d MMMM');
                echo '</td>';
                echo '<td>';
             //   echo $this->HtmlCustomSite->boxVerticalSupplierOrganizationreferents($child_order->suppliers_organization->suppliers_organizations_referents);
                echo '<ul>';
                foreach($child_order->suppliers_organization->suppliers_organizations_referents as $suppliers_organizations_referent) {
                    
                    $user = $suppliers_organizations_referent->user;

                    echo '<li>';
                    echo $user->name;
                    if(!empty($user->email))
                        echo '&nbsp;'.$this->HtmlCustom->mailIco($user->email);                    
                    echo '</li>';
                }
                echo '</ul>';
                echo '</td>';
                echo '</tr>';
            }
            ?>            
        </tbody>
    </table>
</div>
<?php 
}     
