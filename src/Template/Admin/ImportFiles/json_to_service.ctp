<?php
use Cake\Core\Configure;

echo '<section class="content-header">';
echo '<h1>';
echo __('Gdxp-JsonToService');
echo '<small>'.$url.'</small>';
echo '</h1>';
echo '<ol class="breadcrumb">';
echo '<li><a href="'.$this->Url->build(['controller' => 'gdxps', 'action' => 'index']).'"><i class="fa fa-dashboard"></i> '.__('Home').'</a></li>';
echo '</ol>';
echo '</section>';



/* 
* errors
*/
if(isset($errors)) {

    if($application_env='development') debug($errors);
    
    echo '<section class="content">';
    echo '<div class="row">';
    echo '<div class="col-md-12">';
    echo '<div class="box box-primary">';
    echo '<div class="box-body">';

    if(isset($errors['error']))
    foreach($errors['error'] as $field => $error) {
        echo __($field).' ';
        foreach($error as $err) {
            echo $err.' ';
        }
        echo '<br />';
    }
    
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</section>';
}

if(!empty($supplier)) {

    $config = Configure::read('Config');
    $portalgas_bo_url = $config['Portalgas.bo.url'];

    $name = '';
    if(isset($supplier->suppliers_organizations[0])) {
        $id = $supplier->suppliers_organizations[0]->id;
        $name = $supplier->suppliers_organizations[0]->name;
    }

    echo '<section class="content">';
    echo '<div class="row">';
    echo '<div class="col-md-8">';
    echo $name;
    echo ' ';
    echo $supplier->indirizzo.' '.$supplier->localita;
    echo '</div>';
    echo '<div class="col-md-4">';
    if(isset($id)) {
        echo '<a class="btn btn-info" 
                href="'.$portalgas_bo_url.'/administrator/index.php?FilterArticleArticleTypeIds_hidden=&FilterArticleCategoryArticleId=&FilterArticleFlagPresenteArticlesorders=ALL&FilterArticleSupplierId='.$id.'&FilterArticleUm=&FilterArticleStato=ALL&FilterArticleName=&option=com_cake&controller=Articles&action=context_articles_index">';
        echo 'Visualizza listino articoli importati';
        echo '</a>';        
    }
    echo '</div>';
    echo '</div>';
    echo '</section>';    
}
?>