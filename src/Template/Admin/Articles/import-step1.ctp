<?php 
use Cake\Core\Configure; 
?>
<div class="col-md-12">
    <h3 class="box-title">Scegli il produttore</h3>
    <?php
    /* 
     * filtri di ricerca
     */
    echo '<div class="row">';
    echo '<div class="col-md-12">';
    $options = ['options' => $suppliersOrganizations,
                'escape' => false,
                'class' => 'select2- form-control',
                'label' => __('SupplierOrganization'),
                'empty' => Configure::read('HtmlOptionEmpty'),
                'v-model' => 'supplier_organization_id',
                '@change' => 'getSuppliersOrganization(event)'];
    echo $this->Form->control('supplier_organization_id', $options);
    echo '</div>';
    echo '</div>'; // row  

    echo '<template v-if="supplier_organization.name!=null">';    
        /* 
        * anagrafica produttore
        */
        echo '<div class="row" style="margin-bottom:15px;" 
                    v-if="supplier_organization.supplier.img1!=\'\'">';
        echo '  <div class="col-md-12">
            <img style="max-width:250px" class="img-responsive" v-bind:src="supplier_organization.img1" /></div>';
        echo '</div>'; 

        /* 
        * listino articoli NON gestito dal GAS
        */
        echo '<template v-if="supplier_organization.owner_articles!=\'REFERENT\'">';
        echo '<div class="row">';
        echo '<div class="col-md-12">';
        echo '<div class="alert alert-danger">Non puoi importare il listino del produttore {{supplier_organization.name}} perchè è gestito {{supplier_organization.owner_articles | ownerArticlesLabel}}'; 
        echo '</div>';
        echo '</div>'; // row  
        echo '</template>';    
    echo '</template>';    
    ?>  

    <button class="btn btn-primary nextBtn btn-lg pull-right" type="button"
            :disabled="!ok_step1">Avanti</button>
</div>