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
            <li><a href="<?php echo $this->Url->build('/admin/articles/export'); ?>"><?php echo $icon;?><?php echo __('Export to EXCEL');?> <label class="label label-success">new</label></a></li>
            <li><a href="<?php echo $this->Url->build('/admin/articles/import'); ?>"><?php echo $icon;?><?php echo __('Import from EXCEL');?> <label class="label label-warning">new</label></a></li>
            <!-- 
            <li><a href="<?php echo $this->HtmlCustomSite->jLink('CsvImports', 'articles');?>" target=""><?php echo $icon;?>Importa articoli</a></li>
            <li><a href="<?php echo $this->HtmlCustomSite->jLink('CsvImports', 'articles_form_export');?>" target=""><?php echo $icon;?>Esporta articoli per reimportarli</a></li>
            <li><a href="<?php echo $this->HtmlCustomSite->jLink('CsvImports', 'articles_form_import');?>" target=""><?php echo $icon;?>Importa articoli da esportazione precedente</a></li>
            -->
          </ul>
        </a>
      </li>
    <?php
    } // end if($user->acl['isSuperReferente'] && isset($user->organization->paramsConfig['hasArticlesGdxp']) && $user->organization->paramsConfig['hasArticlesGdxp']=='Y') 
    
  if($this->Identity->get()->acl['isRoot'] || 
     $this->Identity->get()->acl['isProdGasSupplierManager']) {
  ?>
      <li class="treeview">
          <a href="#">
              <i class="fa fa-cart-plus"></i> <span>SocialMarket</span>
              <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
              <ul class="treeview-menu">
                  <li><a href="<?php echo $this->Url->build('/admin/socialmarket-carts/carts'); ?>"><?php echo $icon;?><?php echo __('Elenco acquisti');?></a></li>
              </ul>
          </a>
      </li>
  <?php 
    }
    /*
    if($this->Identity->get()->acl['isRoot'] || 
    (isset($this->Identity->get()->organization->paramsConfig['hasArticlesGdxp']) && $this->Identity->get()->organization->paramsConfig['hasArticlesGdxp']=='Y')) {
  ?>
      <li class="treeview">
          <a href="#">
              <i class="fa fa-share-alt"></i> <span>Interoperabilità</span>
              <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
              <ul class="treeview-menu">
                  <li><a href="<?php echo $this->Url->build('/admin/gdxps/articlesSendIndex'); ?>"><?php echo $icon;?><?php echo __('Article-Send-short');?></a></li>
              </ul>
          </a>
      </li>
  <?php
  }*/