<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 23/02/2018
 * Time: 15:15
 */
$total_amount = 0;
if(!empty($invoices)){
    foreach ($invoices as $invoice){
        $currency = $invoice->currency();
        $vendor = $invoice->stakeholder();
        ?>
        <tr>
            <td><?= custom_standard_date($invoice->invoice_date) ?></td>
            <td><?= $invoice->reference ?></td>
            <td><?= $vendor ? $vendor->stakeholder_name : '' ?></td>
            <td style="text-align: right"><?= $currency->symbol ?></td>
            <td style="text-align: right"><?= number_format($invoice->amount, 2) ?></td>
            <td><?= $invoice->description ?></td>
            <td>
                <?php if($invoice->created_by == $this->session->userdata('employee_id')){ ?>
                <button type="button" invoice_id="<?= $invoice->{$invoice::DB_TABLE_PK} ?>" class="btn btn-danger btn-xs delete_order_invoice">
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
        <td colspan="7">
            <div class="alert alert-info">No general invoices found for order <?= $order->order_number() ?></div>
        </td>
    </tr>
<?php
}
?>


