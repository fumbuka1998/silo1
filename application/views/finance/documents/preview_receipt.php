<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/25/2016
 * Time: 6:40 PM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center"> RECEIPT</h2>
<br/>
<table width="100%">
    <tr>
        <td width="50%">
            <h4><strong>Receipt No: </strong><?= $receipt->receipt_number() ?></h4><br/>
        </td>
        <td style="vertical-align: top;" width="40%">
            <strong>Receipt Date: </strong><?=custom_standard_date($receipt->receipt_date) ?><br/>
        </td>
    </tr>
    <tr>
        <td width="50%">
            <h4><strong>Currency: </strong><?= $receipt->currency()->name_and_symbol() ?></h4><br/>
        </td>
        <td style="vertical-align: top;" width="40%">
        </td>
    </tr>
</table>
<br/>
<table style="font-size: 12px" border="1" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th>Description</th><th>Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php $items = $receipt->items();
        $total_amount = 0;
        foreach ($items as $item){
            $total_amount += $item->amount;
            ?>
            <tr>
                <td><?= $item->remarks ?></td>
                <td style="text-align: right"><?= $receipt->currency()->symbol.' '.number_format($item->amount,2) ?></td>
            </tr>
    <?php
        }
    ?>


    </tbody>
    <tfoot>
    <tr>
        <th style="text-align: right">TOTAL</th><th style="text-align: right"><?=  $receipt->currency()->symbol.' '.number_format($total_amount,2) ?></th>
    </tr>
    </tfoot>
</table>
<br/>
<strong>Amount In Words: </strong><?= numbers_to_words($total_amount) ?><br/><br/><br/>
<strong>Remarks: </strong><?=$receipt->remarks?><br/><br/><br/>
<table width="100%">
    <tr>
        <td width="33.3%">
            <strong>Issued By: </strong><br/><br/>
            <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span><br/>
            <?= $receipt->employee()->full_name(); ?>
        </td>
        <td width="33.3%">
            <strong>Received By: </strong><br/><br/>
            <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span><br/>
        </td>
    </tr>
</table>


