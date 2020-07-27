<?php
use Cake\Core\Configure;
if(Configure::read('BootstrapUIEnabled'))  $this->extend('/Layout/TwitterBootstrap/signin');

$this->layout = 'login';
?>
<style>
/*
.main-center {
    margin-top: 30px;
    margin: 0 auto;
    max-width: 450px;
    padding: 40px 40px;
}
.main-login {
    background-color: #fff;
    -moz-border-radius: 2px;
    -webkit-border-radius: 2px;
    border-radius: 2px;
    -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
}*/
</style>

<div class="container-disabled">

	<div class="panel-heading">
	   <div class="panel-title text-center">
			<h1 class="title"></h1>
			<hr>
		</div>
	</div>
	
	<div class="main-login main-center">

	  <?php echo $this->Flash->render(); ?>
	  <?php echo $this->Flash->render('auth');?>
	  
	  <?php	
		echo $this->Form->create('', ['id' => 'frmLogin']);
	  ?> 
		<!-- h2 class="form-signin-heading">Richiesta d'identificazione</h2 -->
		
		<div class="form-group">
			<label for="name" class="col-2 control-label"><?php echo __d('CakeDC/Users', 'Username');?></label>
			<div class="col-10">
				<div class="input-group">
					<span class="input-group-addon"><i class="glyphicon glyphicon-user" aria-hidden="true"></i></span>
					<input class="form-control" type="text" class="form-control" name="username" id="username" placeholder="Username" required>
				</div>
			</div>
		</div>
		
		<div class="form-group">
			<label for="password" class="col-2 control-label"><?php echo __d('CakeDC/Users', 'Password');?></label>
			<div class="col-10">
				<div class="input-group">
					<span class="input-group-addon"><i class="glyphicon glyphicon-lock" aria-hidden="true"></i></span>
					<input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
				</div>
			</div>
		</div>
							
								
		<?php
				if (Configure::read('Users.reCaptcha.login')) {
					echo $this->User->addReCaptcha();
				}
				if (Configure::read('Users.RememberMe.active')) {
					echo '<div class="checkbox">';
					echo $this->Form->control(Configure::read('Users.Key.Data.rememberMe'), [
						'type' => 'checkbox',
						'label' => __d('CakeDC/Users', 'Remember me'),
						'checked' => Configure::read('Users.RememberMe.checked')
					]);
					echo '</div>';
				}
		
                if (Configure::check('Users.Registration.active') && Configure::read('Users.Registration.active')) {
                    echo $this->Html->link(__d('users', 'Register'), ['action' => 'register']);
                }
                if (Configure::check('Users.Email.active') && Configure::read('Users.Email.active')) {
                    echo ' | ';
                    echo $this->Html->link(__d('users', 'Reset Password'), ['plugin' => 'CakeDC/Users', 'controller' => 'users', 'action' => 'requestResetPassword']);
                }    
                ?>
		<button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
	  </form>

	</div>
</div>