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
?>
  <li class="treeview">
    <a href="#">
      <i class="fa fa-files-o"></i> <span><?php echo __('Cms');?></span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    <ul class="treeview-menu">
        <?php
        echo '<li><a href="'.$this->Url->build('/admin/cms-menu-types').'">'.$icon.__('CMS menu types').' <label class="label label-success">new</label></a></li>';
        echo '<li><a href="'.$this->Url->build('/admin/cms-menus').'">'.$icon.__('CMS menus').' <label class="label label-success">new</label></a></li>';
        echo '<li><a href="'.$this->Url->build('/admin/cms-pages').'">'.$icon.__('CMS pages').' <label class="label label-success">new</label></a></li>';
        ?>
      </ul>
    </a>
  </li>

<?php
*/

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
            echo '<li><a href="'.$this->Url->build('/admin/orders/add-to-parent/'.Configure::read('Order.type.gas_groups')).'">'.$icon.__('Add Order').' <label class="label label-success">new</label></a></li>';
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
  if($user->acl['isGasGroupsManagerGroups'] ||
     $user->acl['isGasGroupsManagerDeliveries'] ||
     $user->acl['isManager']) {
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

if($user->acl['isReferentGeneric'] || $user->acl['isSuperReferente']) {
?>
  <li class="treeview">
    <a href="#">
      <i class="fa fa-cubes"></i> <span>Articoli</span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      <ul class="treeview-menu">
        <li><a href="<?php echo $this->Url->build('/admin/articles/index-quick'); ?>"><?php echo $icon;?><?php echo __('Gestione completa');?> <label class="label label-success">new</label></a></li>
        <li><a href="<?php echo $this->HtmlCustomSite->jLink('Articles', 'context_articles_add');?>" target=""><?php echo $icon;?>Aggiungi un nuovo articolo</a></li>
        <li><a href="<?php echo $this->HtmlCustomSite->jLink('Pages', 'export_docs_articles');?>" target=""><?php echo $icon;?>Stampa articoli</a></li>
        <li><a href="<?php echo $this->HtmlCustomSite->jLink('Articles', 'gest_categories');?>" target=""><?php echo $icon;?>Gestisci categorie</a></li>
        <li><a href="<?php echo $this->HtmlCustomSite->jLink('Articles', 'index_edit_prices_default');?>" target=""><?php echo $icon;?>Modifica prezzi</a></li>
        <li><a href="<?php echo $this->HtmlCustomSite->jLink('Articles', 'index_edit_prices_percentuale');?>" target=""><?php echo $icon;?>Modifica prezzi in %</a></li>
        <li><a href="<?php echo $this->HtmlCustomSite->jLink('ArticlesOrders', 'order_choice');?>" target=""><?php echo $icon;?>Modifica prezzo degli articolo associati agli ordini</a></li>
        <!--
        <li><a href="<?php echo $this->HtmlCustomSite->jLink('CsvImports', 'articles');?>" target=""><?php echo $icon;?>Importa articoli</a></li>
        <li><a href="<?php echo $this->HtmlCustomSite->jLink('CsvImports', 'articles_form_export');?>" target=""><?php echo $icon;?>Esporta articoli per reimportarli</a></li>
        <li><a href="<?php echo $this->HtmlCustomSite->jLink('CsvImports', 'articles_form_import');?>" target=""><?php echo $icon;?>Importa articoli da esportazione precedente</a></li>
        -->
      </ul>
    </a>
  </li>
  <li class="treeview">
    <a href="#">
      <i class="fa fa-file-excel-o"></i> <span>Gestione Listini da Excel</span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      <ul class="treeview-menu">
      <li><a href="<?php echo $this->Url->build('/admin/helps/articles-export-import'); ?>"><?php echo $icon;?>Istruzioni per esporta ed importa</a></li>
      <li><a href="<?php echo $this->Url->build('/admin/articles/export'); ?>"><?php echo $icon;?><?php echo __('Export to EXCEL');?> <label class="label label-success">new</label></a></li>
        <li><a href="<?php echo $this->Url->build('/admin/articles/import'); ?>"><?php echo $icon;?><?php echo __('Import from EXCEL');?> <label class="label label-success">new</label></a></li>
      </ul>
    </a>
  </li>
<?php
} // end if($user->acl['isSuperReferente'] && isset($user->organization->paramsConfig['hasArticlesGdxp']) && $user->organization->paramsConfig['hasArticlesGdxp']=='Y')

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

if($user->organization->paramsConfig['hasGasGroups']=='N' &&
  ($user->acl['isManager'] || $user->acl['isSuperReferente'] || $user->acl['isReferentGeneric'])) {
  ?>
   <li class="treeview">
     <a href="#">
       <i class="fa fa-cloud"></i> <span><?php echo __('Export');?></span>
       <span class="pull-right-container">
         <i class="fa fa-angle-left pull-right"></i>
       </span>
     <ul class="treeview-menu">
       <li><a href="<?php echo $this->Url->build('/admin/exports/deliveries'); ?>"><?php echo $icon;?><?php echo __('Deliveries');?> <label class="label label-success">new</label></a></li>
     </ul>
     </a>
   </li>
 <?php
 } // end if($user->acl['isManager'])

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
      <li><a href="<?php echo $this->Url->build('/admin/cashiers/deliveries'); ?>"><?php echo $icon;?>Pagamenti dell'intera consegna <label class="label label-success">new</label></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/cashiers/massive'); ?>"><?php echo $icon;?>Movimenti di cassa massivi <label class="label label-success">new</label></a></li>
      <li><a href="<?php echo $this->Url->build('/admin/movements/index'); ?>"><?php echo $icon;?>Movimenti di cassa <label class="label label-success">new</label></a></li>
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

if($user->acl['isTesoriere']) {
    ?>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-credit-card"></i> <span><?php echo __('UserGroupsTesoriere');?></span>
            <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
            <ul class="treeview-menu">
                <li><a href="<?php echo $this->HtmlCustomSite->jLink('Tesoriere', 'home');?>" target=""><?php echo __('Tesoriere home');?></a></li>
                <li><a href="<?php echo $this->HtmlCustomSite->jLink('Tesoriere', 'orders_get_WAIT_PROCESSED_TESORIERE');?>" target=""><?php echo __('OrdersWaitProcessedTesoriere');?></a></li>
                <li><a href="<?php echo $this->HtmlCustomSite->jLink('Tesoriere', 'orders_get_PROCESSED_TESORIERE');?>" target=""><?php echo __('OrdersProcessedTesoriereShort');?></a></li>
                <li><a href="<?php echo $this->HtmlCustomSite->jLink('Tesoriere', 'orders_get_TO_REQUEST_PAYMENT');?>" target=""><?php echo __('OrdersToRequestPaymentShort');?></a></li>
                <li><a href="<?php echo $this->HtmlCustomSite->jLink('RequestPayments', 'index');?>" target=""><?php echo __('OrdersToPaymentShort');?></a></li>
            </ul>
        </a>
    </li>
<?php
} // if($user->acl['isTesoriere'])

if($user->organization->paramsConfig['hasCms']=='Y') { ?>
<li class="treeview">
    <a href="#">
        <i class="fa fa-files-o"></i> <span><?php echo __('Cms');?></span>
        <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
        <ul class="treeview-menu">
            <?php
            if($user->acl['isRoot'])
                echo '<li><a href="'.$this->Url->build('/admin/cms-menu-types').'">'.$icon.__('Cms MenuType').' <span class="pull-right-container"><small class="label pull-right bg-red">root</small></span></a></li>';
            echo '<li><a href="'.$this->Url->build('/admin/cms-menus').'">'.$icon.__('Cms Menus').'</a></li>';
            echo '<li><a href="'.$this->Url->build('/admin/cms-pages').'">'.$icon.__('Cms Pages').'</a></li>';
            echo '<li><a href="'.$this->Url->build('/admin/cms-images').'">'.$icon.__('Cms Images').'</a></li>';
            if($user->organization->paramsConfig['hasDocuments']=='Y')
                echo '<li><a href="'.$this->Url->build('/admin/cms-docs').'">'.$icon.__('Cms Docs').'</a></li>';
            ?>
        </ul>
    </a>
</li>
<?php
}?>
