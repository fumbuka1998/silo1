<?php
/**
 * Created by PhpStorm.
 * User: stunna
 * Date: 28/11/2017
 * Time: 17:31
 */
?>
<table <?php if(isset($print)){
    ?>
    width="100%" border="1" cellspacing="0" style="font-size: 10px"
    <?php
} else { ?> class="table table-bordered table-hover"
<?php } ?>
>
    <thead>
        <tr>
            <th>S/N</th><th>Item Name</th><th>Unit</th><th>Opening Stock</th><th>Ordered</th><th>Received From GRNs</th><th>Assigned In</th><th>Sold</th><th>Used</th><th>Assigned Out</th><th>On Transit</th><th>Main Store Balance</th>
            <?php foreach ($site_sub_locations as $sub_location){
                ?>
                <th><?= 'Site Store/'.$sub_location ?></th>
            <?php
            } ?>
            <th>Total Balance</th>
            <th>Average Price</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $sn = 0;
    $total_price = 0;
    foreach ($rows as $row){
            $sn++;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $row['item_name'] ?></td>
                <td><?= $row['unit'] ?></td>
                <td><?= $row['opening_stock'] ?></td>
                <td><?= $row['ordered'] ?></td>
                <td style="text-align: right"><?= $row['received_from_orders'] ?></td>
                <td style="text-align: right"><?= $row['assigned_in'] ?></td>
                <td style="text-align: right"><?= $row['sold'] ?></td>
                <td style="text-align: right"><?= $row['used'] ?></td>
                <td style="text-align: right"><?= $row['assigned_out'] ?></td>
                <td style="text-align: right"><?= $row['on_transit'] ?></td>
                <td style="text-align: right"><?= $row['main_store_balance'] ?></td>
                <?php
                    $total_balance = $row['main_store_balance'] + $row['on_transit'];
                    foreach ($site_sub_locations as $sub_location_id => $sub_location_name){
                        $total_balance += $row['site_sub_location_balances'][$sub_location_id];
                        ?>
                        <td style="text-align: right"><?= $row['site_sub_location_balances'][$sub_location_id] ?></td>
                <?php
                    }
                ?>
                <td style="text-align: right"><?= $total_balance ?></td>
                <td style="text-align: right"><?= number_format($row['average_price'],2) ?></td>
            </tr>
    <?php

            $total_price += $total_balance * $row['average_price'];
        }
    ?>
    </tbody>
    <tfoot>
        <tr style="font-weight: bold">
            <td colspan="11">TOTAL MATERIAL BALANCE VALUE</td>
            <?php
            foreach ($site_sub_locations as $sub_location_id => $sub_location_name){
                ?>
                <td style="text-align: right"></td>
                <?php
            }
            ?>
            <td style="text-align: right"></td>
            <td style="text-align: right"><?= number_format($total_price,2) ?></td>
        </tr>
    </tfoot>
</table>
