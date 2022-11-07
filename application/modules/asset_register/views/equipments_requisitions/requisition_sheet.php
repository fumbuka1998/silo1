<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/8/2016
 * Time: 5:56 PM
 */

$this->load->view('includes/letterhead');
$has_project = $requisition->project_requisition();

if($has_project){
    $project = $has_project->project();
}
$full_access = !$has_project || ($has_project && $project->manager_access()) || check_permission('Administrative Actions');
$approved = $requisition->status == 'APPROVED';
$last_approval = $requisition->last_approval();
?>
<hr/>
<h2 style="text-align: center">REQUISITION SHEET</h2>
<br/>

<table width="100%">
    <tr>
        <td style=" width:33%">
            <strong>Requisition No: </strong><?= $requisition->requisition_number() ?>
        </td>
        <td style=" width:33%">
            <strong>Requested For: </strong><?= $has_project ? $project->project_name : $requisition->cost_center_name() ?>
        </td>
        <td style=" width:33%">
            <strong>Required Date: </strong><?= $requisition->required_date != null ? custom_standard_date($requisition->required_date) : 'N/A' ?>
        </td>
    </tr>
</table>
<br/>
<table style="font-size: 11px" width="100%" cellspacing="0" border="1">
    <thead>
        <tr>
            <th>Item Description</th>
            <th>Unit</th>
            <th>Quantity</th>
            <th nowrap="true">Rate</th>
            <th nowrap="true">Amount</th>
            <th>Requested Vendor</th>
            <th>Expense Account</th>
            <?= $approved ? '<th style="width: 50%">Approved Information</th>' : null ?>
        </tr>
    </thead>
    <tbody>
    <?php
        $material_items = $requisition->material_items();
        foreach($material_items as $item){
            $material = $item->material_item();
     ?>
            <tr>
                <td><?= $material->item_name ?></td>
                <td><?= $material->unit()->symbol ?></td>
                <td style="text-align: right"><?= $item->requested_quantity ?></td>
                <td style="text-align: right"><?= $item->currency_symbol().' '. number_format($item->requested_rate,2) ?></td>
                <td style="text-align: right"><?= $item->currency_symbol().' '.  number_format(($item->requested_quantity*$item->requested_rate),2) ?></td>
                <td><?= $item->requested_vendor_id != null ? $item->requested_vendor()->vendor_name : '' ?></td>
                <td><?= $item->expense_account()->account_name ?></td>
                <?php
                    if($approved && $last_approval){
                        $sources = $last_approval->material_items($item->{$item::DB_TABLE_PK});
                       ?>
                        <td>
                            <table style="font-size: 11px" width="100%" cellspacing="0" border="1">
                                <thead>
                                    <tr>
                                        <th>Source</th><th>Quantity</th><th>Price</th><th>Amount</th><th>Currency</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    foreach ($sources as $source){
                                        $source_name = $source->source_name();
                                    ?>
                                        <tr>
                                            <td><?= $source_name ?></td>
                                            <td><?= $source->approved_quantity ?></td>
                                            <td style="text-align: right"><?= number_format($source->approved_rate) ?></td>
                                            <td style="text-align: right"><?= number_format(($source->approved_quantity*$source->approved_rate)) ?></td>
                                            <td><?= $source->currency()->symbol ?></td>
                                        </tr>
                                <?php
                                    }
                                ?>
                                </tbody>
                            </table>
                        </td>
                <?php
                    }
                ?>
            </tr>
    <?php
        }

        $cash_items = $requisition->cash_items();
        foreach ($cash_items as $item){
?>
            <tr>
                <td><?= $item->description ?></td>
                <td><?= $item->measurement_unit()->symbol ?></td>
                <td style="text-align: right"><?= $item->requested_quantity ?></td>
                <td nowrap="nowrap" style="text-align: right"><?= $item->currency_symbol().' '. number_format($item->requested_rate,2) ?></td>
                <td nowrap="nowrap" style="text-align: right"><?= $item->currency_symbol().' '.  number_format(($item->requested_quantity*$item->requested_rate),2) ?></td>
                <td>N/A</td>
                <td><?= $item->expense_account()->account_name ?></td>
                <?php
                if($approved && $last_approval){
                    $sources = $last_approval->cash_items($item->{$item::DB_TABLE_PK});
                    ?>
                    <td>
                        <table style="font-size: 11px" width="100%" cellspacing="0" border="1">
                            <thead>
                            <tr>
                                <th>Source</th><th>Quantity</th><th>Price</th><th>Amount</th><th>Currency</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($sources as $source){
                                $source_name = $source->account()->account_name;
                                ?>
                                <tr>
                                    <td><?= $source_name ?></td>
                                    <td><?= $source->approved_quantity ?></td>
                                    <td style="text-align: right"><?= number_format($source->approved_rate) ?></td>
                                    <td style="text-align: right"><?= number_format(($source->approved_quantity*$source->approved_rate)) ?></td>
                                    <td><?= $source->currency()->symbol ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </td>
                    <?php
                }
                ?>
            </tr>
    <?php
        }
        
    ?>
    </tbody>
</table><br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <tr>
        <td  style=" width:25%; vertical-align: top">
            <strong>Requesting Comments</strong><br/><?= $requisition->requesting_comments ?>
        </td>
        <td style=" width:25%; vertical-align: top">
            <strong>Requested By: </strong><br/><?= $requisition->requester()->full_name() ?><br/>
        </td>
        <td style=" width:25%; vertical-align: top">
            <strong>Request Date: </strong><br/><?= $requisition->request_date != null ? custom_standard_date($requisition->request_date) : '' ?>
        </td>
    </tr>
    <?php

    if($requisition->status == 'APPROVED'){ ?>
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <tr>
        <td  style=" width:50%; vertical-align: top">
            <strong>Approving Comments</strong><br/><?= $requisition->finalizing_comments ?>
        </td>
        <td style=" width:25%; vertical-align: top">
            <strong>Approved By: </strong><br/><?= $requisition->finalizer()->full_name() ?>
        </td>
        <td style=" width:25%; vertical-align: top">
            <strong>Approve Date: </strong><br/><?= custom_standard_date($requisition->finalized_date) ?>
        </td>
    </tr>
    <?php } ?>
</table>
