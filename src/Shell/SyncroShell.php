<?php
/*
 * 
 *  * * * * * /var/www/..../bin/cake syncro users_expiration 1,text,false
 *
 * bin/cake bake shell Syncro
 *
 * LOG  cli-debug.log
 */

namespace App\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

/**
 * Syncro shell command.
 */
class SyncroShell extends Shell
{
    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main($method, $args='')
    {
        // $this->out($this->OptionParser->help());
        Log::write('debug', 'SyncroShell::main '.$method.' args '.$args);

        $this->{$method}($args);
    }


    public function users_expiration($args)
    {

        Log::write('debug', 'SyncroShell::users_expiration START');

        $today = date('Y-m-d');
        $usersTable = TableRegistry::get('Users');
        $users = $usersTable->find()
                                     ->where(['DATE(expiration) <= ' => $today, 'user_state_id != ' => Configure::read('AuthorizationStateExpired')]);
        // debug($today);                                              
        // debug($users);                                              
        foreach($users as $user) {

            $data['user_state_id'] = Configure::read('AuthorizationStateExpired');
            $user = $usersTable->patchEntity($user, $data);
            if ($usersTable->save($user)) {
                Log::write('debug', "user name ".$user->name.' ('.$user->id.') UPDATE to '.Configure::read('AuthorizationStateExpired').' OK');                
            }            
            else {
                Log::write('debug', "user name ".$user->name.' ('.$user->id.') KO');
                Log::write('debug', $user->errors());
            }
        }
       
        Log::write('debug', 'SyncroShell::users_expiration END');
    }    
}
