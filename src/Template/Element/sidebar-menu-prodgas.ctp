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

  if($this->Identity->get()->acl['isRoot'] || 
    ($this->Identity->get()->acl['isProdGasSupplierManager'] && isset($this->Identity->get()->organization->paramsConfig['hasArticlesGdxp']) && $this->Identity->get()->organization->paramsConfig['hasArticlesGdxp']=='Y')) {
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
  }