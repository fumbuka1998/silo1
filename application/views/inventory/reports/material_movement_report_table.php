<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 8/22/2017
 * Time: 3:48 PM
 */
?>

<table  <?php if($print){ ?> style="font-size: 11px" width="100%" border="1" cellspacing="0"  <?php } else { ?> class="table table-bordered table-hover" <?php } ?> >
    <thead>
        <tr>
            <th>SN</th><th>Item Name</th><th>UOM</th><th>Opening Balance</th><th>Received</th><th>Assigned Out</th><th>Sold</th><th>Disposed</th><th>Transferred Out</th><th>Used</th><th>Balance</th>
        </tr>
    </thead>
    <tbody>
<?php
    $sn = 0;
    foreach ($table_items as $item){
        if( $item['received'] != 0 ||  $item['transferred_out'] != 0 || $item['assigned_out'] != 0 ||  $item['used'] != 0 || $item['balance'] != 0 ) {
            $sn++;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $item['item_name'] ?></td>
                <td><?= $item['unit'] ?></td>
                <td style="text-align: right"><?= $item['opening_balance'] ?></td>
                <td style="text-align: right"><?= $item['received'] ?></td>
                <td style="text-align: right"><?= $item['assigned_out'] ?></td>
                <td style="text-align: right"><?= $item['sold'] ?></td>
                <td style="text-align: right"><?= $item['disposed'] ?></td>
                <td style="text-align: right"><?= $item['transferred_out'] ?></td>
                <td style="text-align: right"><?= $item['used'] ?></td>
                <td style="text-align: right"><?= $item['balance'] ?></td>
            </tr>
            <?php
        }
    }
?>
    </tbody>
</table>


