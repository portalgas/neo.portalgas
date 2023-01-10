<?php
use Cake\Core\Configure;

$icon = ''; // '<i class="fa fa-circle"></i> ';

$config = Configure::read('Config');
$portalgas_bo_url = $config['Portalgas.bo.url'];
$portalgas_bo_home = $config['Portalgas.bo.home'];
$joomla25Salts_isActive = $config['Joomla25Salts.isActive'];

echo $this->fetch('tb_sidebar');

if($joomla25Salts_isActive) {
  echo '<li class="">';
  echo '<a href="'.$this->Url->build('/admin/joomla25Salts').'?scope=BO">';
  echo '  <i class="fa fa-home"></i> <span>'.__('PortAlGas').'</span>';
  echo '</a>';
  echo '</li>';
}
else {
  echo '<li class="">';
  echo '<a href="'.$portalgas_bo_url.$portalgas_bo_home.'">';
  echo '  <i class="fa fa-home"></i> <span>'.__('PortAlGas').'</span>';
  echo '</a>';
  echo '</li>';
}

/* 
 * ordini 
 */
if($this->Identity->get()->acl['isReferentGeneric'] || $this->Identity->get()->acl['isSuperReferente']) {
  ?>
    <li class="treeview"> 
      <a href="#">
        <i class="fa fa-folder-open"></i> <span>Ordini</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
        <ul class="treeview-menu">
          <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Orders&amp;action=index" target="">Elenco ordini</a></li>
          <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Orders&amp;action=add" target="">Aggiungi un nuovo ordine</a></li>
          <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Orders&amp;action=easy_add" target="">Aggiungi un nuovo ordine (modalità semplificata)</a></li>
          <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Orders&amp;action=index_history" target="">Ordini storici</a></li>
          <li><a href="<?php echo $this->Url->build('/admin/loops-orders/index'); ?>"><?php echo $icon;?><?php echo __('Loops Orders');?> <label class="label label-success">new</label></a></li>
          <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=MonitoringOrders&amp;action=home" target="">Monitoraggio Ordini</a></li>        </ul>
      </a>
    </li>
  <?php
  } // end if($this->Identity->get()->acl['isSuperReferente'] && isset($this->Identity->get()->organization->paramsConfig['hasArticlesGdxp']) && $this->Identity->get()->organization->paramsConfig['hasArticlesGdxp']=='Y') 

  
// if($this->Identity->get()->acl['isSuperReferente']) || $this->Identity->get()->acl['isReferentGeneric'])) {
if($this->Identity->get()->acl['isSuperReferente'] && isset($this->Identity->get()->organization->paramsConfig['hasArticlesGdxp']) && $this->Identity->get()->organization->paramsConfig['hasArticlesGdxp']=='Y') {
?>
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-share-alt"></i> <span>Interoperabilità</span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
      <ul class="treeview-menu">
        <li><a href="<?php echo $this->Url->build('/admin/gdxps/suppliersIndex'); ?>"><?php echo $icon;?><?php echo __('Gdxp-Suppliers-index-short');?><label class="label label-success">new</label></a></li>
        <li><a href="<?php echo $this->Url->build('/admin/gdxps/articlesIndex'); ?>"><?php echo $icon;?><?php echo __('Article-Export-short');?> <label class="label label-success">new</label></a></li>
        <li class="treeview">
          <a href="#"><?php echo $icon;?><?php echo __('Import-File-short');?>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo $this->Url->build('/admin/import-files/json'); ?>"><?php echo $icon;?>File Json <label class="label label-success">new</label></a></li>
            <?php
            /*
            <li><a href="<?php echo $this->Url->build('/admin/import-files/xml'); ?>"><?php echo $icon;?>File XML</a></li>
            <li><a href="<?php echo $this->Url->build('/admin/import-files/csv'); ?>"><?php echo $icon;?>File CSV</a></li>
            */
            ?>
          </ul>
        </li>
      </ul>
    </a>
  </li>
<?php
} // end if($this->Identity->get()->acl['isSuperReferente'] && isset($this->Identity->get()->organization->paramsConfig['hasArticlesGdxp']) && $this->Identity->get()->organization->paramsConfig['hasArticlesGdxp']=='Y') 

 if($this->Identity->get()->acl['isManager']) {
 ?>
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-money"></i> <span><?php echo __('Prepaid');?></span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    <ul class="treeview-menu">
      <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=OrganizationsCashs&amp;action=index" target=""><?php echo $icon;?>Configura</a></li>
      <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=OrganizationsCashs&amp;action=ctrl" target=""><?php echo $icon;?>Prospetto utenti</a></li>
      <?php
      if($this->Identity->get('organization')->paramsConfig['hasCashFilterSupplier']=='Y') {
          echo '<li><a href="'.$this->Url->build('/admin/cashes/supplier-organization-filter').'">'.$icon.__('Prepagato per produttori').' <label class="label label-success">new</label></a></li>';
      }   
      ?>
    </ul>
    </a>
  </li>
<?php
} // end if($this->Identity->get()->acl['isManager'])

if($this->Identity->get()->acl['isCassiere']) {
?>
<li class="treeview"> 
  <a href="#">
    <i class="fa fa-bank"></i> <span><?php echo __('Cashier');?></span>
    <span class="pull-right-container">
      <i class="fa fa-angle-left pull-right"></i>
    </span>
  <ul class="treeview-menu">
      <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Cashs&amp;action=index" target=""><?php echo $icon;?>Gestione cassa</a></li>
      <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Cashs&amp;action=index_quick" target=""><?php echo $icon;?>Gestione cassa rapida</a></li>
      <li><a href="<?php echo $this->Url->build('/admin/cashiers/deliveries'); ?>"><?php echo $icon;?><?php echo __("Pagamenti dell'intera consegna");?> <label class="label label-success">new</label></a></li>
      <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Docs&amp;action=cassiere_delivery_docs_export" target=""><?php echo $icon;?>Stampa/Gestisci l'intera consegna</a></li>
      <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Docs&amp;action=cassiere_docs_export" target=""><?php echo $icon;?>Stampa i singoli ordini</a></li>
      <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Cassiere&amp;action=orders_to_wait_processed_tesoriere" target=""><?php echo $icon;?>Passa gli ordini al tesoriere</a></li>
      <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=OrganizationsCashs&amp;action=ctrl" target=""><?php echo $icon;?>Prepagato - prospetto utenti</a></li>
      <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Pages&amp;action=export_docs_cassiere" target=""><?php echo $icon;?>Stampe cassiere</a></li>
      <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Pages&amp;action=utility_docs_cassiere" target=""><?php echo $icon;?>Utility da scaricare</a></li>      
  </ul>
  </a>
</li>
<?php 
} // if($this->Identity->get()->acl['isCassiere']) 

if($this->Identity->get()->organization->paramsConfig['hasGasGroups']=='Y') {
?>
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-users"></i> <span><?php echo __('Gas Groups');?></span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    <ul class="treeview-menu">
        <?php 
        if($this->Identity->get()->acl['isGasGroupsManagerGroups']) 
          echo '<li><a href="'.$this->Url->build('/admin/gas-groups').'">'.$icon.__('Gas Group Users').' <label class="label label-success">new</label></a></li>';
        if($this->Identity->get()->acl['isGasGroupsManagerDeliveries']) 
          echo '<li><a href="'.$this->Url->build('/admin/gas-group-deliveries').'">'.$icon.__('Gas Group Deliveries').' <label class="label label-success">new</label></a></li>';
        if($this->Identity->get()->acl['isGasGroupsManagerParentOrders']) 
          echo '<li><a href="'.$this->Url->build('/admin/orders/index/'.Configure::read('Order.type.gas_parent_groups')).'">'.$icon.__('Gas Group Parent Orders').' <label class="label label-success">new</label></a></li>';
        if($this->Identity->get()->acl['isGasGroupsManagerOrders']) 
          echo '<li><a href="'.$this->Url->build('/admin/orders/index/'.Configure::read('Order.type.gas_groups')).'">'.$icon.__('Gas Group Orders').' <label class="label label-success">new</label></a></li>';
        ?>
      </ul>
    </a>
  </li>
<?php 
}    