<?php
namespace App\Traits;

use Cake\Core\Configure;

trait HelperTrait
{               
    public function title($title, $options=[]) {    
        return '
            <div class="box-header" style="background-color: #3c8dbc;color: #fff;margin: 10px 0;">
                    <h3 class="box-title">'.$title.'</h3>                  
            </div>
            ';
    }

    public function modal($id, $title, $body, $options=[]) {

        isset($options['size']) ? $size = $options['size']: $size = '';
    
        $html = '
            <div id="'.$id.'" class="modal fade" role="dialog">
            <div class="modal-dialog '.$size.'">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">'.$title.'</h4>
                    </div>
                    <div class="modal-body" style="font-size: 16px;">'.$body.'</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Chiudi</button>
                    </div>
                </div>
            </div>
        </div> <!-- modal --> ';

        return $html;
    }     
}