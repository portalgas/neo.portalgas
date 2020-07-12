<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;
use Cake\Core\Configure;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use App\Controller\Component\TotalComponent;

class MyCommand extends Command
{
    protected $mail_send_max = 250;

    public function initialize() {
        $this->Total = new TotalComponent(new ComponentRegistry());
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
        
        foreach($organizationResults as $organizationResult) {

            $where = ['organization_id' => $organizationResult->id,
                      'block' => 0];
            // Log::info($where); 
            $tot_user = $this->Total->totUsers(null, $where, $debug);
            $tot_user = ($tot_user -2); // tolgo i 2 di sistema @portalgas.it
            
            $results[$organizationResult->id] = [];
            $results[$organizationResult->id]['id'] = $organizationResult->id;
            $results[$organizationResult->id]['name'] = $organizationResult->name;
            $results[$organizationResult->id]['tot_user'] = $tot_user; 
        }

        return $results;		
	}

	protected function _getArrayMailSendMax($totResults, $debug=false) {

        $tot_users = 0;
        $results = [];
        foreach ($totResults as $organization_id => $totResult) {

            $nel_limite = false;
            foreach ($results as $i => $result) {
                
                $tot_ctrl = ($result['tot_users'] + $totResult['tot_user']);
                Log::info('array['.$i.'].tot_users '.$result['tot_users'].' CTRL SE AGGIUNGERE GAS '.$totResult['name'].' '.$totResult['tot_user'].' = '.$tot_ctrl, ['scope' => ['shell']]);

                if($tot_ctrl < $this->mail_send_max) {
                    $nel_limite = true;
                    $results[$i]['organizations'][] = $totResult; 
                    $results[$i]['tot_users'] = ($results[$i]['tot_users'] + $totResult['tot_user']);

                    Log::info('Add array['.$i.'] con GAS '.$totResult['name'].' '.$totResult['tot_user'].' array['.$i.'].tot_users diventa con '.$results[$i]['tot_users'], ['scope' => ['shell']]);
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
                $results[$next_array]['tot_users'] = $totResult['tot_user'];

                Log::info('New array['.$next_array.'] tot_users '.$results[$next_array]['tot_users'].' preso da GAS '.$totResult['name'].' '.$totResult['tot_user'], ['scope' => ['shell']]);
            }
        }

        return $results;
    }
}