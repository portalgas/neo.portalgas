<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * GeoProvinces Controller
 *
 * @property \App\Model\Table\GeoProvincesTable $GeoProvinces
 *
 * @method \App\Model\Entity\GeoProvince[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GeoProvincesController extends AppController
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
        $this->paginate = [
            'contain' => ['GeoRegions'],
        ];
        $geoProvinces = $this->paginate($this->GeoProvinces);

        $this->set(compact('geoProvinces'));
    }

    /**
     * View method
     *
     * @param string|null $id K Geo Province id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $geoProvince = $this->GeoProvinces->get($id, [
            'contain' => ['GeoRegions'],
        ]);

        $this->set('geoProvince', $geoProvince);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $geoProvince = $this->GeoProvinces->newEntity();
        if ($this->request->is('post')) {
            $geoProvince = $this->GeoProvinces->patchEntity($geoProvince, $this->request->getData());
            if ($this->GeoProvinces->save($geoProvince)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Geo Province'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Geo Province'));
        }
        $geoRegions = $this->GeoProvinces->GeoRegions->find('list', ['limit' => 200]);
        $this->set(compact('geoProvince', 'geoRegions'));
    }


    /**
     * Edit method
     *
     * @param string|null $id K Geo Province id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $geoProvince = $this->GeoProvinces->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $geoProvince = $this->GeoProvinces->patchEntity($geoProvince, $this->request->getData());
            if ($this->GeoProvinces->save($geoProvince)) {
                $this->Flash->success(__('The {0} has been saved.', 'K Geo Province'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'K Geo Province'));
        }
        $geoRegions = $this->GeoProvinces->GeoRegions->find('list', ['limit' => 200]);
        $this->set(compact('geoProvince', 'geoRegions'));
    }


    /**
     * Delete method
     *
     * @param string|null $id K Geo Province id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $geoProvince = $this->GeoProvinces->get($id);
        if ($this->GeoProvinces->delete($geoProvince)) {
            $this->Flash->success(__('The {0} has been deleted.', 'K Geo Province'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'K Geo Province'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
