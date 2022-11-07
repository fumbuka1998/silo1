<?php
/**
 * Created by PhpStorm.
 * User: miralearn
 * Date: 02/11/2018
 * Time: 15:27
 */
?>
<table
    <?php if(isset($triggered)){ ?>
        style="font-size: 11px" width="100%" border="1" cellspacing="0"
    <?php } else { ?>
        class="table table-bordered table-hover" <?php }
        ?> >
    <thead>
        <tr>
            <th>SN</th><th>Vendor Name</th><th>Amount Supplied</th><th>Delivered Amount</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $sn = 0;
    foreach ($table_items as $item){
            $sn++;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $item['vendor_name'] ?></td>
                <td style="text-align: right"><?= number_format($item['supplied_amount'],2) ?></td>
                <td style="text-align: right"><?= number_format($item['delivered_amount'],2) ?></td>
            </tr>
    <?php
    }
    ?>
    </tbody>
</table>
