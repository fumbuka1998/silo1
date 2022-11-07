<?php

    $this->load->view('includes/mpdf_css');
    $this->load->view('includes/letterhead');
?>
<br><br>

<h2 style="text-align:center;">STOCK REPORT</h2>

<table style="font-size: 10px" width="100%" border="1" cellspacing="0">
    <thead>
        <tr style="background: #cdcdcd; color: #ed1c24; ">
            <th style="width: 5%">SN</th><th width="20%">Material/Item</th><th style="width: 10%">Unit</th>
             <th style="width: 10%">Quantity</th>
            </tr>
            
    </thead>
    <tbody>
    <?php

         $total_amount = 0;
         $sn = 0;

        foreach($stock_items as $stock_item){ 
            $sn++;
            $item = new self;
           // $item->load($stock_item->item_id);
            //$average_price = $item->sub_location_average_price($sub_location_id, 'ALL');

            $total_amount=$total_amount + ($stock_item->quantity_available*1000);

            ?>

            <tr>
                <td><?= $sn ?></td>
                <td><?= $stock_item->item_name ?></td>
                <td><?= $stock_item->symbol ?></td>
                <td style="text-align: right"><?= $stock_item->quantity_available ?></td>
                
            </tr>
            <?php
        }
    ?>
    </tbody>
    <tfoot>
        
    </tfoot>
</table>

