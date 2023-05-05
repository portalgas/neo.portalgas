<?php 
use Cake\Core\Configure;
use Cake\I18n\Time;

echo "Il giorno ".$delivery->data->i18nFormat('eeee d MMMM')." ci sar&agrave; la consegna a ".$delivery->luogo.", dalle ore ".substr($delivery->orario_da, 0, 5)." alle ".substr($delivery->orario_a, 0, 5);
if (!empty($delivery->nota)) {
    $body_mail .= '<div style="float:right;width:75%;margin-top:5px;">';
    $body_mail .= '<span style="color:red;">Nota</span> ';
    $body_mail .= $delivery->nota;
    $body_mail .= '</div>';
}

if(count($delivery->orders)==1)
    echo '<h3>Produttore presente alla consegna</h3>';
else 
    echo '<h3>Elenco dei produttori presenti alla consegna</h3>';

foreach($delivery->orders as $numResult => $order) {
    echo '<br />' . ($numResult + 1) . ') ';

    if(!empty($order->suppliers_organization->supplier->img1) && file_exists(env('PortalgasAppRoot').Configure::read('App.img.upload.content').'/'.$order->suppliers_organization->supplier->img1))
        echo ' <img width="50" src="'.env('PortalgasFeUrl').Configure::read('App.web.img.upload.content').'/'.$order->suppliers_organization->supplier->img1.'" alt="'.$order->suppliers_organization->name.'" /> ';
    else
        echo ' <img width="50" src="'.env('PortalgasFeUrl').Configure::read('App.web.img.upload.content').'/empty.png" alt="'.$order->suppliers_organization->name.'" /> ';

    echo $order->suppliers_organization->name;
    if (!empty($order->suppliers_organization->supplier->descrizione))
        echo ' (' . $order->suppliers_organization->supplier->descrizione . ')';
    if (!empty($order->suppliers_organization->frequenza))
        echo ' Frequenza ' . $order->suppliers_organization->frequenza;



}

$url = env('PortalgasFeUrl').'/home-'.$organization->j_seo.'/preview-carrello-'.$organization->j_seo.'?'.$order->urlCartPreviewNoUsername;
echo '<div style="clear: both; float: none; margin: 15px 0 15px;">'; 
echo '<img src="'.env('PortalgasFeUrl').Configure::read('App.img.cake').'/cesta-piena.png" title="" border="0" />';
echo ' <a target="_blank" href="'.$url.'">Clicca qui per visualizzare i tuoi <b>acquisti</b> che dovrai ritirare durante la consegna</a>';
echo '</div>';         
