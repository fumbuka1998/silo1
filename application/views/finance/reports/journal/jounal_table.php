<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 4/4/2019
 * Time: 11:48 AM
 */

?>

<table  <?php if($print){ ?> style="font-size: 10px" width = "100%" border="1" cellspacing="0"  <?php } ?> class="table table-bordered table-hover table-striped">
    <thead>
    <tr>
        <th>S/N</th><th>Payment Date</th><th style="width: 40%">Descriptions</th><th>Dr</th><th>Cr</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sn = 0;
    foreach ($transactions as $transaction){
        $sn++;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= custom_standard_date($transaction['payment_date']) ?></td>
            <td style="width: 40%"><?= $transaction['descriptions'] ?></td>
            <td><?= $transaction['debited_amount'] ?></td>
            <td><?= $transaction['credited_amount'] ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
