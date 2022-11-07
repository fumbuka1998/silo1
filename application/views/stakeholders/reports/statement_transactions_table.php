<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 30/04/2018
 * Time: 16:06
 */
?>
    <table <?php if($print){ ?> style="font-size: 10px" width="100%" border="1" cellspacing="0"  <?php } ?>  class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Date</th><th>Transaction Type</th><th>Reference</th><th>Debit</th><th>Credit</th><th>Balance</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= custom_standard_date($from) ?></td>
                <td style="font-style: italic" colspan="4">Opening Balance</td>
                <td style="text-align: right"><?= accountancy_number($opening_balance) ?></td>
            </tr>
        <?php
        $balance = $opening_balance;
        foreach ($transactions as $transaction){
            $balance = $balance-$transaction->debit_amount+$transaction->credit_amount;
            ?>
            <tr>
                <td><?= custom_standard_date($transaction->transaction_date) ?></td>
                <td><?= ucwords($transaction->transaction_type) ?></td>
                <td><?= $transaction->reference ?></td>
                <td style="text-align: right"><?= $transaction->debit_amount != 0 ? number_format($transaction->debit_amount,2) : '' ?></td>
                <td style="text-align: right"><?= $transaction->credit_amount != 0 ? number_format($transaction->credit_amount,2) : '' ?></td>
                <td style="text-align: right"><?= accountancy_number($balance) ?></td>
            </tr>
        <?php
        };
        ?>
        </tbody>
    </table>

