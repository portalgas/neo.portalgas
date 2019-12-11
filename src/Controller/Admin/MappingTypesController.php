<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * MappingTypes Controller
 *
 * @property \App\Model\Table\MappingTypesTable $MappingTypes
 *
 * @method \App\Model\Entity\MappingType[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MappingTypesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $mappingTypes = $this->paginate($this->MappingTypes);

        $this->set(compact('mappingTypes'));
    }

    /**
     * View method
     *
     * @param string|null $id Mapping Type id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $mappingType = $this->MappingTypes->get($id, [
            'contain' => ['Mappings']
        ]);

        $this->set('mappingType', $mappingType);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $mappingType = $this->MappingTypes->newEntity();
        if ($this->request->is('post')) {
            $mappingType = $this->MappingTypes->patchEntity($mappingType, $this->request->getData());
            if ($this->MappingTypes->save($mappingType)) {
                $this->Flash->success(__('The {0} has been saved.', 'Mapping Type'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Mapping Type'));
        }
        $this->set(compact('mappingType'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Mapping Type id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $mappingType = $this->MappingTypes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $mappingType = $this->MappingTypes->patchEntity($mappingType, $this->request->getData());
            if ($this->MappingTypes->save($mappingType)) {
                $this->Flash->success(__('The {0} has been saved.', 'Mapping Type'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} could not be saved. Please, try again.', 'Mapping Type'));
        }
        $this->set(compact('mappingType'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Mapping Type id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mappingType = $this->MappingTypes->get($id);
        if ($this->MappingTypes->delete($mappingType)) {
            $this->Flash->success(__('The {0} has been deleted.', 'Mapping Type'));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', 'Mapping Type'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
