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
            <th>S/N</th><th>Item Name</th><th>Unit</th><th>Opening Stock</th><th>Received From Orders</th><th>Assigned In</th><th>Used</th><th>Assigned Out</th><th>Main Store Balance</th>
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
        foreach ($rows as $row){
            $sn++;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $row['item_name'] ?></td>
                <td><?= $row['unit'] ?></td>
                <td><?= $row['opening_stock'] ?></td>
                <td style="text-align: right"><?= $row['received_from_orders'] ?></td>
                <td style="text-align: right"><?= $row['assigned_in'] ?></td>
                <td style="text-align: right"><?= $row['used'] ?></td>
                <td style="text-align: right"><?= $row['assigned_out'] ?></td>
                <td style="text-align: right"><?= $row['main_store_balance'] ?></td>
                <?php
                    $total_balance = $row['main_store_balance'];
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
        }
    ?>
    </tbody>
</table>
