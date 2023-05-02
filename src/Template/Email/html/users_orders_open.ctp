<?php 
use Cake\Core\Configure;

foreach($orders as $order) {

    if($order->delivery->sys=='Y')
        echo "Per una consegna <b>".$order->delivery->luogo."</b><br />";
    else
        echo "Per la consegna di <b>".$order->delivery->data->i18nFormat('eeee d MMMM')."</b> a ".$order->delivery->luogo.'<br />';

    //if(count($orderResults)==1) {
        echo "si <span style='color:green;'>apre</span> oggi il periodo d'ordine nei confronti del seguente produttore:<br />";
        // $subject_mail = $order->suppliers_organization->name'].", ordine che si apre oggi";
    // echo "si <span style='color:green;'>apre</span> oggi il periodo d'ordine nei confronti dei seguenti produttori: ";
    echo "<div style='clear:both;float:none;margin-top:5px;'>";
    echo $order->suppliers_organization->name;
    if(!empty($order->suppliers_organization->supplier->descrizione)) echo "/".$order->suppliers_organization->supplier->descrizione;
    if(!empty($order->suppliers_organization->frequenza)) echo " (frequenza ".$order->suppliers_organization->frequenza.')';
    echo " fino a ".$order->data_fine->i18nFormat('eeee d MMMM');

    if(!empty($order->suppliers_organization->supplier->img1) && file_exists(Configure::read('App.root').Configure::read('App.img.upload.content').'/'.$order->suppliers_organization->supplier->img1))
        echo ' <img width="50" src="https://www.portalgas.it'.Configure::read('App.web.img.upload.content').'/'.$order->suppliers_organization->supplier->img1.'" alt="'.$order->suppliers_organization->name.'" /> ';
    else
        echo ' <img width="50" src="https://www.portalgas.it'.Configure::read('App.web.img.upload.content').'/empty.png" alt="'.$order->suppliers_organization->name.'" /> ';										



    if(!empty($order->mail_open_testo)) {
            echo '<div style="float:right;width:75%;margin-top:5px;">';
            echo '<span style="color:red;">Nota</span> ';
            echo $$order->mail_open_testo->mail_open_testo;
            echo '</div>';
    }
    echo '</div>';
}