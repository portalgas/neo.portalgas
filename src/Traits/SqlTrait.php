<?php
namespace App\Traits;

use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use ArrayObject;

trait SqlTrait
{
    public function getSort($model, $where=[]) {

        if(is_string($model))
            $model = TableRegistry::get($model);

        $sort = '';
    
        /*
         * progressivo sort
         */     
        $max = $this->getMax($model, 'sort', $where);
        $sort = ($max + 10);

        //debug($sort);

        return $sort;
    }

    /*
     * estrae il valore e lo incrementa (ex num progressivo offerta)
     */
    public function getMax($model, $field, $where = [], $debug=false) {

        if(is_string($model))
            $model = TableRegistry::get($model);
        
        if($debug) debug($model);
        if($debug) debug($where);

        $query = $model->find()->where($where);
        $query = $query->select(['max' => $query->func()->max($field)]);
        $results = $query->toList();

        if (!isset($results[0]->max))
            $results = 0;
        else
            $results = $results[0]->max;

        if($debug) debug($results);

        return $results;
    }

    /*
     * conta gli elementi estratti e li incrementa (ex nome dettaglio offerta)
     */
    public function getCount($model, $where = [], $debug=false) {

        if(is_string($model))
            $model = TableRegistry::get($model);
        
        if($debug) debug($model);
        if($debug) debug($where);
        
        $query = $model->find()->where($where);
        $query = $query->select(['count' => $query->func()->count('*')]);
        $results = $query->toList();

        if (!isset($results[0]->count))
            $results = 0;
        else
            $results = $results[0]->count;

        if($debug) debug($results);

        return $results;
    }

    /*
     * estrae tutti gli elementi di una tabella non relazioni in un'altra
     * ex tutti gli users (model) non associati ai collaboratori (modelReleated)
     */
    public function getElementNotReleated($field, $model, $modelReleated, $where = [], $whereReleated = [], $debug=false) {

        if(is_string($model))
            $model = TableRegistry::get($model);
        // debug($model);

        if(is_string($modelReleated))
            $modelReleated = TableRegistry::get($modelReleated);
        // debug($modelReleated);

        $whereReleated = array_merge($whereReleated, [$field.' > ' => '0']);
        $ids = []; // id da escludere
        $query = $modelReleated->find()->select($field)->where($whereReleated);
        $releatedResults = $query->toList();
        if(!empty($releatedResults)) {
            foreach($releatedResults as $releatedResult)
                array_push($ids, $releatedResult->{$field});

        }
        // debug($ids);

        if(!empty($ids))
            $where = array_merge($where, ['id not in ' => $ids]); 
        $query = $model->find()->where($where);
        $results = $query->toArray();
        // debug($results);

        return $results;
    }  

    public function getElementNotReleatedList($field, $model, $modelReleated, $where = [], $whereReleated = [], $debug=false)
            {

        $listResults = [];

        $results = $this->getElementNotReleated($field, $model, $modelReleated, $where, $whereReleated, $debug);
        if(!empty($results)) {
            foreach($results as $result)
                $listResults[$result->id] = $result->name;
        }
        // debug($listResults);

        return $listResults;
    } 

    public function getDefaultIni($model, $where = [], $debug=false) {

        if(is_string($model))
            $model = TableRegistry::get($model);
        
        $where = array_merge($where, ['is_default_ini' => 1, 'is_active' => 1]);
        if($debug) debug($model);
        if($debug) debug($where);

        $query = $model->find()->where($where);
        $query = $query->select();
        $results = $query->first();

        if($debug) debug($results);

        return $results;
    }

    public function getIdDefaultIni($model, $where = [], $debug=false) {

        $id = 0;
        $results = $this->getDefaultIni($model, $where, $debug);

        if(!empty($results)) {
            $id = $results->id;
        }

        return $id;
    }    

    public function getDefaultEnd($model, $where = [], $debug=false) {

        if(is_string($model))
            $model = TableRegistry::get($model);
        
        $where = array_merge($where, ['is_default_end' => 1, 'is_active' => 1]);
        if($debug) debug($model);
        if($debug) debug($where);

        $query = $model->find()->where($where);
        $query = $query->select();
        $results = $query->first();

        if($debug) debug($results);

        return $results;
    }

    public function getIdDefaultEnd($model, $where = [], $debug=false) {

        $id = 0;
        $results = $this->getDefaultEnd($model, $where, $debug);

        if(!empty($results)) {
            $id = $results->id;
        }

        return $id;
    }  

    public function convertImport($import) {

        if (!empty($import)) {
            $import = str_replace(',', '.', $import);
        }
        return $import;
    } 

    /*
     * converte da dd/mm/yyyy a array['year', 'month', 'day']
     */
    public function convertDate($key, $data) {

        $convertData = [];
        if (!empty($data)) {
            if(is_array($data)) {
               // $convertData = $data['year'].'-'.$data['month'].'-'.$data['day'];
                $convertData = $data;
            }
            else {
                $data = str_replace('/', '-', $data);
                $convertData['year'] = date('Y', strtotime($data));
                $convertData['month'] = date('m', strtotime($data));
                $convertData['day'] = date('d', strtotime($data));
            }
        }
        return $convertData;
    } 

    protected function convertRequestDateToDatabase($request_data) {

        foreach ($request_data as $key => $value) {
            if ($this->stringStartsWith($key, 'data_') && !empty($value)) {
                $request_data[$key] = $this->convertDate($key, $value);
                // debug($key.' '.$request_data[$key]);
            }             
        }

        return $request_data;
    } 
   
    public function tableTruncate($table) {
        $sqls = $table->getSchema()->truncateSql($table->getConnection());
        foreach ($sqls as $sql) {
           // debug($sql);
           $table->getConnection()->execute($sql)->execute();
        } 
        return true;       
    } 
}