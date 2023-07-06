<?php 
use Cake\Core\Configure;

$user = $this->Identity->get();
?>
<div class="box box-primary direct-chat direct-chat-primary">
    <div class="box-header with-border">
      <h3 class="box-title"><?php echo __('Search');?></h3>

      <div class="box-tools pull-right">
      <span data-toggle="tooltip" :title="'totale ordini '+articles.length" class="badge bg-light-blue">{{ articles.length }}</span>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

<?= $this->Form->create(null, ['id' => 'frmSearch', 'type' => 'GET']); ?>
<fieldset>
    <legend><?= __('Search {0}', ['Articles']) ?></legend>
    <?php
    echo '<div class="row-no-margin">';
    echo '<div class="col col-md-2">';
    $options = [];
    $options['ctrlDesACL'] = false;
    $options['id'] = 'search_supplier_organization_id'; // non c'e' il bind in supplierOrganization.js
    $options['default'] = $search_supplier_organization_id;
    $options['empty'] = true;
    $options['v-model'] = 'search_supplier_organization_id';
    echo $this->HtmlCustomSiteOrders->supplierOrganizations($suppliersOrganizations, $options);
    echo '</div>';
    echo '<div class="col-md-2 autocomplete">';
    echo $this->Form->control('search_name', ['label' => __('Name'), 'v-model' => 'search_name', 
                    '@input' => 'onChangeSearchAutoComplete(\'name\')', 
                    '@keydown.down' => 'onArrowDownSearchName',
                    '@keydown.up' => 'onArrowUpSearchName',
                    '@keydown.enter' => 'onEnterSearchName',
                    'autocomplete' => 'off',
                    'placeholder' => __('Name')]);
    echo '    <ul
    v-show="autocomplete_name_is_open"
    class="autocomplete-results">
    <li
        v-if="autocomplete_name_is_loading"
        class="loading"
    >
        Sto caricando...
    </li>    
    <li
      v-else  
      v-for="(result, i) in autocomplete_name_results"
      :key="i"
      @click="setSearchAutoCompleteResult(result, \'name\')"
      class="autocomplete-result"
      :class="{ \'is-active\': i === autocomplete_name_arrow_counter }"
    >
      {{ result }}
    </li>
  </ul>';
    echo '</div>';
    echo '<div class="col-md-2 autocomplete"> ';
    echo $this->Form->control('search_codice', ['label' => __('Code'), 'v-model' => 'search_codice', 
                    '@input' => 'onChangeSearchAutoComplete(\'codice\')', 
                    '@keydown.down' => 'onArrowDownSearchCodice',
                    '@keydown.up' => 'onArrowUpSearchCodice',
                    '@keydown.enter' => 'onEnterSearchCodice',
                    'autocomplete' => 'off',
                    'placeholder' => __('Code')]);
    echo '    <ul
    v-show="autocomplete_codice_is_open"
    class="autocomplete-results">
    <li
        v-if="autocomplete_codice_is_loading"
        class="loading"
    >
        Sto caricando...
    </li>    
    <li
      v-else  
      v-for="(result, i) in autocomplete_codice_results"
      :key="i"
      @click="setSearchAutoCompleteResult(result, \'codice\')"
      class="autocomplete-result"
      :class="{ \'is-active\': i === autocomplete_codice_arrow_counter }"
    >
      {{ result }}
    </li>
  </ul>';
    echo '</div>';
    echo '<div class="col-md-2">';
    echo $this->Form->control('search_categories_articles', ['label' => __('Categories'), 'v-model' => 'search_categories_articles', 'options' => $categories_articles, 'escape' => false, 'class' => 'form-control select2', 'empty' => Configure::read('HtmlOptionEmpty')]);
    echo '</div>';  
    echo '<div class="col-md-2">';
    echo '<br />'; 
    echo '<a class="btn-block btn" 
            :class="search_flag_presente_articlesorders ? \'btn-success\' : \'btn-danger\'" 
            :title="search_flag_presente_articlesorders ? \'Articolo ordinabile\' : \'Articolo NON ordinabile\'" 
            @click="toggleSearchFlagPresenteArticlesOrders()">
        <span v-if="search_flag_presente_articlesorders">Ordinabile</span>
        <span v-if="!search_flag_presente_articlesorders">Non ordinabile</span>
        </a>';  
    echo '</div>';
    echo '<div class="col col-md-2 text-right">';
    echo '<br />';
    echo '<button type="button" class="btn btn-primary pull-right" @click="gets()">'.__('Search').'</button>';
    echo '</div>';   
    echo '</div>';
    ?>
</fieldset>
</div>
    <!-- div class="box-footer">
        <?= $this->Form->button(__('Search'), ['class' => 'btn btn-primary pull-right']) ?>
    </div -->
</div>
<?php
echo $this->Form->end() 
?>
