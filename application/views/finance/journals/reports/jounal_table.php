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
    <tr style="background-color: #9FAFD1">
        <th style="width: 5%">Transaction Date</th><th style="width: 8%">Transaction Type</th><th style="width: 8%">Reference</th><th style="width: 40%">Account Tittle And Explaination</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($journal_vouchers as $journal_voucher){
        $jv_transactions = $journal_voucher->journal($from, $to);

        ?>
        <tr>
            <td><?= custom_standard_date($journal_voucher->transaction_date) ?></td>
            <td><?= $journal_voucher->journal_type ?></td>
            <td><?= $print ? $journal_voucher->jv_number() : anchor(base_url('finance/preview_journal_voucher/'.$journal_voucher->{$journal_voucher::DB_TABLE_PK}),$journal_voucher->jv_number(), 'target="_blank"') ?></td>
            <td>
                <table <?php if($print){ ?> style="font-size: 10px" width = "100%" border="1" cellspacing="0"  <?php } ?>  class="table  table-hover table-striped ">
                    <thead>
                    <tr>
                        <th>Account</th><th style="width: 23%; text-align: right;">Dr</th><th style="width: 23%; text-align: right;">Cr</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_credit_amnt = $total_debit_amnt = 0;
                    foreach($jv_transactions as $jv_transaction){
                        $total_credit_amnt += $jv_transaction->credited_amount;
                        $total_debit_amnt += $jv_transaction->debited_amount;
                        ?>
                        <tr>
                            <td><?= $jv_transaction->descriptions ?></td>
                            <td style="text-align: right; width: 23%"><?= $jv_transaction->debited_amount > 0 ? $journal_voucher->currency()->symbol.' '.number_format($jv_transaction->debited_amount,2) : '' ?></td>
                            <td style="text-align: right; width: 23%"><?= $jv_transaction->credited_amount > 0 ? $journal_voucher->currency()->symbol.' '.number_format($jv_transaction->credited_amount,2) : '' ?></td>
                        </tr>
                        <?php
                    }

                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3">(<?= wordwrap($journal_voucher->remarks,140,'<br/>') ?>)</td>
                    </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
