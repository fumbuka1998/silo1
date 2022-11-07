<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 23/02/2018
 * Time: 15:15
 */
$total_amount = 0;
$currency = $order->currency();
if(!empty($invoices)){
    foreach ($invoices as $invoice){
        ?>
        <tr>
            <td><?= custom_standard_date($invoice->invoice_date) ?></td>
            <td><?= $invoice->reference ?></td>
            <td><?= $invoice->correspondence_number() ?></td>
            <td style="text-align: right"><?= $currency->symbol.' '. number_format($invoice->amount, 2) ?></td>
            <td><?= $invoice->description ?></td>
            <td>
                <?php if(($invoice->created_by == $this->session->userdata('employee_id')) || check_privilege('Make Payment')){ ?>
                <button type="button" invoice_id="<?= $invoice->{$invoice::DB_TABLE_PK} ?>" order_id="<?= $order->{$order::DB_TABLE_PK} ?>" class="btn btn-danger btn-xs delete_grn_invoice">
                    <i class="fa fa-close"></i>
                </button>
                <?php } ?>
            </td>
        </tr>
        <?php
        $total_amount += $invoice->amount;
    }

} else {
    ?>
    <tr>
        <td colspan="6">
            <div class="alert alert-info">No invoices found for order <?= $order->order_number() ?></div>
        </td>
    </tr>
<?php
}
?>

<tr>
    <th colspan="3">TOTAL</th><th style="text-align: right"><?=  $currency->symbol.' <span class="total_invoiced_amount">'. number_format($total_amount, 2).'</span>' ?></th><th colspan="2"></th>
</tr>

