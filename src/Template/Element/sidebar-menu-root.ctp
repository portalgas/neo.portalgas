<?php
use Cake\Core\Configure;

$icon = ''; // '<i class="fa fa-circle"></i> ';

$config = Configure::read('Config');
$joomla25Salts_isActive = $config['Joomla25Salts.isActive'];
?> 
  <li class="treeview"> 
    <a href="#">
      <i class="fa fa-cog"></i> <span><?php echo __('Administrator');?></span>
      <span class="pull-right-container">
        <i class="fa fa-mail pull-right"></i>
      </span>
    <ul class="treeview-menu">
      <li><a href="<?php echo $this->Url->build('/admin/articles-import-officinanatura/index'); ?>"><?php echo $icon;?><?php echo __('Articles Import OfficinaNatura');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/organizations/settingParams'); ?>"><?php echo $icon;?><?php echo __('Organization Setting Params');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/tests/sitemap'); ?>"><?php echo $icon;?><?php echo __('Tests sitemap');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
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
      <li><a href="<?php echo $this->HtmlCustomSite->jLink('organizationsPays', 'mail');?>" target="">Mail manager / tesorieri <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->HtmlCustomSite->jLink('organizationsPays', 'index');?>" target="">Prospetto <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/organizations-pays/generate'); ?>"><?php echo $icon;?><?php echo __('Genera pagamenti').' '.date('Y');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/organizations-pays/index'); ?>"><?php echo $icon;?><?php echo __('Gestisci pagamenti').' '.date('Y');?> <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->HtmlCustomSite->jLink('organizationsPays', 'invoice_create_form');?>" target="">Genera fattura <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->HtmlCustomSite->jLink('organizationsPays', 'invoice_create_pdfs');?>" target="">Genera tutte le fatture <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
      <li><a href="<?php echo $this->HtmlCustomSite->jLink('pages', 'export_docs_root');?>" target="">Stampa documenti <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>
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