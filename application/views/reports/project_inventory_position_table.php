<?php
/**
 * Created by PhpStorm.
 * User: stunna
 * Date: 17/11/2017
 * Time: 12:13
 */
?>

<table <?php if(isset($print)){
?>
    width="100%" border="1" cellspacing="0"
    <?php
} else { ?> class="table table-bordered table-hover"
<?php } ?>
>
    <thead>
        <tr>
            <th>Description</th><th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Material Requested Value</td>
            <td style="text-align: right"><?= number_format($total_approved_amount,2) ?></td>
        </tr>
        <tr>
            <td>Goods Ordered Value</td>
            <td style="text-align: right"><?= number_format($order_amount, 2) ?></td>
        </tr>
        <tr>
            <td>Goods Received Value</td>
            <td style="text-align: right"><?= number_format($ordered_received_value, 2) ?></td>
        </tr>
        <tr>
            <td>Site Goods Received Value</td>
            <td style="text-align: right"><?= number_format($site_goods_received_value, 2) ?></td>
        </tr>
        <tr>
            <td>Used/Installed Material Value</td>
            <td style="text-align: right"><?= number_format($material_used_value,2) ?></td>
        </tr>
        <tr>
            <td>Material On/Off Site Value</td>
            <td style="text-align: right"><?= number_format($material_balance_value, 2) ?></td>
        </tr>
        <tr>
            <td>On Site Material Balance Value</td>
            <td style="text-align: right"><?= number_format($site_material_balance_value, 2) ?></td>
        </tr>
    </tbody>
</table>
