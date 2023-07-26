<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * GeoRegions Controller
 *
 * @property \App\Model\Table\GeoRegionsTable $GeoRegions
 *
 * @method \App\Model\Entity\GeoRegion[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GeoRegionsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Auths');
    }

    public function beforeFilter(Event $event) {
        
        parent::beforeFilter($event);

        if(empty($this->_user)) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
                
        if(!$this->_user->acl['isRoot']) {
            $this->Flash->error(__('msg_not_permission'), ['escape' => false]);
            return $this->redirect(Configure::read('routes_msg_stop'));
        }
    }
	
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $geoRegions = $this->paginate($this->GeoRegions);

        $this->set(compact('geoRegions'));
    }

    /**
     * View method
     *
     * @param string|null $id K Geo Region id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $geoRegion = $this->GeoRegions->get($id, [
            'contain' => [],
        ]);

        $this->set('geoRegion', $geoRegion);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $geoRegion = $this->GeoRegions->newEntity();
        if ($this->request->is('post')) {
            $geoRegion = $this->GeoRegions->patchEntity($geoRegion, $this->request->getData());
            if ($this->GeoRegions->save($geoRegion)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Geo Region'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Geo Region'));
        }
        $this->set(compact('geoRegion'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Geo Region id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $geoRegion = $this->GeoRegions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $geoRegion = $this->GeoRegions->patchEntity($geoRegion, $this->request->getData());
            if ($this->GeoRegions->save($geoRegion)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Geo Region'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Geo Region'));
        }
        $this->set(compact('geoRegion'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Geo Region id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $geoRegion = $this->GeoRegions->get($id);
        if ($this->GeoRegions->delete($geoRegion)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Geo Region'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Geo Region'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
