<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 3/2/2017
 * Time: 11:11 AM
 */

$opening_balance_date = date('Y-m-d',strtotime($from." -1 day"));
$balance = $account->balance($opening_balance_date);
?>
<table <?php if(!$export){ ?> class="table table-bordered table-hover"    <?php } else {
        ?> cellspacing="0" border="1" width="100%" style="font-size: 11px" <?php } ?>>
    <thead>
    <tr>
        <th>Date</th><th>Transaction Type</th><th>Reference</th><th>Supplementary Account</th><th>Description</th><th>Debit</th><th>Credit</th><th>Balance</th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= custom_standard_date($from) ?></td>
            <td colspan="3"></td>
            <td colspan="3">Previous Balance(Forwarded)</td>
            <td style="text-align: right"><?= number_format($balance) ?></td>
        </tr>
        <?php
        $total_credits = $total_debits = 0;
            foreach ($transactions AS $transaction){
                $total_credits += $transaction['credit'];
                $total_debits += $transaction['debit'];
                $credit = $transaction['credit'] > 0 ? number_format($transaction['credit']) : '';
                $debit = $transaction['debit'] > 0 ? number_format($transaction['debit']) : '';
                $balance = $account->account_nature() == 'debit' ? $balance + $transaction['debit'] - $transaction['credit'] : $balance + $transaction['credit'] - $transaction['debit'];
        ?>
                <tr>
                    <td><?= $transaction['transaction_date'] ?></td>
                    <td><?= $transaction['transaction_type'] ?></td>
                    <td><?= $transaction['reference'] ?></td>
                    <td><?= $transaction['supplementary_accounts'] ?></td>
                    <td><?= $transaction['description'] ?></td>
                    <td style="text-align: right"><?= $debit ?></td>
                    <td style="text-align: right"><?= $credit ?></td>
                    <td style="text-align: right"><?= number_format($balance) ?></td>
                </tr>
        <?php
            }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5" style="text-align: right">TOTAL</th>
            <th style="text-align: right"><?= number_format($total_debits) ?></th>
            <th style="text-align: right"><?= number_format($total_credits) ?></th>
            <th style="text-align: right"><?= number_format($balance) ?></th>
        </tr>
    </tfoot>
</table>
