<?php
namespace App\Traits;

use Cake\Core\Configure;

trait UtilTrait
{
    public function stringStartsWith($string, $search) {
        return (strncmp($string, $search, strlen($search)) == 0);
    }

    public function stringEndsWith($string, $search) {
        return (substr($string, - strlen($search)) === $search);
    }

    /*
     * data una string ripete a sn $str_to_repeat 
     */
    public function strRepeat($string, $num_repeat, $str_to_repeat) {
        $string = str_repeat($str_to_repeat, $num_repeat).$string;
        $string = substr($string, strlen($string)-$num_repeat, strlen($string));
        return $string;
    }

    /*
     * data una string elimina a sn $str_to_delete 
     */
    public function strDelete($string, $str_to_delete) {
        // debug('strDelete() string '.$string);
        $string_new = '';
        $strings = str_split($string);
        foreach ($strings as $numResult => $char) {
            // debug('strDelete() char '.$char);
            if($char!=$str_to_delete) {
                $string_new = substr($string, $numResult);
                break;
            }
        }
        // debug('strDelete() string_new '.$string_new);
        return $string_new;
    }

    public function getFileExtension($file_name) {

        if(empty($file_name))
            return '';

        if(strpos($file_name, '.')===false)
            return '';

        $ext = substr($file_name, strpos($file_name, '.')+1, strlen($file_name));

        return $ext;
    }

    public function setFlashError($errors, $debug = false) {

        if ($debug) {
            debug($errors);
        }

        $msg = "";
        if(!is_array($errors)) {
            $msg = $errors;
        }
        else {
            foreach ($errors as $field => $error) 
                foreach ($error as $value) {
                    $msg .= "Campo " . __($field) . ": " . __($value) . " \r\n";
                    // $msg .= $value."<br />";
                }

        }

        // $this->Flash->error(__('MsgDataSavedKO'));
        $this->Flash->error($msg, ['escape' => false]);
    }
}