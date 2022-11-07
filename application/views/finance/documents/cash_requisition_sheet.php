<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/22/2017
 * Time: 2:49 PM
 */

$this->load->view('includes/letterhead');
$account = $requisition->account();
?>
<table width="100%">
    <tr>
        <td style=" width:20%">
            <strong>Requisition No: </strong><br/><?= $requisition->requisition_number() ?>
        </td>
        <td style=" width:60%">
            <strong>Account: </strong><br/><?= $account->account_name ?>
        </td>
        <td style=" width:20%">
            <strong>Required Date: </strong><br/><?= $requisition->required_date != null ? custom_standard_date($requisition->required_date) : 'N/A' ?>
        </td>
    </tr>
</table>
<br/>
<br/>
<table style="font-size: 11px" width="100%" cellspacing="0" border="1">
    <thead>
    <tr>
        <th>Description</th><th>Unit</th>
        <th>Requested Quantity</th>
        <th>Requested Rate</th>
        <th>Requested Amount</th>
        <?php if($requisition->status == 'APPROVED'){ ?>
        <th>Approved Quantity</th>
        <th>Approved Rate</th>
        <th>Approved Amount</th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php
    $items = $requisition->cash_requisition_items();
    $total_requested = $total_approved = 0;
        foreach($items as $item) {
            $total_requested += $requested_amount = $item->requested_rate*$item->requested_quantity;
            $total_approved += $approved_amount = $item->approved_rate*$item->approved_quantity;
            ?>
            <tr>
                <td><?= $item->description ?></td>
                <td><?= $item->measurement_unit()->symbol ?></td>
                <td style="text-align: right"><?= $item->requested_quantity ?></td>
                <td style="text-align: right"><?= number_format($item->requested_rate) ?></td>
                <td style="text-align: right"><?= number_format(($requested_amount)) ?></td>
            <?php if ($requisition->status == 'APPROVED') {
                ?>
                <td  style="text-align: right"><?= $item->approved_quantity ?></td>
                <td  style="text-align: right"><?= number_format($item->approved_rate) ?></td>
                <td  style="text-align: right"><?= number_format(($approved_amount)) ?></td>
                <?php
            }
                ?>
                </tr>
                <?php
        }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">TOTAL</th><th style="text-align: right"><?= number_format($total_requested) ?></th>
            <?php if ($requisition->status == 'APPROVED') { ?>
                <th colspan="2"></th>
            <th style="text-align: right"><?= number_format($total_approved) ?></th>
                <?php
            }
            ?>
        </tr>
    </tfoot>
</table>
<br/>
<table style="font-size: 12px" width="100%">
    <?php if($requisition->request_date != null){ ?>
        <tr>
            <td colspan="3"><hr/></td>
        </tr>
        <tr>
            <td  style=" width:25%; vertical-align: top">
                <strong>Requesting Comments</strong><br/><?= $requisition->requesting_remarks ?>
            </td>
            <td style=" width:25%; vertical-align: top">
                <strong>Requested By: </strong><br/><?= $requisition->requester()->full_name() ?>
            </td>
            <td style=" width:25%; vertical-align: top">
                <strong>Request Date: </strong><br/><?= $requisition->request_date != null ? custom_standard_date($requisition->request_date) : '' ?>
            </td>
        </tr>
    <?php }

    if($requisition->status == 'APPROVED'){ ?>
        <tr>
            <td colspan="3"><hr/></td>
        </tr>
        <tr>
            <td  style=" width:50%; vertical-align: top">
                <strong>Approving Comments</strong><br/><?= $requisition->approving_remarks ?>
            </td>
            <td style=" width:25%; vertical-align: top">
                <strong>Approved By: </strong><br/><?= $requisition->approver()->full_name() ?>
            </td>
            <td style=" width:25%; vertical-align: top">
                <strong>Approve Date: </strong><br/><?= custom_standard_date($requisition->approved_date) ?>
            </td>
        </tr>
    <?php } ?>
</table>
