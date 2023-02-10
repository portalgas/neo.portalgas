<?php
namespace App\Traits;

use Cake\Mailer\Email;
use Cake\Core\Configure;
use Cake\Log\Log;

/* 
 * mail inviate se $this->_config['mail.send'] = true
 */
trait MailTrait
{
    private $_email = null;
    private $_config = null;

    public function mailInit($subject, $template='default', $options=[]) {

        $this->_config = Configure::read('Config');

        $this->_email = new Email(Configure::read('EmailConfig')); // aws
        $this->_email->setFrom([Configure::read('SOC.mail') => Configure::read('SOC.name')])
              ->setReplyTo([Configure::read('Mail.no_reply_mail') => Configure::read('Mail.no_reply_name')])
              ->setSender([Configure::read('SOC.mail') => Configure::read('SOC.name')]) // real sender
              ;
        $this->_email->setSubject($subject); 
        /*
         * /src/Template/Email/html/{template}.ctp
         * /src/Template/Layout/Email/html/default.ctp
         */        
        $this->_email->setTemplate($template, 'default')
               ->setEmailFormat('html')
              ;              
    }

    public function mailSetViewVars($datas) {
        $this->_email->setViewVars($datas);
    }

    public function mailSend($mail) {

        $results = [];

        $exclude = false;
        foreach(Configure::read('EmailExcludeDomains') as $this->_emailExcludeDomain) {
            // if($debug) Log::debug('Mail::send - EmailExcludeDomains '.$mail.' - '.$this->_emailExcludeDomain, ['scope' => ['mail']]);
            if(strpos($mail, $this->_emailExcludeDomain)!==false) {
                $exclude = true;
                break;
            }
        }

        if($exclude)  {
            $results['esito'] = false;
            $results['mail'] = $mail.' (EmailExcludeDomains)';
            return $results;
        }

        $this->_email->setTo($mail);
        try {
            if(!$this->_config['mail.send']) {
                $results['esito'] = false;
                $results['mail'] = $mail.' (modalita DEBUG)';
            }
            else {
                $this->_email->send();
                $results['esito'] = true;
                $results['mail'] = $mail.' (modalita PRODUZIONE)';
            }
        } catch (Exception $e) {
            $results['esito'] = false;
            $results['mail'] = $mail;
            Log::error('mail', 'ERROR ----------------------!', ['scope' => ['mail']]);
            Log::error('mail', $e, ['scope' => ['mail']]);
            Log::error('mail', 'ERROR ----------------------!', ['scope' => ['mail']]);
        }  
        
        return $results;
    }    
}