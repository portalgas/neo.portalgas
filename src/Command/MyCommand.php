<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\Filesystem\File;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use App\Controller\Component\TotalComponent;
use App\Traits;

/*
 * creo file
 * /var/www/neo.portalgas/src/Command/Sh/mailUsersDelivery-1.sh ... 7.sh
 * /var/www/neo.portalgas/src/Command/Sh/mailUsersOrdersClose-0.sh ... 7.sh
 * /var/www/neo.portalgas/src/Command/Sh/mailUsersOrdersOpen-1.sh ... 7.sh
 */ 
class MyCommand extends Command
{
    use Traits\SqlTrait;

    private $cron =  null;
    private $file_name_sh =  null ;    
    protected $mail_send_max;
    protected $tot_files_sh; 
    protected $file_name_shs = [];

    public function initialize() {
        $this->Total = new TotalComponent(new ComponentRegistry());

        $this->mail_send_max = Configure::read('mailSendMax');
        $this->tot_files_sh = Configure::read('totFilesSh');

        $this->file_name_shs['mailUsersDelivery'] = 'mailUsersDelivery-%s.sh';
        $this->file_name_shs['mailUsersOrdersClose'] = 'mailUsersOrdersClose-%s.sh';
        $this->file_name_shs['mailUsersOrdersOpen'] = 'mailUsersOrdersOpen-%s.sh';
    }

    protected function _setCron($cron) {
        $this->cron = $cron;
    }

    protected function _setFileNameSh($file_name_sh) {
        $this->file_name_sh = $file_name_sh;
    }

    protected function _getOrganizationsGas($debug=false) {

        $modelOrganizations = $this->loadModel('Organizations');
        
        $where = ['Organizations.stato' => 'Y', 'Organizations.type' => 'GAS'];
        $organizations = $modelOrganizations->find()
				                            ->where($where)
				                            // ->limit(5)
				                            ->all();
        if($organizations->count()==0) {
            Log::info("Organizations empty!!", ['scope' => ['shell']]); 
            $this->abort();           
        }

        return $organizations;
    }

	protected function _getOrganizationTotUsers($organizationResults, $debug=false) {
        
        $results = [];
        
        if(!empty($organizationResults))
        foreach($organizationResults as $organizationResult) {

            $where = ['organization_id' => $organizationResult->id,
                      'block' => 0];
            // Log::info($where); 
            $tot_users = $this->Total->totUsers(null, $where, $debug);
            $tot_users = ($tot_users -2); // tolgo i 2 di sistema @portalgas.it
            
            $results[$organizationResult->id] = [];
            $results[$organizationResult->id]['id'] = $organizationResult->id;
            $results[$organizationResult->id]['name'] = $organizationResult->name;
            $results[$organizationResult->id]['tot_users'] = $tot_users; 
        }

        return $results;		
	}

	protected function _getArrayMailSendMax($totResults, $debug=false) {

        $tot_users = 0;
        $results = [];
        foreach ($totResults as $organization_id => $totResult) {

            $nel_limite = false;
            foreach ($results as $i => $result) {
                
                $tot_ctrl = ($result['tot_users'] + $totResult['tot_users']);
                Log::info('array['.$i.'].tot_users '.$result['tot_users'].' CTRL SE AGGIUNGERE GAS '.$totResult['name'].' '.$totResult['tot_users'].' = '.$tot_ctrl, ['scope' => ['shell']]);

                if($tot_ctrl < $this->mail_send_max) {
                    $nel_limite = true;
                    $results[$i]['organizations'][] = $totResult; 
                    $results[$i]['tot_users'] = ($results[$i]['tot_users'] + $totResult['tot_users']);
                    $results[$i]['ids'] = $results[$i]['ids'].' '.$totResult['id'];

                    Log::info('Add array['.$i.'] con GAS '.$totResult['name'].' '.$totResult['tot_users'].' array['.$i.'].tot_users diventa con '.$results[$i]['tot_users'], ['scope' => ['shell']]);
                    //Log::info($results);

                    break;                      
                }
                else {
                    if($i<count($result))
                        Log::info('array['.$i.'] supero limite, non ho altri gruppi => ne creo uno nuovo ', ['scope' => ['shell']]);
                    else
                        Log::info('array['.$i.'] supero limite, ctrl altro GAS', ['scope' => ['shell']]);
                }
            }
            
            if(!$nel_limite) {
                $next_array = count($results);
                $results[$next_array]['organizations'][] = $totResult; 
                $results[$next_array]['tot_users'] = $totResult['tot_users'];
                $results[$next_array]['ids'] = $totResult['id'];

                Log::info('New array['.$next_array.'] tot_users '.$results[$next_array]['tot_users'].' preso da GAS '.$totResult['name'].' '.$totResult['tot_users'], ['scope' => ['shell']]);
            }
        }

        return $results;
    }

    protected function _getContentShTemplate($debug=false) {

        $template_sh_path_full = Configure::read('Sh.template.path.full');
        Log::info('template_sh_path_full '.$template_sh_path_full, ['scope' => ['shell']]);

        $file = new File($template_sh_path_full);
        $file_content = $file->read(true, 'r');

        return $file_content;
    }     

    protected function _writeFilesSh($results, $debug=false) {

        $tot_files_created = 0;

        if(!empty($results)) {
            foreach ($results as $result) {

                /*
                 * sostituisco i placeholder al template
                 */
                $template = $this->_getContentShTemplate();
                $template = sprintf($template, $result['ids'], $this->cron);
                // debug($template);
            
                /*
                 * nome del file .sh 
                 */
                $file_name_sh_complete = sprintf($this->file_name_sh, $tot_files_created);
                $file_full_path = Configure::read('Sh.template.dir.path.full') . $file_name_sh_complete;
                $file = new File($file_full_path);
                $file->write($template);

                $tot_files_created ++;
                Log::info('Creato file '.$tot_files_created.' '.$file_full_path, ['scope' => ['shell']]);

                foreach ($result['organizations'] as $rs) {
                    $this->_insertDb($rs, $file_name_sh_complete, $debug);
                }
            }
        }
        else {
            /*
             * nessun record trovato
             */
            $results = [];
            $results['id'] = 0;
            $results['tot_users'] = 0;
            $file_name_sh_complete = sprintf($this->file_name_sh, 'x');
            $this->_insertDb($results, $file_name_sh_complete, $debug);
        }  // end if(!empty($results))

        /*
         * creo eventuali file sh vuoti perche' richiamati dal cron
         */
        // debug('tot_files_created '.$tot_files_created.' tot_files_sh '.$this->tot_files_sh);        
        if($tot_files_created < $this->tot_files_sh) {
            $tot_files_created++;
            for($i=$tot_files_created; $i<=$this->tot_files_sh; $i++) {

                $file_name_sh_complete = sprintf($this->file_name_sh, $i);
                $file_full_path = Configure::read('Sh.template.dir.path.full') . $file_name_sh_complete;
                $file = new File($file_full_path);
                $file->write("");    

                Log::info('Creato file vuoto '.$i.' '.$file_full_path, ['scope' => ['shell']]);
            }

            return true;

        } // if($tot_files_created < $this->tot_files_sh)
        else {
            Log::error('Creati '.$tot_files_created.' file ma il cron ne richiama '.$this->tot_files_sh, ['scope' => ['shell']]);
            return false;            
        }
    }

    /*
     * cancello file sh creati precedentemente
     */
    protected function _deleteOldFileSh($file_name_sh='') {

        $file_name_shs = [];

        if(empty($file_name_sh)) 
            $file_name_shs = $this->file_name_shs;
        else
            $file_name_shs[] = $file_name_sh.'-%s.sh';
            
        foreach ($file_name_shs as $file_name_sh) {
            for($i=0; $i <= $this->tot_files_sh; $i++) {

                $file_name_sh_complete = sprintf($file_name_sh, $i);
                $file_full_path = Configure::read('Sh.template.dir.path.full') . $file_name_sh_complete;

                if(file_exists($file_full_path)) {
                    $file = new File($file_full_path);
                    if($file->delete())   
                        Log::info('Delete old file '.$i.' '.$file_full_path, ['scope' => ['shell']]);
                    else
                        Log::error('Not delete old file '.$i.' '.$file_full_path, ['scope' => ['shell']]);
                }
                else {
                    Log::info('not exist to delete old file '.$i.' '.$file_full_path, ['scope' => ['shell']]);
                }
            }
        } // end foreach ($this->file_name_shs => $file_name_sh)
    }

    private function _insertDb($result, $file_name_sh_complete, $debug) {

        $modelMailSends = $this->loadModel('MailSends');
        
        $data = [];
        $data['organization_id'] = $result['id'];
        $data['tot_users'] = $result['tot_users'];
        $data['file_sh'] = $file_name_sh_complete;
        $data['cron'] = $this->cron;
        $data['data'] = date('Y-m-d');
        /*
         * il ResponseMiddleware fa il match con data_
         */
        $data['data'] = $this->convertDate($data['data']);

        $mailSend = $modelMailSends->newEntity();
        $mailSend = $modelMailSends->patchEntity($mailSend, $data);
        if (!$modelMailSends->save($mailSend)) {
            Log::error($mailSend->getErrors(), ['scope' => ['shell']]);
        }

        return true;
    } 
}