<?php
use Cake\Core\Configure;

$icon = '<i class="fa fa-circle"></i> ';
?>	
  <?php echo $this->fetch('tb_sidebar') ?>

  <?php
  if($isRoot) {
  ?>  
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
      <i class="fa fa-dashboard"></i> <span>Interoperabilità tra gestionali</span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
      <ul class="treeview-menu">
        <li><a href="<?php echo $this->Url->build('/admin/gdxps/'); ?>"><?php echo $icon;?><?php echo __('Article-Export-short');?></a></li>

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
      <li><a href="<?php echo $this->Url->build('/admin/cashs/supplier-organization-filter'); ?>"><?php echo $icon;?><?php echo __('Cash-Filter-Suppliers');?></a></li>
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
      <li><a href="<?php echo $this->Url->build('/admin/cashiers/deliveries'); ?>"><?php echo $icon;?><?php echo __('Gestisci consegna');?></a></li>
    </ul>
    </a>
  </li>
  <?php
  }
  ?>  