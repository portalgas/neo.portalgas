<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

/**
 * MailSends Controller
 *
 * @property \App\Model\Table\MailSendsTable $MailSends
 *
 * @method \App\Model\Entity\MailSend[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MailSendsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(!isset($this->Authentication->getIdentity()->acl) && !$this->Authentication->getIdentity()->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }

    public function index()
    {
        $this->MailSends->Organizations->removeBehavior('OrganizationsParams');
        $this->paginate = [
            'contain' => ['Organizations'],
            'order' => ['data' => 'desc']
        ];
        $mailSends = $this->paginate($this->MailSends);

        $this->set(compact('mailSends'));
    }

    /**
     * View method
     *
     * @param string|null $id Mail Send id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $mailSend = $this->MailSends->get($id, [
            'contain' => ['Organizations'],
        ]);

        $this->set('mailSend', $mailSend);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $mailSend = $this->MailSends->newEntity();
        if ($this->request->is('post')) {
            $mailSend = $this->MailSends->patchEntity($mailSend, $this->request->getData());
            if ($this->MailSends->save($mailSend)) {
                $this->Flash->success(__('The {0} has been saved.', 'Mail Send'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Mail Send'));
        }
        $organizations = $this->MailSends->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('mailSend', 'organizations'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Mail Send id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $mailSend = $this->MailSends->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $mailSend = $this->MailSends->patchEntity($mailSend, $this->request->getData());
            if ($this->MailSends->save($mailSend)) {
                $this->Flash->success(__('The {0} has been saved.', 'Mail Send'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Mail Send'));
        }
        $organizations = $this->MailSends->Organizations->find('list', ['limit' => 200]);
        $this->set(compact('mailSend', 'organizations'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Mail Send id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mailSend = $this->MailSends->get($id);
        if ($this->MailSends->delete($mailSend)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Mail Send'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Mail Send'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
