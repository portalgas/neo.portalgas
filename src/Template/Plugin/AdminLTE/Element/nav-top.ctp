<?php 
use Cake\Core\Configure; 

$config = Configure::read('Config');
$portalgas_bo_url = $config['Portalgas.bo.url'];
$portalgas_bo_home = $config['Portalgas.bo.home'];   
$joomla25Salts_isActive = $config['Joomla25Salts.isActive'];  
?>
<nav class="navbar navbar-static-top">

  <?php if (isset($layout) && $layout == 'top'): ?>
  <div class="container">

    <div class="navbar-header">
      <a href="<?php echo $this->Url->build('/admin'); ?>" class="navbar-brand"><?php echo Configure::read('Theme.logo.large') ?></a>
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
        <i class="fa fa-bars"></i>
      </button>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
        <li><a href="#">Link</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li class="divider"></li>
            <li><a href="#">One more separated link</a></li>
          </ul>
        </li>
      </ul>
      <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
          <input type="text" class="form-control" id="navbar-search-input" placeholder="Search">
        </div>
      </form>
    </div>
    <!-- /.navbar-collapse -->
  <?php else: ?>

    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </a>

  <?php endif; ?>


<?php
if ($this->Identity->isLoggedIn()) {
?>
  <div class="navbar-custom-menu">
    <ul class="nav navbar-nav">


    <?php
      if($application_env=='development') {
    ?>      
      <!-- Messages: style can be found in dropdown.less-->
      <li class="dropdown messages-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <i class="fa fa-envelope-o"></i>
          <span class="label label-success">5</span>
        </a>
        <ul class="dropdown-menu">
          <li class="header">Hai 4 messaggi</li>
          <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
              <li><!-- start message -->
                <a href="#">
                  <div class="pull-left">
                    <?php echo $this->Html->image('avatar5.png', array('class' => 'img-circle', 'alt' => 'User Image')); ?>
                  </div>
                  <h4>
                    Support Team
                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                  </h4>
                  <p>Why not buy a new awesome theme?</p>
                </a>
              </li>
              <!-- end message -->
              <li>
                <a href="#">
                  <div class="pull-left">
                    <?php echo $this->Html->image('user3-128x128.jpg', array('class' => 'img-circle', 'alt' => 'User Image')); ?>
                  </div>
                  <h4>
                    AdminLTE Design Team
                    <small><i class="fa fa-clock-o"></i> 2 hours</small>
                  </h4>
                  <p>Why not buy a new awesome theme?</p>
                </a>
              </li>
              <li>
                <a href="#">
                  <div class="pull-left">
                    <?php echo $this->Html->image('user4-128x128.jpg', array('class' => 'img-circle', 'alt' => 'User Image')); ?>
                  </div>
                  <h4>
                    Developers
                    <small><i class="fa fa-clock-o"></i> Today</small>
                  </h4>
                  <p>Why not buy a new awesome theme?</p>
                </a>
              </li>
              <li>
                <a href="#">
                  <div class="pull-left">
                    <?php echo $this->Html->image('user3-128x128.jpg', array('class' => 'img-circle', 'alt' => 'User Image')); ?>
                  </div>
                  <h4>
                    Sales Department
                    <small><i class="fa fa-clock-o"></i> Yesterday</small>
                  </h4>
                  <p>Why not buy a new awesome theme?</p>
                </a>
              </li>
              <li>
                <a href="#">
                  <div class="pull-left">
                    <?php echo $this->Html->image('user4-128x128.jpg', array('class' => 'img-circle', 'alt' => 'User Image')); ?>
                  </div>
                  <h4>
                    Reviewers
                    <small><i class="fa fa-clock-o"></i> 2 days</small>
                  </h4>
                  <p>Why not buy a new awesome theme?</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="footer"><a href="#">See All Messages</a></li>
        </ul>
      </li>




      <!-- Notifications: style can be found in dropdown.less -->
      <li class="dropdown notifications-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <i class="fa fa-bell-o"></i>
          <span class="label label-warning">10</span>
        </a>
        <ul class="dropdown-menu">
          <li class="header">You have 10 notifications</li>
          <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
              <li>
                <a href="#">
                  <i class="fa fa-users text-aqua"></i> 5 new members joined today
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the
                  page and may cause design problems
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="fa fa-users text-red"></i> 5 new members joined
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                </a>
              </li>
              <li>
                <a href="#">
                  <i class="fa fa-user text-red"></i> You changed your username
                </a>
              </li>
            </ul>
          </li>
          <li class="footer"><a href="#">View all</a></li>
        </ul>
      </li>




      <!-- Tasks: style can be found in dropdown.less -->
      <li class="dropdown tasks-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <i class="fa fa-flag-o"></i>
          <span class="label label-danger">9</span>
        </a>
        <ul class="dropdown-menu">
          <li class="header">You have 9 tasks</li>
          <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
              <li><!-- Task item -->
                <a href="#">
                  <h3>
                    Design some buttons
                    <small class="pull-right">20%</small>
                  </h3>
                  <div class="progress xs">
                    <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar"
                         aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                      <span class="sr-only">20% Complete</span>
                    </div>
                  </div>
                </a>
              </li>
              <!-- end task item -->
              <li><!-- Task item -->
                <a href="#">
                  <h3>
                    Create a nice theme
                    <small class="pull-right">40%</small>
                  </h3>
                  <div class="progress xs">
                    <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar"
                         aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                      <span class="sr-only">40% Complete</span>
                    </div>
                  </div>
                </a>
              </li>
              <!-- end task item -->
              <li><!-- Task item -->
                <a href="#">
                  <h3>
                    Some task I need to do
                    <small class="pull-right">60%</small>
                  </h3>
                  <div class="progress xs">
                    <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar"
                         aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                      <span class="sr-only">60% Complete</span>
                    </div>
                  </div>
                </a>
              </li>
              <!-- end task item -->
              <li><!-- Task item -->
                <a href="#">
                  <h3>
                    Make beautiful transitions
                    <small class="pull-right">80%</small>
                  </h3>
                  <div class="progress xs">
                    <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar"
                         aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                      <span class="sr-only">80% Complete</span>
                    </div>
                  </div>
                </a>
              </li>
              <!-- end task item -->
            </ul>
          </li>
          <li class="footer">
            <a href="#">View all tasks</a>
          </li>
        </ul>
      </li>
    <?php
    } // end if($application_env=='development')
    ?>

      <!-- User Account: style can be found in dropdown.less -->
	  


      <?php
      if(isset($isImpersonated) && $isImpersonated)
        echo '<li class="user-header">'.$this->Html->link('<button type="button" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-off"></span> '.__('impersonateLogout').' '.$this->Identity->get('username').'</button>', ['controller' => 'auths', 'action' => 'impersonateLogout'],['escape' => false]).'</li> ';
      ?>
	  





      <li class="dropdown user user-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <?php echo $this->Html->image('avatar5.png', array('class' => 'user-image', 'alt' => 'User Image')); ?>
          <span class="hidden-xs"><?php echo $this->Identity->get('username');?></span>
        </a>
        <ul class="dropdown-menu">
          <!-- User image -->
          <li class="user-header">
            <?php echo $this->Html->image('avatar5.png', array('class' => 'img-circle', 'alt' => 'User Image')); ?>

            <p>
             <?php echo $this->Identity->get('username');?>
            </p>
          </li>
          <!-- Menu Body 
          <li class="user-body">
            <div class="row">
              <div class="col-xs-4 text-center">
                <a href="#">Collaboratori</a>
              </div>
              <div class="col-xs-4 text-center">
                <a href="#">Offerte</a>
              </div>
              <div class="col-xs-4 text-center">
                <a href="#">Commesse</a>
              </div>
            </div>
          </li>
          -->
          <!-- Menu Footer-->

          <?php
          echo '<li class="user-footer">';
          echo '<div class="pull-left">';
          // echo '<a href="/admin/users/view/'.$this->Identity->get('id').'" class="btn btn-default btn-flat">Profile</a>';
          echo '</div>';
          echo '<div class="pull-right">';
          // logout valida per il FE 
          // echo '<a href="/users/logout" class="btn btn-default btn-flat">Logout</a>';
          if($joomla25Salts_isActive) 
              $url = $this->Url->build('/admin/joomla25Salts');
          else
              $url = $portalgas_bo_url.$portalgas_bo_home;

          $url = $portalgas_bo_url.$portalgas_bo_home;
          $url = '/users/logout_bo';

          echo '<a href="'.$url.'" class="btn btn-danger btn-flat">Logout</a>';
          echo '</div>';
          echo '</li>';
        echo '</ul>';
      echo '</li>';

      if($application_env=='development') {
          if (!isset($layout)) {
            echo '<li>';
            echo '<a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>';
            echo '</li>';
          }
      }
    
    echo '</ul>';
  echo '</div>';

} // if ($this->Identity->isLoggedIn()) {
?>

  <?php if (isset($layout) && $layout == 'top'): ?>
  </div>
  <?php endif; ?>
</nav>