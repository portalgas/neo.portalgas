<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\Core\Configure;
use Cake\I18n\FrozenDate;
use App\Traits;

class HtmlCustomHelper extends FormHelper
{
	private $debug = false;
	public  $helpers = ['Html', 'Form'];

    use Traits\UtilTrait;

    public function initialize(array $config)
    {
        // debug($config);
    }

    public function getCsrfToken() {
        $str = '';
        $str .= $this->getView()->getRequest()->getParam('_csrfToken');
        return $str;
    }

    public function csrfTokenHidden() {
        return $this->Form->hidden('CsrfToken', ['name' => '_csrfToken', 'value' => $this->getCsrfToken(), 'autocomplete' => 'off']);
    }

    // https://api.cakephp.org/3.8/class-Cake.View.Helper.TimeHelper.html
    public function data($data, $format='dd MMMM yyyy') {
        $str = $data->i18nFormat($format);
        // $str = $data->format($format);
        return $str;
    } 

    public function noteMore($value) {

        $max_len = Configure::read('NoteMoreLen');

        $str = '';
        $len = strlen($value);
        if($len > $max_len) {
            $uniq = uniqid();
            $value = h($value);
            $intro = substr($value, 0, $max_len);

            $str .= '<div id="note-intro-'.$uniq.'"" style="max-width:200px;">';
            $str .= $intro;
            $str .= ' <a href="#note-'.$uniq.'" onClick="$(\'#note-intro-'.$uniq.'\').hide();$(\'#note-full-'.$uniq.'\').show(200);"><b>...</b></a>';
            $str .= '</div>';

            $str .= '<div id="note-full-'.$uniq.'"" style="display:none;max-width:200px">';
            $str .= $value;
            $str .= '</div>';
        }
        else 
            $str = '<div class="note" style="max-width:200px;">'.$value.'</div>';

        return $str;
    }

    public function note($value) {
        $str = '<div class="direct-chat-text">'.h($value).'</div>';
        return $str;
    }



    public function alert($msg, $level_alert='info') {
        $str = ''; 
        $str .= '<div class="alert alert-'.$level_alert.' alert-dismissible">';
        $str .= '  <button type="button" class="close" data-dismiss="alert">&times;</button>';
        $str .= $msg;
        $str .= '</div>';
        return $str;
    }

    public function fileUpload($entity, $field, $options=[]) {
        if(!isset($options['name']))
            $name = $entity->{$field}.' <i class="fa fa-download fa-lg"></i>';
        else
            $name = $options['name'];

        $path = '/files/'.$entity->source().'/'.$field.'/'.$entity->id.'/'.$entity->{$field};
        // debug($path);
        $str = $this->Html->link($name, $path, ['fullBase' => true, 'target' => '_blank', 'title' => $entity->{$field}, 'escape' => false]);
        return $str;
    }

    public function mail($value, $label='') {
        $str = '';

        if(!empty($value)) {
            if(empty($label))
                $label = $value;
            $str = '<a href="mailto:'.$value.'" target="_blank">'.$label.'</a>';
        }
        return $str;
    }    

    public function mailIco($value) {
        return $this->mail($value, '<i class="fa fa-fw fa-envelope"></i>');
    }

    public function www($value, $label='') {
    	$str = '';

    	if(!empty($value)) {
    		if(empty($label))
    			$label = $value;

            $value = $this->decorateWWW($value);
	    	$str = '<a href="'.$value.'" target="_blank">'.$label.'</a>';
    	}
    	return $str;
    }
  
    public function drawBagde($name, $html_class, $options=[]) {

        $html = '';

        if(!empty($html_class))
          echo '<span class="label label-'.$html_class.'">'.$name.'</span>';
        else
          echo $name;

        return $html;
    }
    
    public function drawState($state, $options=[]) {

        $html = '';

        // debug(get_class($state));
        $classname = get_class($state); // App\Model\Entity\PayType
        $controller = '';
        if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
            $controller = $matches[1].'s';
        }
        
        if(!empty($state->css_color))
           $html .=  '<span class="badge" style="background-color:'.$state->css_color.'">&nbsp;</span> ';
        if(isset($options['no-label']) && $options['no-label']) {

        }
        else {
            if(isset($options['no-link']))
                $html .= $state->name;
            else
                $html .= $this->Html->link($state->name, ['controller' => $controller, 'action' => 'view', $state->id]);
        }

        return $html;
    }             

    public function drawDocumentPreview($document, $options=[]) {

        if(isset($options['width']))
            $width = $options['width'];
        else 
            $width = '150px';

        $results = '';

        switch (strtolower($document->file_ext)) {
            case 'gif':
            case 'png':
            case 'jpg':
            case 'jpeg':
                $img_path = $document->path.$document->file_name;
                $results = '<a href="'.$img_path.'" target="_blank"><img width="'.$width.'" src="'.$img_path.'" title="'.$document->name.'" /></a>';
            break;
            default:
                $results = $document->file_name;
            break;
        }

        return $results;
    }
    
    /*
     * Configure::write('icon_is_system', ['OK' => 'fa fa-lock', 'KO' => 'fa fa-unlock-alt']); 
     */
    public function drawTruFalse($model, $field, $icons=[]) {

        $html = '';

        /*
         * estraggo da App\Model\Entity\... la classe
         */
        // debug(get_class($model));
        $classname = get_class($model);
        $entity = '';
        if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
            $entity = $matches[1].'s';
        }
        if(empty($entity))
            return $html;

        $value = $model->{$field};

        if($value===true) 
            $data_attr_value = '1';
        else
            $data_attr_value = '0';
        $data_attrs = '';
        $data_attrs .= 'data-attr-id='.$model->id.' data-attr-entity="'.$entity.'" data-attr-field="'.$field.'" data-attr-value="'.$data_attr_value.'"';
        
        /*
         * per is_default_ini e is_default_end disabilito fieldUpdateAjaxClick perche' devo aggiornarli tutti
         */
        if($field=='is_default_ini' || $field=='is_default_end')
            $class = 'fieldUpdateAjaxClick-disabled';
        else 
            $class = 'fieldUpdateAjaxClick';

        if($value===true) {
            if(isset($icons['OK']))
                $icon = $icons['OK'];
            else
                $icon = 'glyphicon glyphicon-ok';
            $html .= '<span '.$data_attrs.' class="'.$class.' trueFalse '.$icon.' icon-true" title="'.__('Yes').'"></span>';
        }
        else
        if($value===false) {
            if(isset($icons['KO']))
                $icon = $icons['KO'];
            else
                $icon = 'glyphicon glyphicon-remove';
            $html .= '<span '.$data_attrs.' class="'.$class.' trueFalse '.$icon.' icon-false" title="'.__('No').'"></span>';
        }
        else 
            $html .= '';

        return $html;
    }   

    public function importo($value) {
    	$str = '';
    	if(!empty($value))
    		$str = number_format($value, 2, Configure::read('separatoreDecimali'), Configure::read('separatoreMigliaia')).' &euro;';
    	return $str;
    }

    /*
     * id dell'entity, in edit escludo se stesso
     * modificare /webroot/js/vue/users-roles-add-edit.js
     */
    public function textExist($id, $fieldName, $value='', $options=[]) {

        $html = '';
        $html .= '<div class="form-group">';
        $html .= '<label for="'.__($fieldName).'" class="control-label">'.__($fieldName).'</label>';
        $html .= '<div class="input-group">
                  <div class="input-group-addon"><i class="fa fa-shield"></i></div>';
        $html .= '<input type="text" name="'.$fieldName.'" 
                                     placeholder="'.__($fieldName).'" 
                                     data-attr-id="'.$id.'"
                                     value="'.$value.'" 
                                     id="'.$fieldName.'" 
                                     class="form-control" 
                                     @blur="ctrlExist($event, \''.$fieldName.'\');" />';
        $html .= '</div>';
        $html .= '<div v-if="isRun_'.$fieldName.'" class="fa-lg fa fa-spinner"></div>';
        $html .= '</div>';

        return $html;
    }

    /* 
     * '@change' => $select1 ['change'] metodo definito in file vue
     */
    public function selectOnChange($select1, $select2, $options=[]) {

        if(isset($options['col-size']))
            $col_size = $options['col-size'];
        else
            $col_size = '6';

        if(isset($select2['value']))
            $value2 = $select2['value'];
        else
            $value2 = 0;

        $html = '';
        $html .= '<div class="col-md-'.$col_size.'">';
        $html .= $this->Form->control($select1['id'], ['options' => $select1 ['options'], 'class' => 'form-control', 'empty' => [0 => __('FrmListEmpty')], '@change' => $select1 ['change'], 'v-model' => $select1['id'], 'required' => $select1['required']]);
        $html .= '</div>';
        $html .= '<div class="col-md-'.$col_size.'">';
        $html .= '<div class="form-group input select">';
        $html .= '<label for="'.$select2['label'].'" class="control-label">'.$select2['label'].'</label>';
        $html .= '<select id="'.$select2['id'].'" name="'.$select2['id'].'" class="form-control" v-model="'.$select2['id'].'" required='.$select1['required'].'>';
        $html .= '<option 
                  v-for="(row, index) in '.$select2['model'].'"
                  :value="index">{{ row }}</option>';
        $html .= '</select>';
        $html .= '<div v-if="isRun_'.$select2['model'].'" class="fa-lg fa fa-spinner"></div>';
        $html .= '</div>'; // form-group
        $html .= '</div>'; // col
        
        return $html;
    }

    public function datepicker($fieldName, $options=[]) {

        /*
         * ResponseMiddleware trasforma y/m/d in array ['year' => '2020', 'month' => '06','day' => '19']
         *  devo riconvertirlo in \Cake\I18n\FrozenDate
         */
        $value = $this->getView()->getRequest()->getData($fieldName);
        if(!empty($value) && is_array($value)) {
            $options['value'] = $value['day'].'/'.$value['month'].'/'.$value['year'];
        }

		$optionDefault = ['type' => 'text', 'label' => false, 'class' => 'form-control datepicker pull-right'];
    	if(!empty($options)) {
    		$optionDefault = array_merge($optionDefault, $options);
    		if(isset($options['class']))
    			$optionDefault['class'] = 'form-control datepicker pull-right '.$options['class'];
    	}

    	$html = '';
    	$html .= "
              <div class=\"form-group\">";
        
        if(!isset($options['label']) || $options['label']==true)
	         $html .= "<label>".__($fieldName)."</label>";

        $html .= "<div class=\"input-group date\">
                  <div class=\"input-group-addon\">
                    <i class=\"fa fa-calendar\"></i>
                  </div>";
        $html .= $this->Form->control($fieldName, $optionDefault);
        $html .= "</div>
              </div>
              ";

        return $html;
    }

    public function daterangepicker($fieldName, $options=[]) {

		$optionDefault = ['type' => 'text', 'label' => false, 'class' => 'form-control bootstrap-daterangepicker pull-right'];

		// escludo <div class="form-group input text">
		$optionDefault += ['templates' => ['inputContainer' => '{{content}}']];

    	if(!empty($options)) {

    		$val = '';
    		if(isset($options['values'])) {
    			foreach($options['values'] as $key => $value)
    				$val .= $value.' - ';

    			$val = substr($val, 0, strlen($val)-3);
    		}
			$optionDefault['value'] = $val;

    		$optionDefault = array_merge($optionDefault, $options);
    		if(isset($options['class']))
    			$optionDefault['class'] = 'form-control bootstrap-daterangepicker pull-right '.$options['class'];
    	}
    
    	$html = '';
    	$html .= "<div class=\"form-group\">";
        
        if(!isset($options['label']) || $options['label']==true)
	         $html .= "<label>".__($fieldName)."</label>";

        $html .= "<div class=\"input-group\">
                  <div class=\"input-group-addon\">
                    <i class=\"fa fa-calendar\"></i>
                  </div>";
        $html .= $this->Form->control($fieldName, $optionDefault);
        $html .= "</div>
              </div>
              ";

        return $html;
    }

    public function checkbox($results, $editResults=[], $options=[], $debug=false) {    
        
        $html = '';
        foreach($results as $result) {

            /*
             * gestione valori gia' salvati per edit
             */
            $checked=''; 
            if(!empty($editResults))
            foreach($editResults as $numResult => $editResult) {
                $checked='';
                if($editResult->id==$result->id) {
                    $checked='checked';
                    unset($editResults[$numResult]);
                    break;
                }   
            }

            $html .= $this->Form->control('checkbox['.$result->id.']', 
                                        ['type' => 'checkbox', 
                                         'name' => 'checkbox.id['.$result->id.']', 
                                         'class' => 'checkbox-inline', 
                                         'inline' => 'checkbox',
                                        'value' => $result->id, 
                                        'label' => $result->name, 
                                        'hiddenField'=>false, 
                                        'checked' => $checked]);
        }

        return $html;
    }
}