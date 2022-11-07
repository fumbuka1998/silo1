<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 05/05/2019
 * Time: 09:51
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">JOURNAL VOUCHER</h2>
<br/>
<table width="40%">
    <tr>
        <td style="text-align: left; font-size: small">
            <span class="pull-left"><strong>Transaction Date: </strong></span>
        </td>
        <td style="text-align: right; font-size: small">
            <span class="pull-right"><?= custom_standard_date($jv->transaction_date) ?></span>
        </td>
    </tr>
    <tr>
        <td style="text-align: left; font-size: small">
            <span class="pull-left"><strong>Transaction No: </strong></span>
        </td>
        <td style="text-align: right; font-size: small">
            <span class="pull-right"><?= $jv->jv_number() ?></span>
        </td>
    </tr>
    <tr>
        <td style="text-align: left; font-size: small">
            <span class="pull-left"><strong>Reference: </strong></span>
        </td>
        <td style="text-align: right; font-size: small">
            <span class="pull-right"><?= $jv->reference ?></span>
        </td>
    </tr>
    <tr>
        <td style="text-align: left; font-size: small">
            <span class="pull-left"><strong>Currency: </strong></span>
        </td>
        <td style="text-align: right; font-size: small">
            <span class="pull-right"><?= $jv->currency()->name_and_symbol() ?></span>
        </td>
    </tr>
</table>
<br/>
<table style="font-size: 10px" width = "100%" border="1" cellspacing="0"  class="table  table-hover table-striped ">
    <thead>
    <tr>
        <th>Account</th><th style="width: 23%; text-align: right;">Dr</th><th style="width: 23%; text-align: right;">Cr</th>
    </tr>
    </thead>
    <tbody>
    <?php

    $jv_transactions = $jv->journal($jv->transaction_date, $jv->transaction_date);
    $total_credit_amnt = $total_debit_amnt = 0;
    foreach($jv_transactions as $jv_transaction){
        $total_credit_amnt += $jv_transaction->credited_amount;
        $total_debit_amnt += $jv_transaction->debited_amount;
        ?>
        <tr>
            <td><span><?= $jv_transaction->descriptions ?></span></td>
            <td style="text-align: right; width: 23%"><?= $jv_transaction->debited_amount > 0 ? $jv->currency()->symbol.' '.number_format($jv_transaction->debited_amount,2) : '' ?></td>
            <td style="text-align: right; width: 23%"><?= $jv_transaction->credited_amount > 0 ? $jv->currency()->symbol.' '.number_format($jv_transaction->credited_amount,2) : '' ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3">(<?= wordwrap($jv->remarks,140,'<br/>') ?>)</td>
    </tr>
    </tfoot>
</table>
<br/>
<?php $amount_in_words = $total_credit_amnt > $total_debit_amnt ? $total_credit_amnt : $total_debit_amnt ?>
<strong>Amount In Words: </strong><br/><?= numbers_to_words($amount_in_words) ?><br/><br/>
<table style="font-size: 12px"  width="100%" >
    <tr>
        <td  colspan="2" style="vertical-align: top; text-align: center">
            <strong>Issued By: </strong><br/><br/>
            <span style="text-decoration: underline">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span><br/>
            <?= $jv->created_by()->full_name() ?>
        </td>
    </tr>
</table>
