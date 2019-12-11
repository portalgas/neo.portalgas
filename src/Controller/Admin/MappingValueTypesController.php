<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * MappingValueTypes Controller
 *
 * @property \App\Model\Table\MappingValueTypesTable $MappingValueTypes
 *
 * @method \App\Model\Entity\MappingValueType[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MappingValueTypesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $mappingValueTypes = $this->paginate($this->MappingValueTypes);

        $this->set(compact('mappingValueTypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Mapping Value Type id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $mappingValueType = $this->MappingValueTypes->get($id, [
            'contain' => ['Mappings']
        ]);

        $this->set('mappingValueType', $mappingValueType);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $mappingValueType = $this->MappingValueTypes->newEntity();
        if ($this->request->is('post')) {
            $mappingValueType = $this->MappingValueTypes->patchEntity($mappingValueType, $this->request->getData());
            if ($this->MappingValueTypes->save($mappingValueType)) {
                $this->Flash->success(__('The {0} has been saved.', 'Mapping Value Type'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Mapping Value Type'));
        }
        $this->set(compact('mappingValueType'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Mapping Value Type id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $mappingValueType = $this->MappingValueTypes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $mappingValueType = $this->MappingValueTypes->patchEntity($mappingValueType, $this->request->getData());
            if ($this->MappingValueTypes->save($mappingValueType)) {
                $this->Flash->success(__('The {0} has been saved.', 'Mapping Value Type'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Mapping Value Type'));
        }
        $this->set(compact('mappingValueType'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Mapping Value Type id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mappingValueType = $this->MappingValueTypes->get($id);
        if ($this->MappingValueTypes->delete($mappingValueType)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Mapping Value Type'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Mapping Value Type'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
