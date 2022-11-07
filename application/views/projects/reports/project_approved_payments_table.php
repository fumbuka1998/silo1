<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 9/17/2018
 * Time: 12:01 AM
 */


if(!empty($project_approved_payments)) {

    ?>

    <table <?php if($print){ ?> style="font-size: 10px" width="100%" border="1" cellspacing="0"  <?php } ?> class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Approval Date</th>
            <th>Payment Nature</th>
            <th>Reference</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $bg = '#efefef';
        $total_amount = 0;
        foreach ($project_approved_payments as $payment) {
            ?>
            <tr style="background: <?= $bg ?>">
                <td><?= $payment['approved_date'] ?></td>
                <td><?= $payment['payment_nature'] ?></td>
                <td><?= $payment['reference'] ?></td>
                <td style="text-align: right"><?= 'TSH '.number_format($payment['amount'],2) ?></td>
            </tr>
            <?php
            $total_amount += $payment['amount'];
            $bg = $bg == '#efefef' ? '#ffffff' : '#efefef';
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <td style="font-weight: bold" colspan="3">TOTAL</td>
                <td style="font-weight: bold; text-align: right"><?= number_format($total_amount,2)?></td>
            </tr>
        </tfoot>
    </table>
    <?php

}