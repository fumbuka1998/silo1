<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 8/20/2018
 * Time: 2:06 PM
 */
?>
 <?php
    if(!empty($invoices)){
        ?>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Request Date</th><th>Reference</th><th>Request</th><th>Requested Amount</th><th>status</th><th></th>
                </tr>
                <?php
                    foreach($invoices as $invoice){
                ?>
                    <tr>
                        <td><?= set_date($invoice['request_date']) ?></td>
                        <td><?= $invoice['reference'] ?></td>
                        <td><?= $invoice['payment_request_no'] ?></td>
                        <td style="text-align: right"><?= $invoice['currency_symbol'].' '.number_format($invoice['amount'],2) ?></td>
                        <td><?= $invoice['status'] ?></td>
                        <td><a class="btn btn-default btn-xs" target="_blank"
                               href="<?= base_url('procurements/preview_purchase_order_payment_request/'.$invoice['payment_request_id']) ?>" role="button">
                                <i class="fa fa-file-pdf-o"></i>Request
                            </a>
                        </td>
                    </tr>
                <?php
                    }
                    ?>
                </thead>
        </table>
        <?php
    } else {
    ?>
        <div style="text-align: center" class="alert alert-info col-xs-12">
            No processed request for this order
        </div>
    <?php
    }
    ?>
