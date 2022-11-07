<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 19/02/2018
 * Time: 12:03
 */
?>

<table  <?php if($print){ ?> style="font-size: 10px" width="100%" border="1" cellspacing="0"  <?php } ?> class="table table-bordered table-hover">
    <thead>
        <tr>
            <?php if($report_category == "in_bulk"){ ?><th>S/N</th><?php } else { ?><th>Delivery Date</th><?php } ?><th>Item Name</th><th>Quantity</th><?php if($report_category != "in_bulk"){ ?><th>Price</th><th>Amount</th><th>For</th><?php } ?>
        </tr>
    </thead>
    <tbody>
    <?php
        $sn = 0;
        foreach ($items as $item){
            $sn++
            ?>
            <tr>
                <?php if($report_category == "in_bulk"){ ?>
                <td><?= $sn ?></td>
                <?php } else { ?>
                <td><?= $item['date_delivered'] ?></td>
                <?php } ?>
                <td><?= $item['item_name'] ?></td>
                <td nowrap style="text-align: right"><?= $item['quantity'].' '. $item['unit'] ?></td>
                <?php if($report_category != "in_bulk"){ ?>
                <td nowrap style="text-align: right"><?= $item['currency'].' '. number_format(($item['receiving_price']),2) ?></td>
                <td nowrap style="text-align: right"><?= $item['currency'].' '. number_format(($item['receiving_price']*$item['quantity']),2) ?></td>
                <td><?= $item['cost_center_name'] ?></td>
                <?php } ?>
            </tr>
    <?php
        }
    ?>
    </tbody>
</table>
