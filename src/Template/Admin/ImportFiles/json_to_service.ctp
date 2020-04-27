  <section class="content-header">
    <h1>
      Import File
      <small><?php echo __('JsonToService'); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo $this->Url->build(['action' => 'index']); ?>"><i class="fa fa-dashboard"></i> <?php echo __('Home'); ?></a></li>
    </ol>
  </section>


<?php
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

    $id = $supplier->suppliers_organizations[0]->id;

    echo '<section class="content">';
    echo '<div class="row">';
    echo '<div class="col-md-12">';
    echo $supplier->suppliers_organizations[0]->name;
    echo $supplier->indirizzo.' '.$supplier->localita;
    echo '</div>';
    echo '</div>';

    echo '<div class="row">';
    echo '<div class="col-md-12">';
    echo '<a class="btn btn-success" 
            href="'.$portalgas_bo_url.'/administrator/index.php?FilterArticleArticleTypeIds_hidden=&FilterArticleCategoryArticleId=&FilterArticleFlagPresenteArticlesorders=ALL&FilterArticleSupplierId='.$id.'&FilterArticleUm=&FilterArticleStato=ALL&FilterArticleName=&option=com_cake&controller=Articles&action=context_articles_index">';
    echo 'Visualizza listino articoli importati';
    echo '</a>';
    echo '</div>';
    echo '</div>';
    echo '</section>';    
}
?>