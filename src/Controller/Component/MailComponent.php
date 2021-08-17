<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Controller\ComponentRegistry;

class MailComponent extends Component {

	private $_mail_send;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        $this->_registry = $registry;
        $controller = $registry->getController();
        //$controller->request
    }

	/*
	 * crea oggetto mail per invio mail di sistema
	 */
	 public function getMailSystem($user) {
		
        $config = Configure::read('Config');
        $this->_mail_send = $config['mail.send'];

		$email = new Email(Configure::read('EmailConfig')); // aws
		$email->setFrom([Configure::read('SOC.mail') => Configure::read('SOC.name')])
			  ->setReplyTo([Configure::read('Mail.no_reply_mail'), Configure::read('Mail.no_reply_name')])
			  ->setSender([Configure::read('SOC.mail') => Configure::read('SOC.name')]) // real sender
			  ;

        /*
         * Template\Email\html\default.ctp
         * Template\Layout\Email\html\portalgas.ctp
         */
        $email->setTemplate('default', 'default')
        	  ->setEmailFormat('html')
        	  ->setViewVars(['header' => $this->_drawLogo($user->organization)])
        	  ->setViewVars(['body_footer' => sprintf(Configure::read('Mail.body_footer'))]);

		return $Email;
	}
	
	/*
	 * $mails = [$UserProfile.email, User.email] perche' restituisco il risultato solo della  
	 */
	public function send($Email, $mails, $body_mail, $debug=false) {

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
				foreach(Configure::read('EmailExcludeDomains') as $emailExcludeDomain) {
					if($debug) debug('Mail::send - EmailExcludeDomains '.$mail.' - '.$emailExcludeDomain);
					if(strpos($mail, $emailExcludeDomain)!==false) {
						$exclude = true;
						break;
					}
				}
				
				if($exclude)  {	
					if($debug) debug("EXCLUDE mail TO: ".$mail);
					$results['OK'] = $mail.' (modalita DEBUG)';
				}
				else {
					$Email->viewVars(array('content_info' => $this->_getContentInfo()));
					
					if(!$this->_mail_send) $Email->transport('Debug');
					
					if($debug) {
						if (!$this->_mail_send)
							if($debug) debug("Mail::send - inviata a " . $mail . " (modalita DEBUG)");
						else
							if($debug) debug("Mail::send - inviata a " . $mail);
											
						if($debug) debug("Mail::send - mail TO: ".$mail." body_mail ".$body_mail);
					}

					try {
						$Email->to($mail);
						$Email->send($body_mail);
						
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
			self::d($results, $debug);
		} // loops mails
		
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