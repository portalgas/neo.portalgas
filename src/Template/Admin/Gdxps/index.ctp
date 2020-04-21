<?php
use Cake\Core\Configure;

echo $this->Html->script('vue/gdxpSuppliersIndex', ['block' => 'scriptPageInclude']);

echo $this->HtmlCustomSite->boxTitle(['title' => __('Gdxp-Suppliers-index'), 'subtitle' => 'economiasolidale.net']);

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
          <td><a class="btn btn-success" target="_blank" 
            v-bind:href="'/json/gdxp-articles-'+ supplier.vat +'.json'">Scarica listino in formato GDXP</a></td>
        </tr>
      </tbody>
    </table>
  <?php
echo '</div>'; // is_found_suppliers

echo '</div>';

$js = "var ajaxUrlGdxpSupplierIndex = '".$gdxp_suppliers_index_url."';";

$this->Html->scriptBlock($js, ['block' => true]);
?>