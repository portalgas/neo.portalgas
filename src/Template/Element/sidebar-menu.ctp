<?php
use Cake\Core\Configure;

$icon = ''; // '<i class="fa fa-circle"></i> ';

$config = Configure::read('Config');
$portalgas_bo_url = $config['Portalgas.bo.url'];
$portalgas_bo_home = $config['Portalgas.bo.home'];
?>	
  <?php echo $this->fetch('tb_sidebar') ?>

  <li class=""> 
    <a href="<?php echo $portalgas_bo_url.$portalgas_bo_home;?>">
      <i class="fa fa-home"></i> <span><?php echo __('PortAlGas');?></span>
    </a>
  </li>
  <?php
  if($isRoot) {
  ?>  
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-credit-card"></i> <span><?php echo __('OrganizationPay');?></span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    <ul class="treeview-menu">
      <li><a href="<?php echo $this->Url->build('/admin/organizations-pays/index'); ?>"><?php echo $icon;?><?php echo __('List');?></a></li>
    </ul>
    </a>
  </li>  
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-dashboard"></i> <span>Queue</span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    </a>
    <ul class="treeview-menu">

        <li><a href="<?php echo $this->Url->build('/admin/scopes'); ?>"><?php echo $icon;?>Scope</a></li>
        <li><a href="<?php echo $this->Url->build('/admin/queue-mapping-types'); ?>"><?php echo $icon;?>Type queues</a></li>
        <li><a href="<?php echo $this->Url->build('/admin/queues'); ?>"><?php echo $icon;?>Lists queues</a></li>
        <li><a href="<?php echo $this->Url->build('/admin/queue-logs'); ?>"><?php echo $icon;?>Logs queues</a></li>

        <li class="treeview">
          <a href="#"><?php echo $icon;?>Mappings
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            
            <li><a href="<?php echo $this->Url->build('/admin/tables'); ?>"><?php echo $icon;?>Lists tables</a></li>            
            <li><a href="<?php echo $this->Url->build('/admin/queue-tables'); ?>"><?php echo $icon;?>Lists queue tables</a></li>
            <li><a href="<?php echo $this->Url->build('/admin/mapping-value-types'); ?>"><?php echo $icon;?>Lists mapping value types</a></li>
            <li><a href="<?php echo $this->Url->build('/admin/mapping-types'); ?>"><?php echo $icon;?>Lists mapping types</a></li>
            <li><a href="<?php echo $this->Url->build('/admin/mappings'); ?>"><?php echo $icon;?>Lists mappings</a></li>
          </ul>
        </li>
    </ul>
  </li>
  <?php
  } // if($isRoot)

  // if($isSuperReferente || $isReferentGeneric) {
  if($isRoot) {
  ?>
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-share-alt"></i> <span>Interoperabilità tra gestionali</span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
      <ul class="treeview-menu">
        <li><a href="<?php echo $this->Url->build('/admin/gdxps/'); ?>"><?php echo $icon;?><?php echo __('Gdxp-Suppliers-index-short');?></a></li>
        <li><a href="<?php echo $this->Url->build('/admin/gdxps/export'); ?>"><?php echo $icon;?><?php echo __('Article-Export-short');?></a></li>
        <li class="treeview">
          <a href="#"><?php echo $icon;?><?php echo __('Import-File-short');?>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo $this->Url->build('/admin/import-files/json'); ?>"><?php echo $icon;?>File Json</a></li>
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
  }

  if($isManager) {
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
          echo '<li><a href="'.$this->Url->build('/admin/cashes/supplier-organization-filter').'">'.$icon.__('Prepagato per produttori').'</a></li>';
      }   
      ?>
    </ul>
    </a>
  </li>
  <?php
  }

  if($isCassiere) {
  ?>
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-bank"></i> <span><?php echo __('Cashier');?></span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    <ul class="treeview-menu">
        <li><a href="<a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Cashs&amp;action=index" target=""><?php echo $icon;?>Gestione cassa</a></li>
        <li><a href="<a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Cashs&amp;action=index_quick" target=""><?php echo $icon;?>Gestione cassa rapida</a></li>
        <li><a href="<?php echo $this->Url->build('/admin/cashiers/deliveries'); ?>"><?php echo $icon;?><?php echo __("Pagamenti dell'intera consegna");?></a></li>
        <li><a href="<a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Docs&amp;action=cassiere_delivery_docs_export" target=""><?php echo $icon;?>Stampa/Gestisci l'intera consegna</a></li>
        <li><a href="<a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Docs&amp;action=cassiere_docs_export" target=""><?php echo $icon;?>Stampa i singoli ordini</a></li>
        <li><a href="<a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Cassiere&amp;action=orders_to_wait_processed_tesoriere" target=""><?php echo $icon;?>Passa gli ordini al tesoriere</a></li>
        <li><a href="<a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=OrganizationsCashs&amp;action=ctrl" target=""><?php echo $icon;?>Prepagato - prospetto utenti</a></li>
        <li><a href="<a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Pages&amp;action=export_docs_cassiere" target=""><?php echo $icon;?>Stampe cassiere</a></li>
        <li><a href="<a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Pages&amp;action=utility_docs_cassiere" target=""><?php echo $icon;?>Utility da scaricare</a></li>      
    </ul>
    </a>
  </li>
  <?php
  }
  ?>  