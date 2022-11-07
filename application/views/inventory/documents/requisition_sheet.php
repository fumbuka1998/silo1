<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/8/2016
 * Time: 5:56 PM
 */

$this->load->view('includes/letterhead');
$location = $requisition->location();
$project = $location->project();
$has_project = $location->project_id != null;
$full_access = !$has_project || ($has_project && $project->manager_access()) || check_permission('Administrative Actions');
$approved = $requisition->status == 'APPROVED';
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
            <strong>Location: </strong><?= $location->location_name ?>
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
            <th>Material/Tool Type</th>
            <th>Unit</th>
            <th>Requested Quantity</th>
            <?php if($full_access){ ?>
            <th>Requested Price</th>
            <th>Requested Amount</th>
            <th>Requested Vendor</th>
            <?php } ?>
            <th>Requesting Remarks</th>
            <?= $approved ? '<th>Approved Information</th>' : null ?>
        </tr>
    </thead>
    <tbody>
    <?php
        $material_items = $requisition->material_items();
        $total_requested_amount = $total_approved_amount = 0;
        foreach($material_items as $item){
            $unapproved_quantity = $requisition->status == 'INITIATED' ? $item->initiated_quantity : $item->requested_quantity;
            $total_requested_amount += $requested_amount = $item->requested_quantity*$item->requested_price;
            $material = $item->material_item();
     ?>
            <tr>
                <td><?= $material->item_name ?></td>
                <td><?= $material->unit()->symbol ?></td>
                <td style="text-align: right"><?= $unapproved_quantity ?></td>
                <?php if($full_access){ ?>
                <td style="text-align: right"><?= $item->currency_symbol().' '. number_format($item->requested_price) ?></td>
                <td style="text-align: right"><?= $item->currency_symbol().' '.  number_format($requested_amount) ?></td>
                <td><?= $item->requested_vendor_id != null ? $item->requested_vendor()->vendor_name : '' ?></td>
               <?php } ?>
                <td><?= $item->requesting_remarks ?></td>
                <?php
                    if($approved){
                        $sources = $item->approved_sources();
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
                                            <td style="text-align: right"><?= number_format($source->approved_price) ?></td>
                                            <td style="text-align: right"><?= number_format(($source->approved_quantity*$source->approved_price)) ?></td>
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

        /*$tools_items = $requisition->tools_items();
        foreach($tools_items as $item){
            $unapproved_quantity = $requisition->status == 'INITIATED' ? $item->initiated_quantity : $item->requested_quantity;
            $total_requested_amount += $requested_amount = $item->requested_quantity*$item->requested_price;
            $total_approved_amount += $approved_amount = $item->approved_quantity*$item->approved_price;
            $tool_type = $item->tool_type();

     */?><!--
            <tr>
                <td><?/*= $tool_type->name */?></td>
                <td>PCS</td>
                <td style="text-align: right"><?/*= $unapproved_quantity */?></td>
                <?/*= $approved ? '<td style="text-align: right">'.$item->approved_quantity.'</td>' : null */?>
                <?php /*if($full_access){ */?>
                <td style="text-align: right"><?/*= number_format($item->requested_price) */?></td>
                <?/*= $approved ? '<td style="text-align: right">'.number_format($item->approved_quantity).'</td>' : null */?>
                <td style="text-align: right"><?/*= number_format($requested_amount) */?></td>
                <?/*= $approved ? '<td style="text-align: right">'.number_format($approved_amount).'</td>' : null */?>
                <td><?/*= $item->requested_vendor_id != null ? $item->requested_vendor()->vendor_name : '' */?></td>
                <?/*= $approved ? '<td>'.( $item->approved_vendor_id != null ? $item->approved_vendor()->vendor_name : '' ).'</td>' : null */?>
                <?php /*} */?>
                <td><?/*= $item->requesting_remarks */?></td>
                <?/*= $approved ? '<td>'.$item->approving_remarks.'</td>' : null */?>
            </tr>
    --><?php
/*        }*/
        if($full_access){
    ?>
        <!--<tr>
            <th>TOTAL</th>
            <th></th>
            <th></th>
            <?php /*if($full_access){ */?>
            <?/*= $approved ? '<th></th>' : null */?>
            <th></th>
            <?/*= $approved ? '<th></th>' : null */?>
            <th style="text-align: right"><?/*= number_format($total_requested_amount) */?></th>
            <?/*= $approved ? '<th style="text-align: right">'.number_format($total_approved_amount).'</th>' : null */?>
            <th></th>
            <?/*= $approved ? '<th></th>' : null */?>
            <?php /*} */?>
            <th></th>
            <?/*= $approved ? '<th></th>' : null */?>
        </tr>-->
    <?php } ?>
    </tbody>
</table><br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td  style=" width:25%; vertical-align: top">
            <strong>Initiating Comments</strong><br/><?= $requisition->initiating_comments ?>
        </td>
        <td style=" width:25%; vertical-align: top">
            <strong>Initiated By: </strong><br/><?= $requisition->initiator()->full_name() ?><br/>
            <strong><em><?= $requisition->initiator_title() ?></em></strong>
        </td>
        <td style=" width:25%; vertical-align: top">
            <strong>Initiated Date: </strong><br/><?= custom_standard_date($requisition->initiated_date) ?>
        </td>
    </tr>
    <?php if($requisition->request_date != null){ ?>
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <tr>
        <td  style=" width:25%; vertical-align: top">
            <strong>Requesting Comments</strong><br/><?= $requisition->requesting_comments ?>
        </td>
        <td style=" width:25%; vertical-align: top">
            <strong>Requested By: </strong><br/><?= $requisition->requester()->full_name() ?><br/>
            <strong><em><?= $requisition->requester_title() ?></em></strong>
        </td>
        <td style=" width:25%; vertical-align: top">
            <strong>Request Date: </strong><br/><?= $requisition->request_date != null ? custom_standard_date($requisition->request_date) : '' ?>
        </td>
    </tr>
    <?php }

    if($requisition->status == 'APPROVED' && ($full_access)){ ?>
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <tr>
        <td  style=" width:50%; vertical-align: top">
            <strong>Approving Comments</strong><br/><?= $requisition->approving_comments ?>
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
