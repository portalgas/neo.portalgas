<?php
use Cake\Core\Configure;

echo $this->Html->script('vue/gdxpSuppliersIndex', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Gdxp-Suppliers-index'), 'subtitle' => 'economiasolidale.net']);

echo $this->element('msg', ['msg' => 'Dati ottenuti interrogando il servizio <a target="_blank" href="'.$gdxp_suppliers_index_url_remote.'">'.$gdxp_suppliers_index_url_remote.'</a>']);

echo '<div id="vue-suppliers">';

echo '<div v-show="is_found_suppliers === false" style="display: none;text-align: center;" class="run run-suppliers"><div class="spinner"></div></div>';
echo '<div v-show="is_found_suppliers === true" style="display:none;">';
?>
    <table class="table table-hover">
      <thead class="thead-light">
        <tr>
          <th><?php echo __('Supplier-Name');?></th>
          <th><?php echo __('P Iva');?></th>
          <th>Last change</th>
          <th>Listino</th>
          <th>Importa</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="supplier in suppliers"
          :supplier="supplier.id"
          :key="supplier.id"
        >
          <td>{{ supplier.name }}</td>
          <td>{{ supplier.vat }}</td>
          <td>{{ supplier.lastchange | formatDate }}</td>
          <td><a class="btn btn-info" target="_blank" 
            v-bind:href="'<?php echo Configure::read('Gdxp.articles.index.url');?>/'+ supplier.vat">Visualizza listino in formato GDXP</a></td>
          <td><a class="btn btn-info" 
            v-bind:href="'/admin/import-files/jsonToService?q=vat&w='+ supplier.vat">Importa listino in formato GDXP</a></td>
        </tr>
      </tbody>
    </table>
  <?php
echo '</div>'; // is_found_suppliers

echo '</div>';

$js = "var ajaxUrlRemoteGdxpSupplierIndex = '".$gdxp_suppliers_index_url_remote."';
       var ajaxUrlLocalGdxpSupplierIndex = '".$gdxp_suppliers_index_url_local."';
";

$this->Html->scriptBlock($js, ['block' => true]);
?>