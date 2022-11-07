<table
  <?php if ($print_sub_sheet) {
                    ?> width="100%" border="1" cellspacing="0"

                    <?php
                } else {
                    ?>
                    class="table table-bordered table-hover table-striped"
                    <?php
                } ?>
                style="font-size: 12px" >
    <thead>
    <tr>
        <th>Approved_date</th>
        <th>Request Number</th>
        <th>Approved Amount</th>
        <th>PV Number</th>
        <th>Paid Amount</th>
    </tr>
    <tbody>
    <?php
    $this->load->model('currency');
    foreach ($cost_center_approved_payments as $payment) {
        if($payment->approved_amount > 0) {
            $rate = new Currency();
            $rate->load($payment->currency_id);
            ?>
            <tr>
                <td style="text-align: left"><?= set_date($payment->approved_date) ?></td>
                <td style="text-align: left">
                    <?php
                    if ($print_sub_sheet) {
                        if ($payment->nature == "requisition") {
                            ?>
                            <?= 'RQ/' . add_leading_zeros($payment->requisition_id) ?>
                            <?php
                        } else {
                            ?>
                            <?= 'PO-PR/' . add_leading_zeros($payment->requisition_id) ?>
                            <?php
                        }
                    } else {
                        if ($payment->nature == "requisition") {
                            ?>
                            <a target="_blank"
                               href="<?= base_url('requisitions/preview_requisition/' . $payment->requisition_id) ?>"><?= 'RQ/' . add_leading_zeros($payment->requisition_id) ?></a>
                            <?php
                        } else {
                            ?>
                            <a target="_blank"
                               href="<?= base_url('procurements/preview_approved_purchase_order_payments/' . $payment->requisition_approval_id) ?>"><?= 'PO-PR/' . add_leading_zeros($payment->requisition_id) ?></a>
                            <?php
                        }
                    }
                    ?>
                </td>
                <td style="text-align: right"><?= $rate->symbol . ' ' . number_format($payment->approved_amount, 2) ?></td>
                <td style="text-align: left">
                    <?php
                    if ($print_sub_sheet) {
                        if ($payment->payment_voucher_id) {
                            ?>
                            <?= 'PV/' . add_leading_zeros($payment->payment_voucher_id) ?>
                            <?php
                        } else { ?>

                        <?php }
                    } else {
                        if ($payment->payment_voucher_id) {
                            ?>
                            <a target="_blank"
                               href="<?= base_url('finance/preview_payment_voucher/' . $payment->payment_voucher_id) ?>"><?= 'PV/' . add_leading_zeros($payment->payment_voucher_id) ?></a>
                            <?php
                        } else { ?>

                        <?php }

                    }
                    ?>
                </td>
                <td style="text-align: right"><?= $rate->symbol . ' ' . number_format($payment->paid_amount, 2) ?></td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="2">TOTAL in Base Currency</th>
        <th style="text-align: right"><?= 'TSH ' . number_format($total_approved_requests, 2) ?></th>
        <th></th>
        <th style="text-align: right"><?= 'TSH ' . number_format($total_paid_requests, 2) ?></th>
    </tr>
    </tfoot>
</table>
<?php
