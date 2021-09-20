<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;
use Cake\Mailer\Email;
use Cake\Routing\Router;

/*
 * layout /src/Template/Layout/Email/html/default.ctp
 */
class MailComponent extends Component {

	private $_email;  // se false non invia mail (config_{env}.php)
	private $_mail_send;  // se false non invia mail (config_{env}.php)
	private $_fullbaseUrl = null;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request

        $this->_fullbaseUrl = Router::fullbaseUrl();
    }

	/*
	 * crea oggetto mail per invio mail di sistema
	 * options = name (nome user per header)
	 */
	 private function _setMailSystem($user, $options) {
		
        $config = Configure::read('Config');
        $this->_mail_send = $config['mail.send'];

		$this->_email = new Email(Configure::read('EmailConfig')); // aws
		$this->_email->setFrom([Configure::read('SOC.mail') => Configure::read('SOC.name')])
			  ->setReplyTo([Configure::read('Mail.no_reply_mail') => Configure::read('Mail.no_reply_name')])
			  ->setSender([Configure::read('SOC.mail') => Configure::read('SOC.name')]) // real sender
			  ;

        /*
         * /src/Template/Email/html/default.ctp
         * /src/Template/Layout/Email/html/default.ctp
         */
        $this->_email->setTemplate('default', 'default')
        	  ->setEmailFormat('html')
        	  ->setViewVars(['body_header' => $this->_getHeader($user, $options)])
        	  ->setViewVars(['body_footer' => $this->_getFooter($user, $options)]);
	}
	
	/*
	 * $mails = [$UserProfile.email, User.email] perche' restituisco il risultato solo della  
	 */
	public function send($user, $mails, $subject, $body, $options=array(), $debug=false) {

		$this->_setMailSystem($user, $options);

		$this->_email->setSubject($subject);

		$results = [];
		$_mails = [];
		
		if(!is_array($mails))
			$_mails[] = $mails;
		else 
			$_mails = $mails;
		
		foreach($_mails as $mail) {
			
			$mail = trim($mail);
			
			// profile.email = "mail"
			if(substr($mail, 0, 1)=='"') // primo carattere
				$mail = substr($mail, 1, strlen($mail));
			if(substr($mail, -1, 1)=='"') // ultimo carattere
				$mail = substr($mail, 0, strlen($mail)-1);
			$mail = trim($mail);
				
			if(!empty($mail)) { 
			
				if($debug) debug("Mail::send - tratto la mail ".$mail);
									
				/*
				non + perche' mail2 = UserProfile.email
				$results['KO'] = 'Mail vuota!';
				return $results;
				*/
			
				$exclude = false;
				foreach(Configure::read('EmailExcludeDomains') as $this->_emailExcludeDomain) {
					if($debug) debug('Mail::send - EmailExcludeDomains '.$mail.' - '.$this->_emailExcludeDomain);
					if(strpos($mail, $this->_emailExcludeDomain)!==false) {
						$exclude = true;
						break;
					}
				}
				
				if($exclude)  {	
					if($debug) debug("EXCLUDE mail TO: ".$mail);
					$results['OK'] = $mail.' (modalita DEBUG)';
				}
				else {
					/*
					 * todo
					 * $this->_email->viewVars(array('content_info' => $this->_getContentInfo()));
					 */
										
					if($debug) {
						if (!$this->_mail_send)
							if($debug) debug("Mail::send - inviata a " . $mail . " (modalita DEBUG)");
						else
							if($debug) debug("Mail::send - inviata a " . $mail);
											
						if($debug) debug("Mail::send - mail TO: ".$mail." $body ".$body);
					}

					try {
						$this->_email->setTo($mail);
						$this->_email->send($body);
						
						if (!$this->_mail_send)
							$results['OK'] = $mail.' (modalita DEBUG)';
						else
							$results['OK'] = $mail;
					} catch (Exception $e) {
						$results['KO'] = $mail;
						CakeLog::write("error", $e, array("mails"));
					}
				}
			} // end if(empty($mail)) 
			
			if($debug) debug($result);

		} // loops mails
		
		return $results;
	}
	
	private function _getHeader($user, $options) {
		$results = [];
		if(isset($options['name'])) {
			$results['greeting'] = sprintf(Configure::read('Mail.body_header'), $options['name']);
		}
		$results['logo'] = $this->_drawLogo($user->organization);
			
		return $results;
	}						
	
	private function _getFooter($user, $options) {
		$results = [];
		$results['text'] = sprintf(Configure::read('Mail.body_footer'));
		
		return $results;
	}						

    private function _drawLogo($organization=null) {
    
        $logo_url = $this->_fullbaseUrl.'/img/loghi/150h50.png';
        $str = '<a href="https://'.Configure::read('SOC.site').'" target="_blank"><img border="0" src="'.$logo_url.'" /></a>';
        return $str;

        if(isset($organization))
            $logo_url = 'https://'.Configure::read('SOC.site').Configure::read('App.img.loghi').'/'.$organization['Organization']['id'].'/'.Configure::read('Mail.logo');
        else
            $logo_url = 'https://'.Configure::read('SOC.site').Configure::read('App.img.loghi').'/0/'.Configure::read('Mail.logo');
    
        $str = '<a href="https://'.Configure::read('SOC.site').'" target="_blank"><img border="0" src="'.$logo_url.'" /></a>';
        return $str;
    }  
	
	public function _getContentInfo() {

		App::import('Model', 'Msg');
		$Msg = new Msg;	

		$results = $Msg->getRandomMsg();
		if(!empty($results)) 
			$str = $results['Msg']['testo'];
		else
			$str = '';
		/*
		echo "<pre>";
		print_r($results);
		echo "</pre>";
		*/
		return $str;
	}
	
	public $belongsTo = [
			'User' => [
					'className' => 'User',
					'foreignKey' => 'user_id',
					'conditions' => 'User.organization_id = Mail.organization_id',
					'fields' => '',
					'order' => ''
			]
	];
}