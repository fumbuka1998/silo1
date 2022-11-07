<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 12/10/2018
 * Time: 11:58
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">CONTRA SHEET</h2>
<br/>
<table width="100%">
    <tr>
        <td width="50%">
            <h3><strong>Contra No: </strong><?= $contra->contra_number() ?></h3><br/>
            <strong>Credit Account:</strong> <?= $contra->credit_account()->account_name ?><br/>
            <strong>Contra Date: <?= custom_standard_date($contra->contra_date) ?></strong><br/><br/>
        </td>
        <td style="vertical-align: top;" width="40%">
            <?php if($imprest_voucher_contra = $contra->imprest_voucher_contra()){ ?>
                <strong>Reference: </strong><?= $contra->imprest_voucher_contra()->imprest_voucher()->requisition()->requisition_number().', '.$contra->reference ?><br/>
            <?php } else {?>
                <strong>Reference: </strong><?= $contra->reference ?><br/>
            <?php } ?>
        </td>
    </tr>
</table>
<br/>
<table style="font-size: 12px" border="1" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th>Debit Account</th><th>Description</th><th>Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $contra_items = $contra->contra_items();
    $total_amount = 0;
    foreach ($contra_items as $item){
        $total_amount += $item->amount;
        ?>
        <tr>
            <td><?= $item->debit_account()->account_name ?></td>
            <td><?= $item->description ?></td>
            <td style="text-align: right"><?= $contra->currency()->symbol .' '.number_format($item->amount,2) ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="2">TOTAL</th><th style="text-align: right"><?=  $contra->currency()->symbol .' '.number_format($total_amount,2) ?></th>
    </tr>
    </tfoot>
</table>
<br/>
<strong>Amount In Words: </strong><br/><?= numbers_to_words($total_amount) ?><br/><br/>
<strong>Remarks: </strong><br/><?= $contra->remarks != '' ? $contra->remarks : 'N/A'  ?><br/><br/>
<table width="100%">
    <tr>
        <td style="vertical-align: top"  width="33.3%">
            <strong>Issued By: </strong><br/><br/>
            <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span><br/>
            <?= $contra->employee()->full_name(); ?>
        </td>
        <td style="vertical-align: top"  width="33.3%">
            <strong>Received By: </strong><br/><br/>
            <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span><br/>
        </td>
    </tr>
</table>


