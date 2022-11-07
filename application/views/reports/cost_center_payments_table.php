<?php
if(!empty($cost_center_payments)){
?>

    <table
        <?php if($print){
            ?> width="100%" border="1" cellspacing="0"
            style="font-size: 10px"
            <?php
        } else {
            ?>
            class="table table-bordered table-hover table-striped"
            <?php
        } ?>>
        <thead>
        <tr>
            <th>S/N</th><th>Date</th><?php if(!$cost_center){ ?><th>Cost Center</th><?php } ?><th>PV No.</th><th>Paid Amount</th><th>Paid Amount(TSH)</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $row = '';
        $total_amount = $sn = 0;
        foreach($cost_center_payments as $cost_center_payment) {
            $sn++;
            $total_amount += $amount_in_base_currency =  $cost_center_payment->paid_amount * $cost_center_payment->exchange_rate;
            ?>
            <tr><td><?= $sn ?></td>
                <td><?= custom_standard_date($cost_center_payment->payment_date) ?></td>
                <?php if(!$cost_center){ ?><td><?= $cost_center_payment->cost_center_name ?></td><?php } ?>
                <td><?= $print ? add_leading_zeros($cost_center_payment->payment_voucher_id) : anchor(base_url('finance/preview_payment_voucher/'.$cost_center_payment->payment_voucher_id),add_leading_zeros($cost_center_payment->payment_voucher_id),'target="_blank" ')  ?></td>
                <td style="text-align: right"><?= $cost_center_payment->currency_symbol.' '.number_format($cost_center_payment->paid_amount,2)?></td>
                <td style="text-align: right"><?= 'TSH '.number_format($amount_in_base_currency,2)?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <?php if(!$cost_center){ ?>
                <td colspan="5" style="text-align: left; font-weight: bold">TOTAL IN BASE CURRENCY</td>
            <?php } else { ?>
                <td colspan="4" style="text-align: left; font-weight: bold">TOTAL IN BASE CURRENCY</td>
            <?php } ?>
            <td style="text-align: right; font-weight: bold"><?= 'TSH  '.number_format($total_amount,2) ?></td>
        </tr>
        </tfoot>
    </table>
<?php } else {
    ?>
    <div style="text-align: center; height: 50px; border-radius: 8px;" class="info alert-info col-xs-12">
        No payment(s) for this Cost Center currently
    </div>
    <?php
} ?>