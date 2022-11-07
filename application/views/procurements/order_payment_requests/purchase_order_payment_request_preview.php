<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/25/2018
 * Time: 7:13 PM
 */

$this->load->view('includes/letterhead');
$order = $payment_request->purchase_order()

?>

<h2 style="text-align: center">P.O PAYMENT REQUEST</h2>
<table style="font-size: 12px" width="100%">

    <tr>
        <td>
            <b>Request No. : </b><?= $payment_request->request_number() ?>
        </td>
        <td>
            <b>P.O No. : </b><?= $order->order_number() ?>
        </td>
    </tr>
    <tr>
        <td>
            <b>Requested For : </b><?= $payment_request->cost_center_name() ?>
        </td>
    </tr>
    <tr>
        <td>
            <b>Request Date : </b><?= custom_standard_date($payment_request->request_date) ?>
        </td>
        <td>
            <b>Currency : </b><?= $payment_request->currency()->name_and_symbol() ?>
        </td>
    </tr>
</table>

<br/><br/>
<table style="font-size: 12px" width="100%" cellspacing="0" border="1">
    <thead>
    <tr>
        <th>No.</th>
        <th>Description</th>
        <th>Claimed By</th>
        <th>Reference</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php
        $invoice_items = $payment_request->invoice_items();
        $total_amount = $sn = 0;

        foreach ($invoice_items as $invoice_item){
            $sn++;
            $total_amount += $invoice_item->requested_amount;

            $invoice = $invoice_item->invoice();
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $invoice_item->description ?></td>
                <td><?= $invoice_item->invoice()->stakeholder()->stakeholder_name ?></td>
                <td><?= $invoice->reference ?></td>
                <td style="text-align: right"><?= $invoice_item->purchase_order_payment_request()->currency()->symbol.' '.number_format($invoice_item->requested_amount,2) ?></td>
            </tr>
    <?php
    }
        $cash_items = $payment_request->cash_items();
        foreach ($cash_items as $cash_item){
            $sn++;
            $total_amount += $cash_item->requested_amount;
        ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $cash_item->description ?></td>
                <td><?= $cash_item->claimed_by ?></td>
                <td><?= $cash_item->reference ?></td>
                <td style="text-align: right"><?= $payment_request->currency()->symbol.' '.number_format($cash_item->requested_amount,2) ?></td>
            </tr>
    <?php } ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="4">TOTAL</th><th style="text-align: right"><?= $payment_request->currency()->symbol.'&nbsp;'. number_format($total_amount,2) ?></th>
    </tr>
    </tfoot>
</table>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>

        <td style="width: 25%">
            <strong>Requested By: </strong><br/><?= $payment_request->employee()->full_name() ?>
        </td>
        <td>
            <strong>Request Date: </strong><br/><?= custom_standard_date($payment_request->request_date) ?>
        </td>
        <td  style=" width:25%; vertical-align: top">
            <strong>Comments: </strong><br/><?= nl2br($payment_request->remarks) ?>
        </td>
    </tr>

    <?php

    foreach ($chain_levels as $chain_level) {
        $has_approval = isset($requisition_approvals[$chain_level->{$chain_level::DB_TABLE_PK}]);
        $approval = $has_approval ? $payment_request[$chain_level->{$chain_level::DB_TABLE_PK}] : null; ?>

        <?php
        if ($has_approval) { ?>
            <tr>
                <td colspan="3">
                    <hr />
                </td>
            </tr>
            <tr>
                <td style=" width:25%; vertical-align: top">
                    <strong><?= $chain_level->label ?> By: </strong><br />
                    <i><?= $approval->created_by()->full_name() ?></i>
                    <br /><?= $chain_level->level_name ?>
                </td>
                <td style=" width:25%; vertical-align: top">
                    <strong>Date: </strong><br />
                    <?= custom_standard_date($approval->approved_date) ?>
                </td>
                <td style=" width:50%; vertical-align: top">
                    <strong>Comments</strong><br />
                    <?= nl2br($approval->approving_comments) ?>
                </td>
            </tr>
        <?php } else {
            //Yohana said we remove this no stronger reason thou
            if ($chain_level->status == 'NOMORE') {
                ?>
                <tr>
                    <td colspan="3">
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td style=" width:25%; vertical-align: top">
                        <strong><?= $chain_level->label ?> By: </strong><br />
                        <br />
                        <span style="text-decoration: underline">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </span>
                        <br /><?= $chain_level->level_name ?>
                    </td>
                    <td style=" width:25%; vertical-align: top">
                        <strong>Date: </strong><br />
                        <br />
                        <span style="text-decoration: underline">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </span>
                    </td>
                    <td style=" width:50%; vertical-align: top">
                        <strong>Comments</strong><br />
                        <br />
                        <span style="text-decoration: underline">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </span>
                    </td>
                </tr>
            <?php }
        }
    }

    if(1<0){
        foreach($chain_levels as $chain_level){ ?>
        <tr>
            <td colspan="3"><hr/></td>
        </tr>
        <tr>
            <td style=" width:25%; vertical-align: top">
                <strong><?= $chain_level->label ?> By: </strong><br/>
                <?php
                $has_approval = isset($payment_request_approvals[$chain_level->{$chain_level::DB_TABLE_PK}]);
                $approval = $has_approval ?  $payment_request_approvals[$chain_level->{$chain_level::DB_TABLE_PK}] : null;
                if($has_approval){
                    echo '<i>'.$approval->employee()->full_name().'</i>';
                } else { ?>
                    <br/>
                    <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
                <?php } ?>
                <br/><?= $chain_level->level_name ?>
            </td>
            <td style=" width:25%; vertical-align: top">
                <strong>Date: </strong><br/>
                <?php if($has_approval){
                    echo custom_standard_date($approval->approval_date);
                } else { ?>
                    <br/>
                    <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
                <?php } ?>
            </td>
            <td style=" width:25%; vertical-align: top">
                <strong>Comments: </strong><br/>
                <?php if($has_approval){
                    echo nl2br($approval->comments);
                } else { ?>
                    <br/>
                    <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
                <?php } ?>
            </td>
        </tr>
    <?php }
    }

    ?>
</table>
