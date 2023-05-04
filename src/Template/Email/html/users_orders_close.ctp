<?php 
use Cake\Core\Configure;
use Cake\I18n\Time;

if(count($orders)>0) {

    $delivery_id_old = 0;
    foreach($orders as $order) {

        if($delivery_id_old==0 || $delivery_id_old != $order->delivery->id) {

            if($order->delivery->sys=='Y')
                echo "<br />Per una consegna <b>".$order->delivery->luogo."</b><br />";
            else
                echo "<br />Per la consegna di <b>".$order->delivery->data->i18nFormat('eeee d MMMM')."</b> a ".$order->delivery->luogo.'<br />';

            $now = \Cake\I18n\Time::now();
            $now->addDays(Configure::read('GGMailToAlertOrderClose')+1);
            $data_oggi_incrementata = $now->i18nFormat('eeee d MMMM');

            echo "si <span style='color:red'>chiudera'</span> tra ".(Configure::read('GGMailToAlertOrderClose')+1)." giorni, ".$data_oggi_incrementata.", il periodo d'ordine nei confronti dei seguenti produttori<br /><br />";
        } // end if($delivery_id_old==0 || $delivery_id_old != $order->delivery->id) 
        
        echo "<div style='clear:both;float:none;margin-top:5px;'>";
        echo $order->suppliers_organization->name;
        if(!empty($order->suppliers_organization->supplier->descrizione)) echo "/".$order->suppliers_organization->supplier->descrizione;
        if(!empty($order->suppliers_organization->frequenza)) echo " (frequenza ".$order->suppliers_organization->frequenza.')';
        
        if(!empty($order->suppliers_organization->supplier->img1) && file_exists(env('PortalgasAppRoot').Configure::read('App.img.upload.content').'/'.$order->suppliers_organization->supplier->img1))
            echo ' <img width="50" src="'.env('PortalgasFeUrl').Configure::read('App.web.img.upload.content').'/'.$order->suppliers_organization->supplier->img1.'" alt="'.$order->suppliers_organization->name.'" /> ';
        else
            echo ' <img width="50" src="'.env('PortalgasFeUrl').Configure::read('App.web.img.upload.content').'/empty.png" alt="'.$order->suppliers_organization->name.'" /> ';

        echo "<br />";


        if(!empty($order->mail_open_testo)) {
                echo '<div style="float:right;width:75%;margin-top:5px;">';
                echo '<span style="color:red;">Nota</span> ';
                echo $order->mail_open_testo;
                echo '</div>';
        }
        echo '</div>';

        $delivery_id_old = $order->delivery->id;
    }

    $url = env('PortalgasFeUrl').'/home-'.$organization->j_seo.'/preview-carrello-'.$organization->j_seo.'?'.$order->urlCartPreviewNoUsername;
    echo '<div style="clear: both; float: none; margin: 15px 0 15px;">'; 
    echo '<img src="'.env('PortalgasFeUrl').Configure::read('App.img.cake').'/cesta-piena.png" title="" border="0" />';
    echo ' <a target="_blank" href="'.$url.'">Clicca qui per visualizzare i tuoi <b>acquisti</b> che dovrai ritirare durante la consegna</a>';
    echo '</div>';         
} // end if(count($orders)>0)