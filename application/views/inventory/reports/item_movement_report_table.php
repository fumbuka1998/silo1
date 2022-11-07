<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 8/30/2017
 * Time: 1:47 PM
 */
?>

<table
    <?php if($print){
?> width="100%" border="1" cellspacing="0"
        style="font-size: 11px"
    <?php
    } else {
?>
        class="table table-bordered table-hover"
    <?php
    } ?>
>
    <thead>
        <tr>
            <th>Date</th><th>Document Type</th><th>Reference</th><th>In</th><th>Out</th><th>Balance</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= custom_standard_date($from) ?></td>
            <td colspan="4"><i>Opening Balance</i></td>
            <td style="text-align: right"><?= number_format($opening_balance) ?></td>
        </tr>
    <?php
        $balance = $opening_balance;
        foreach ($transactions as $transaction){
            $balance += $transaction->qty_in - $transaction->qty_out;
            if($transaction->document_type == 'GRN'){
                $reference = 'GRN/'.add_leading_zeros($transaction->reference);
            } else if($transaction->document_type == 'DELIVERY'){
                $reference = 'DN/'.add_leading_zeros($transaction->reference);
            } else if($transaction->document_type == 'EXT. TRANSFER'){
                $reference = 'EXT/'.add_leading_zeros($transaction->reference);
            } else if($transaction->document_type == 'INT. TRANSFER'){
                $reference = 'INT/'.add_leading_zeros($transaction->reference);
            } else if($transaction->document_type == 'COST ASSIGNMENT'){
                $reference = 'MCA/'.add_leading_zeros($transaction->reference);
            } else if($transaction->document_type == 'STOCK SALE'){
                $reference = 'SALE/'.add_leading_zeros($transaction->reference);
            } else {
                $reference = $transaction->reference;
            }
            ?>
            <tr>
                <td><?= custom_standard_date($transaction->transaction_date) ?></td>
                <td><?= $transaction->document_type ?></td>
                <td><?= $reference ?></td>
                <td style="text-align: right"><?= $transaction->qty_in ?></td>
                <td style="text-align: right"><?= $transaction->qty_out ?></td>
                <td style="text-align: right"><?= $balance ?></td>
            </tr>
        <?php
        }
    ?>
    </tbody>

</table>
