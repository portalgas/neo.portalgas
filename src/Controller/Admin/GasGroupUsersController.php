<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * GasGroupUsers Controller
 *
 * @property \App\Model\Table\GasGroupUsersTable $GasGroupUsers
 *
 * @method \App\Model\Entity\GasGroupUser[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GasGroupUsersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Organizations', 'Users', 'GasGroups'],
        ];
        $gasGroupUsers = $this->paginate($this->GasGroupUsers);

        $this->set(compact('gasGroupUsers'));
    }

    /**
     * View method
     *
     * @param string|null $id Gas Group User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $gasGroupUser = $this->GasGroupUsers->get($id, [
            'contain' => ['Organizations', 'Users', 'GasGroups'],
        ]);

        $this->set('gasGroupUser', $gasGroupUser);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $gasGroupUser = $this->GasGroupUsers->newEntity();
        if ($this->request->is('post')) {
            $gasGroupUser = $this->GasGroupUsers->patchEntity($gasGroupUser, $this->request->getData());
            if ($this->GasGroupUsers->save($gasGroupUser)) {
                $this->Flash->success(__('The {0} has been saved.', 'Gas Group User'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Gas Group User'));
        }
        $organizations = $this->GasGroupUsers->Organizations->find('list', ['limit' => 200]);
        $users = $this->GasGroupUsers->Users->find('list', ['limit' => 200]);
        $gasGroups = $this->GasGroupUsers->GasGroups->find('list', ['limit' => 200]);
        $this->set(compact('gasGroupUser', 'organizations', 'users', 'gasGroups'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Gas Group User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $gasGroupUser = $this->GasGroupUsers->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $gasGroupUser = $this->GasGroupUsers->patchEntity($gasGroupUser, $this->request->getData());
            if ($this->GasGroupUsers->save($gasGroupUser)) {
                $this->Flash->success(__('The {0} has been saved.', 'Gas Group User'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Gas Group User'));
        }
        $organizations = $this->GasGroupUsers->Organizations->find('list', ['limit' => 200]);
        $users = $this->GasGroupUsers->Users->find('list', ['limit' => 200]);
        $gasGroups = $this->GasGroupUsers->GasGroups->find('list', ['limit' => 200]);
        $this->set(compact('gasGroupUser', 'organizations', 'users', 'gasGroups'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Gas Group User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $gasGroupUser = $this->GasGroupUsers->get($id);
        if ($this->GasGroupUsers->delete($gasGroupUser)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Gas Group User'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Gas Group User'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
