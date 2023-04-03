<?php 
use Cake\Core\Configure;

// debug($results);
?>
<div class="related">
    <?php 
    foreach($results as $header) {

        echo '<h1>'.$header['gas_group']['name'].'</h1>';
        echo '<h2>'.$header['delivery']['luogo'].'</h2>';

        $article_orders = $header['article_orders'];

        // e sempre un articolo
        if(!empty($article_orders))
        foreach($article_orders as $result) {
        ?>    
        <div class="table-responsive">
        <table class="table table-hover">
            <tbody>
                <tr>
                    <th style="height:10px;width:30px;" rowspan="2">Nr</th>
                    <th style="height:10px;" rowspan="2">Gasista</th>
                    <th style="text-align:center;width:50px;height:10px;border-bottom:none;border-left:1px solid #CCCCCC;">Quantità</th>
                    <th style="text-align:center;width:100px;height:10px;border-bottom:none;border-right:1px solid #CCCCCC;">Importo</th>
                    <th colspan="2" style="text-align:center;width:150px;height:10px;border-bottom:none;">Quantità e importi totali</th>
                    <th style="height: 10px;" rowspan="2">Importo</th>
                    <th style="height: 10px;" rowspan="2">Stato</th>
                    <th style="height: 10px;" rowspan="2">Acquistato il</th>
                </tr>	
                <tr>
                    <th style="text-align:center;height:10px;border-left:1px solid #CCCCCC;border-right:1px solid #CCCCCC;" colspan="2">dell'utente</th>
                    <th style="text-align:center;height:10px;border-right:1px solid #CCCCCC;" colspan="2">modificati dal referente</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $tot_qta = 0;
                $tot_importo = 0;
                foreach($result['carts'] as $numResult => $cart) {
                ?>
                <tr>
                    <td><?php echo ($numResult+1);?></td>
                    <td><?php echo $cart['user']['name'];?></td>
                    <td style="text-align:center;"><?php echo $cart['qta'];?></td>
                    <td style="text-align:center;"><?php echo $this->Number->currency($result['prezzo']);?></td>
                    <td style="text-align:center;"><?php echo (empty($cart['qta_forzato'])) ? '-': $cart['qta_forzato'];?></td>
                    <td style="text-align:center;"><?php echo (empty($cart['importo_forzato'])) ? '-': $cart['importo_forzato'];?></td>
                    <td><?php echo $this->Number->currency($cart['final_price']);?></td>
                    <td title="<?php echo $result['stato_human'];?>" class="stato_<?php echo strtolower($result['stato']);?>"></td>
                    <td style="white-space: nowrap;"><?php echo $cart['date_human'];?></td>
                </tr>
                <?php 
                    $tot_qta += $cart['final_qta'];
                    $tot_importo += $cart['final_price'];            
                }
                ?>            
            </tbody>
            </tfoot>
            <tr>
                <th></th>
                <th></th>
                <th style="text-align:center;"><?php echo $tot_qta;?></th>
                <th></th>
                <th></th>
                <th></th>
                <th><?php echo $this->Number->currency($tot_importo);?></th>
                <th></th>
                <th></th>
            </tr>
            </tfoot>
        </table>
        </div>
        <?php 
        } // foreach($results as $result)

    } // end foreach($headers as $header) 
    ?>       
</div>