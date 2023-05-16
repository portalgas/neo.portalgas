<?php
use Cake\Core\Configure;

$user = $this->Identity->get();

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
if($user->acl['isReferentGeneric'] || $user->acl['isSuperReferente']) {
?>
  <li class="treeview" id="box-menu-order"></li>

  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-folder-open"></i> <span>Ordini</span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
      <ul class="treeview-menu">
        <?php 
        /* 
          * nuova versione ordini ora solo gas_parent_groups / gas_groups
          */
        if($user->organization->paramsConfig['hasGasGroups']=='Y') {
          if($user->acl['isGasGroupsManagerParentOrders']) 
            echo '<li><a href="'.$this->Url->build('/admin/orders/index/'.Configure::read('Order.type.gas_parent_groups')).'">'.$icon.__('Gas Group Parent Orders').' <label class="label label-success">new</label></a></li>';
          if($user->acl['isGasGroupsManagerOrders']) {
            echo '<li><a href="'.$this->Url->build('/admin/orders/index/'.Configure::read('Order.type.gas_groups')).'">'.$icon.__('Gas Group Orders').' <label class="label label-success">new</label></a></li>';
            echo '<li><a href="'.$this->Url->build('/admin/orders/add/'.Configure::read('Order.type.gas_groups')).'">'.$icon.__('Add Order').' <label class="label label-success">new</label></a></li>';
          }
        }
        else {
        /* 
          * versione predecente ordini
          */
        ?>
          <li><a href="<?php echo $this->HtmlCustomSite->jLink('orders', 'index');?>" target="">Elenco ordini</a></li>
          <?php 
          if($application_env==='development')
            echo '<li><a href="'.$this->Url->build('/admin/orders/add/'.Configure::read('Order.type.gas')).'">'.$icon.'Aggiungi un nuovo ordine <small class="label pull-right bg-red">root</small></a></li>';
          ?>
          <li><a href="<?php echo $this->HtmlCustomSite->jLink('orders', 'add');?>" target="">Aggiungi un nuovo ordine</a></li>
          <li><a href="<?php echo $this->HtmlCustomSite->jLink('orders', 'easy_add');?>" target="">Aggiungi un nuovo ordine (modalità semplificata)</a></li>
          <li><a href="<?php echo $this->HtmlCustomSite->jLink('orders', 'index_history');?>" target="">Ordini storici</a></li>
          <li><a href="<?php echo $this->Url->build('/admin/loops-orders/index'); ?>"><?php echo $icon;?><?php echo __('Loops Orders');?> <label class="label label-success">new</label></a></li>
          <li><a href="<?php echo $this->HtmlCustomSite->jLink('monitoringOrders', 'home');?>" target="">Monitoraggio Ordini</a></li>        
        <?php
        }  
        ?>
      </ul>
    </a>
  </li>
<?php 
} // end if($user->acl['isSuperReferente'] && isset($user->organization->paramsConfig['hasArticlesGdxp']) && $user->organization->paramsConfig['hasArticlesGdxp']=='Y') 

if($user->organization->paramsConfig['hasGasGroups']=='Y') {
  if($user->acl['isGasGroupsManagerGroups'] || $user->acl['isGasGroupsManagerDeliveries']) {
?>
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-users"></i> <span><?php echo __('Gas Groups');?></span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    <ul class="treeview-menu">
        <?php 
        if($user->acl['isGasGroupsManagerGroups']) 
          echo '<li><a href="'.$this->Url->build('/admin/gas-groups').'">'.$icon.__('Gas Group Users').' <label class="label label-success">new</label></a></li>';
        if($user->acl['isGasGroupsManagerDeliveries']) 
          echo '<li><a href="'.$this->Url->build('/admin/gas-group-deliveries').'">'.$icon.__('Gas Group Deliveries').' <label class="label label-success">new</label></a></li>';
        ?>
      </ul>
    </a>
  </li>
<?php 
  } // end if($user->acl['isGasGroupsManagerGroups'] || $user->acl['isGasGroupsManagerDeliveries']) {
} // end if($user->organization->paramsConfig['hasGasGroups']=='Y') 

// if($user->acl['isSuperReferente']) || $user->acl['isReferentGeneric'])) {
if($user->acl['isSuperReferente'] && isset($user->organization->paramsConfig['hasArticlesGdxp']) && $user->organization->paramsConfig['hasArticlesGdxp']=='Y') {
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
} // end if($user->acl['isSuperReferente'] && isset($user->organization->paramsConfig['hasArticlesGdxp']) && $user->organization->paramsConfig['hasArticlesGdxp']=='Y') 

 if($user->acl['isManager']) {
 ?>
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-money"></i> <span><?php echo __('Prepaid');?></span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    <ul class="treeview-menu">
      <li><a href="<?php echo $this->HtmlCustomSite->jLink('organizationsCashs', 'index');?>" target=""><?php echo $icon;?>Configura</a></li>
      <li><a href="<?php echo $this->HtmlCustomSite->jLink('organizationsCashs', 'ctrl');?>" target=""><?php echo $icon;?>Prospetto utenti</a></li>
      <?php
      if($this->Identity->get('organization')->paramsConfig['hasCashFilterSupplier']=='Y') {
          echo '<li><a href="'.$this->Url->build('/admin/cashes/supplier-organization-filter').'">'.$icon.__('Prepagato per produttori').' <label class="label label-success">new</label></a></li>';
      }   
      ?>
    </ul>
    </a>
  </li>
<?php
} // end if($user->acl['isManager'])

if($user->acl['isCassiere']) {
?>
<li class="treeview"> 
  <a href="#">
    <i class="fa fa-bank"></i> <span><?php echo __('Cashier');?></span>
    <span class="pull-right-container">
      <i class="fa fa-angle-left pull-right"></i>
    </span>
  <ul class="treeview-menu">
      <li><a href="<?php echo $this->HtmlCustomSite->jLink('cashs', 'index');?>" target=""><?php echo $icon;?>Gestione cassa</a></li>
      <li><a href="<?php echo $this->HtmlCustomSite->jlink('cashs', 'index_quick');?>" target=""><?php echo $icon;?>Gestione cassa rapida</a></li>
      <li><a href="<?php echo $this->Url->build('/admin/cashiers/deliveries'); ?>"><?php echo $icon;?><?php echo __("Pagamenti dell'intera consegna");?> <label class="label label-success">new</label></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/movements/index'); ?>"><?php echo $icon;?><?php echo __("Movimenti di cassa");?> <label class="label label-warning">beta</label></a></li>
      <li><a href="<?php echo $this->HtmlCustomSite->jlink('docs', 'cassiere_delivery_docs_export');?>" target=""><?php echo $icon;?>Stampa/Gestisci l'intera consegna</a></li>
      <li><a href="<?php echo $this->HtmlCustomSite->jlink('docs', 'cassiere_docs_export');?>" target=""><?php echo $icon;?>Stampa i singoli ordini</a></li>
      <li><a href="<?php echo $this->HtmlCustomSite->jlink('cassiere', 'orders_to_wait_processed_tesoriere');?>" target=""><?php echo $icon;?>Passa gli ordini al tesoriere</a></li>
      <li><a href="<?php echo $this->HtmlCustomSite->jlink('organizationsCashs', 'ctrl');?>" target=""><?php echo $icon;?>Prepagato - prospetto utenti</a></li>
      <li><a href="<?php echo $this->HtmlCustomSite->jlink('pages', 'export_docs_cassiere');?>" target=""><?php echo $icon;?>Stampe cassiere</a></li>
      <li><a href="<?php echo $this->HtmlCustomSite->jlink('pages', 'utility_docs_cassiere');?>" target=""><?php echo $icon;?>Utility da scaricare</a></li>      
  </ul>
  </a>
</li>
<?php 
} // if($user->acl['isCassiere']) 