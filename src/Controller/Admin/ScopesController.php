<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * Scopes Controller
 *
 * @property \App\Model\Table\ScopesTable $Scopes
 *
 * @method \App\Model\Entity\Scope[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ScopesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $scopes = $this->paginate($this->Scopes);

        $this->set(compact('scopes'));
    }

    /**
     * View method
     *
     * @param string|null $id Scope id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $scope = $this->Scopes->get($id, [
            'contain' => ['Tables']
        ]);

        $this->set('scope', $scope);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $scope = $this->Scopes->newEntity();
        if ($this->request->is('post')) {
            $scope = $this->Scopes->patchEntity($scope, $this->request->getData());
            if ($this->Scopes->save($scope)) {
                $this->Flash->success(__('The {0} has been saved.', 'Scope'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Scope'));
        }
        $this->set(compact('scope'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Scope id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $scope = $this->Scopes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $scope = $this->Scopes->patchEntity($scope, $this->request->getData());
            if ($this->Scopes->save($scope)) {
                $this->Flash->success(__('The {0} has been saved.', 'Scope'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Scope'));
        }
        $this->set(compact('scope'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Scope id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $scope = $this->Scopes->get($id);
        if ($this->Scopes->delete($scope)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Scope'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Scope'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
