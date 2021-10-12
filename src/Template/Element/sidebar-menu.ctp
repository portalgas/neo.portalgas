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
  if($this->Identity->get()->acl['isRoot']) {
  ?> 
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-cog"></i> <span><?php echo __('Administrator');?></span>
      <span class="pull-right-container">
        <i class="fa fa-mail pull-right"></i>
      </span>
    <ul class="treeview-menu">
      <li><a href="<?php echo $this->Url->build('/admin/tests/ajax'); ?>"><?php echo $icon;?><?php echo __('Tests ajax');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>   
      <li><a href="<?php echo $this->Url->build('/admin/tests/ajax_cart'); ?>"><?php echo $icon;?><?php echo __('Tests ajax cart');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>   
      <li><a href="<?php echo $this->Url->build('/admin/tests/searchable'); ?>"><?php echo $icon;?><?php echo __('Tests searchable');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>      
      <li><a href="<?php echo $this->Url->build('/admin/tests/salt'); ?>"><?php echo $icon;?><?php echo __('Tests salt');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>   
      <li><a href="<?php echo $this->Url->build('/admin/logs/index'); ?>"><?php echo $icon;?><?php echo __('Logs Lists');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/mail-sends/index'); ?>"><?php echo $icon;?><?php echo __('Logs cron mail');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/order-types/index'); ?>"><?php echo $icon;?><?php echo __('OrderTypes Lists');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/order-state-codes/index'); ?>"><?php echo $icon;?><?php echo __('OrderStateCodes Lists');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/geo-regions/index'); ?>"><?php echo $icon;?><?php echo __('Regions Lists');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/geo-provincies/index'); ?>"><?php echo $icon;?><?php echo __('Provincies Lists');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
    </ul>
    </a>
  </li>  
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-envelope"></i> <span><?php echo __('Mail');?></span>
      <span class="pull-right-container">
        <i class="fa fa-mail pull-right"></i>
      </span>
    <ul class="treeview-menu">
      <li><a href="<?php echo $this->Url->build('/admin/mails/suppliers'); ?>"><?php echo $icon;?><?php echo __('Suppliers');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
    </ul>
    </a>
  </li>   
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-book"></i> <span><?php echo __('Documents');?></span>
      <span class="pull-right-container">
        <i class="fa fa-mail pull-right"></i>
      </span>
    <ul class="treeview-menu">
      <li><a href="<?php echo $this->Url->build('/admin/documents/organization-index'); ?>"><?php echo $icon;?><?php echo __('List Documents');?></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/documents'); ?>"><?php echo $icon;?><?php echo __('List Documents');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/document-types'); ?>"><?php echo $icon;?><?php echo __('List Document Types');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/document-states'); ?>"><?php echo $icon;?><?php echo __('List Document States');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/document-reference-models'); ?>"><?php echo $icon;?><?php echo __('List Document Reference Models');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/document-owner-models'); ?>"><?php echo $icon;?><?php echo __('List Document Owner Models');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
    </ul>
    </a>
  </li>  
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-handshake-o"></i> <span><?php echo __('Pact');?></span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    <ul class="treeview-menu">
      <li><a href="<?php echo $this->Url->build('/admin/price-types/index'); ?>"><?php echo $icon;?><?php echo __('Price types');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
    </ul>
    </a>
  </li>
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-apple"></i> <span><?php echo __('SocialMarket');?></span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    <ul class="treeview-menu">
      <li><a href="<?php echo $this->Url->build('/admin/markets/index'); ?>"><?php echo $icon;?><?php echo __('Markets');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/market-articles/index'); ?>"><?php echo $icon;?><?php echo __('Articles Market');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
    </ul>
    </a>
  </li>

  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-credit-card"></i> <span><?php echo __('OrganizationPay');?></span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    <ul class="treeview-menu">
      <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=OrganizationsPays&amp;action=index" target="">Prospetto <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/organizations-pays/generate'); ?>"><?php echo $icon;?><?php echo __('Genera pagamenti').' '.date('Y');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/organizations-pays/index'); ?>"><?php echo $icon;?><?php echo __('Gestisci pagamenti').' '.date('Y');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=OrganizationsPays&amp;action=invoice_create_form" target="">Genera fattura <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $portalgas_bo_url;?>/administrator/index.php?option=com_cake&amp;controller=Pages&amp;action=export_docs_root" target="">Stampa documenti <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
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

        <li><a href="<?php echo $this->Url->build('/admin/scopes'); ?>"><?php echo $icon;?>Scope <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
        <li><a href="<?php echo $this->Url->build('/admin/queue-mapping-types'); ?>"><?php echo $icon;?>Type queues <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
        <li><a href="<?php echo $this->Url->build('/admin/queues'); ?>"><?php echo $icon;?>Lists queues <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
        <li><a href="<?php echo $this->Url->build('/admin/queue-logs'); ?>"><?php echo $icon;?>Logs queues</a></li>

        <li class="treeview">
          <a href="#"><?php echo $icon;?>Mappings
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            
            <li><a href="<?php echo $this->Url->build('/admin/tables'); ?>"><?php echo $icon;?>Lists tables <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>            
            <li><a href="<?php echo $this->Url->build('/admin/queue-tables'); ?>"><?php echo $icon;?>Lists queue tables <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
            <li><a href="<?php echo $this->Url->build('/admin/mapping-value-types'); ?>"><?php echo $icon;?>Lists mapping value types <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
            <li><a href="<?php echo $this->Url->build('/admin/mapping-types'); ?>"><?php echo $icon;?>Lists mapping types</a></li>
            <li><a href="<?php echo $this->Url->build('/admin/mappings'); ?>"><?php echo $icon;?>Lists mappings <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
          </ul>
        </li>
    </ul>
  </li>
  <?php
  } // if($this->Identity->get()->acl['isRoot'])

  // if($this->Identity->get()->acl['isSuperReferente']) || $this->Identity->get()->acl['isReferentGeneric'])) {
  if($this->Identity->get()->acl['isRoot'] || 
    ($this->Identity->get()->acl['isSuperReferente'] && isset($this->Identity->get()->organization->paramsConfig['hasArticlesGdxp']) && $this->Identity->get()->organization->paramsConfig['hasArticlesGdxp']=='Y')) {
  ?>
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-share-alt"></i> <span>Interoperabilità</span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
      <ul class="treeview-menu">
        <li><a href="<?php echo $this->Url->build('/admin/gdxps/suppliersIndex'); ?>"><?php echo $icon;?><?php echo __('Gdxp-Suppliers-index-short');?></a></li>
        <li><a href="<?php echo $this->Url->build('/admin/gdxps/articlesIndex'); ?>"><?php echo $icon;?><?php echo __('Article-Export-short');?></a></li>
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
          echo '<li><a href="'.$this->Url->build('/admin/cashes/supplier-organization-filter').'">'.$icon.__('Prepagato per produttori').'</a></li>';
      }   
      ?>
    </ul>
    </a>
  </li>
  <?php
  }

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
        <li><a href="<?php echo $this->Url->build('/admin/cashiers/deliveries'); ?>"><?php echo $icon;?><?php echo __("Pagamenti dell'intera consegna");?></a></li>
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
  }
  ?>  