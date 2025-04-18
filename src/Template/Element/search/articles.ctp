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

<?php
echo $this->Form->create(null, ['id' => 'frmSearch', 'type' => 'GET']); ?>
<fieldset>
    <legend><?= __('Search {0}', ['Articles']) ?></legend>
    <?php
    echo '<div class="row-no-margin">';
    echo '<div class="col col-md-4">';
    $options = [];
    $options['ctrlDesACL'] = false;
    $options['id'] = 'search_supplier_organization_id'; // non c'e' il bind in supplierOrganization.js
    $options['default'] = $search_supplier_organization_id;
    (count($suppliersOrganizations)==1) ? $options['empty'] = false: $options['empty'] = true;
    $options['v-model'] = 'search_supplier_organization_id';
    $options['@change'] = 'changeSearchSupplierOrganizationId';
    echo $this->HtmlCustomSiteOrders->supplierOrganizations($suppliersOrganizations, $options);
    echo '</div>';
    echo '<div class="col col-md-4 autocomplete">';
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
    echo '<div class="col col-md-4 autocomplete"> ';
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

    /*
     * categorie
     */
    if($user->organization->paramsFields['hasFieldArticleCategoryId']=='Y') {

        echo '<div class="col col-md-4">';
        // echo $this->Form->control('search_categories_articles', ['label' => __('Categories'), 'v-model' => 'search_categories_articles', 'options' => $categories_articles, 'escape' => false, 'class' => 'form-control select2', 'empty' => Configure::read('HtmlOptionEmpty')]);
        echo '<label for="search-categories-articles" class="control-label">Categorie</label>';

        echo '<div class="loader-global" v-if="is_run_categories_articles">
              <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
        echo </div>';
        echo '<select
              v-if="!is_run_categories_articles"
              name="search_categories_article_id"
              id="search-categories-article_id"
              class="form-control select2"
              :required="true"
              v-model="search_categories_article_id" >
              <option value="">-------</option>
              <option v-for="(categories_article, id) in search_categories_articles" :value="categories_article.id" v-html="$options.filters.html(categories_article.name)"></option>
            </select>';
        echo '</div>';
    } // if($user->organization->paramsFields['hasFieldArticleCategoryId']=='Y')

    echo '<div class="col col-md-3">';
    echo $this->Form->control('search_order', ['label' => __('Order'), 'v-model' => 'search_order', 'options' => $search_orders, 'escape' => false, 'class' => 'form-control']);
    echo '</div>';
    echo '<div class="col col-md-3">';
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
</div> <!-- box-body -->
    <!-- div class="box-footer">
        <?= $this->Form->button(__('Search'), ['class' => 'btn btn-primary pull-right']) ?>
    </div -->
</div>
<?php
echo $this->Form->end()
?>
