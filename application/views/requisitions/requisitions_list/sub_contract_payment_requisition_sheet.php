<?php

/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/23/2018
 * Time: 11:00 AM
 */


$this->load->view('includes/letterhead');
$project = $sub_contract_requisition->project();
$full_access = $project->manager_access() || check_permission('Administrative Actions');
$approved = $sub_contract_requisition->status == 'APPROVED';
$last_approval = $sub_contract_requisition->last_approval();
$last_approval_id = $last_approval ? $last_approval->{$last_approval::DB_TABLE_PK} : false;
$currency = $sub_contract_requisition->currency();
$total_amount = 0;


?>
<h2 style="text-align: center"><?= $approved ? 'APPROVED SUB CONTRACT PAYMENT REQUISITION' : 'SUB CONTRACT PAYMENT REQUISITION SHEET' ?></h2>
<br />
<table style="font-size: 11px" width="100%">
    <tr>
        <td style=" width:20%; vertical-align: top">
            <strong>Department: </strong><br />
            <?= $sub_contract_requisition->department() ?>
        </td>
        <td style=" width:20%; vertical-align: top">
            <strong>Requisition No: </strong><br /><?= $sub_contract_requisition->sub_contract_requisition_number() ?>
        </td>
        <td style=" width:20%;  vertical-align: top">
            <strong>Required Date: </strong><br /><?= $sub_contract_requisition->required_date != null ? custom_standard_date($sub_contract_requisition->required_date) : 'N/A' ?>
        </td>
        <td style=" width:40%;  vertical-align: top">
            <strong>Requested For: </strong><br /><?= $sub_contract_requisition->cost_center_name() ?>
        </td>
    </tr>
</table>
<br />



<table style="font-size: 10px" width="100%" cellspacing="0" border="1">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Description</th>
            <th>Certificate No.</th>
            <th nowrap="true">Amount</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $sn = 0;
        $sub_contract_requisition_items = $sub_contract_requisition->sub_contract_requisition_items();
        foreach ($sub_contract_requisition_items as $item) {
            $sn++;
            $certificate = $item->certificate();
        ?>
            <tr>
                <td><?= $sn ?></td>
                <td>
                    <?= wordwrap($certificate->sub_contract()->contract_name . ' - ' . $certificate->sub_contract()->stakeholder()->stakeholder_name, 50, '<br/>') ?><br />
                    <?php
                    $cretifiacate_tasks = $certificate->certificate_tasks();

                    if (!empty($cretifiacate_tasks) && 1<0) { ?>
                        <table style="font-size: 10px" cellspacing="0" border="1">
                            <thead>
                                <tr>
                                    <td style=" text-align: center">S/N</td>
                                    <td><Task</td>
                                    <td style=" text-align: right">Certified Amount</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sn = $total_tsks_amount = 0;
                                foreach ($cretifiacate_tasks as $index => $cert_task) {
                                    $total_tsks_amount += $cert_task->amount ?>
                                    <tr>
                                        <td style=" text-align: center"><?= ++$sn ?></td>
                                        <td><?= $cert_task->task()->task_name ?></td>
                                        <td style=" text-align: right"><?= number_format($cert_task->amount, 2) ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Total</th>
                                    <th style="text-align: right"><?= number_format($total_tsks_amount, 2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                        <?php if ($certificate->vat_inclusive == 1) { ?>
                            Hint: Tasks amounts are VAT inclusive
                    <?php }
                    } ?>
                </td>
                <td><?= $item->certificate()->certificate_number ?></td>
                <?php
                if ($approved) {

                    $approved_item = $item->approved_item($last_approval_id);
                    $total_amount += $amount = $approved_item->approved_amount;
                ?>
                    <td style="text-align: right"><?= $currency->symbol . ' ' . number_format($amount, 2) ?></td>

                <?php
                } else {
                    $total_amount += $amount = $item->requested_amount;
                ?>
                    <td style="text-align: right"><?= $currency->symbol . ' ' . number_format($amount, 2) ?></td>
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
            <th colspan="3" style="text-align: right">TOTAL</th>
            <th style="text-align: right"><?= $currency->symbol . '  ' . number_format($total_amount, 2) ?></th>
        </tr>
        <?php
        if (!is_null($sub_contract_requisition->vat_inclusive) && $sub_contract_requisition->vat_inclusive != 0) {
            if (!is_null($sub_contract_requisition->vat_inclusive)) { ?>
                <tr>
                    <th style="text-align: right" colspan="3">VAT </th>
                    <th style="text-align: right"><?= $currency->symbol . '  ' . number_format($total_amount * 0.18, 2) ?></th>
                </tr>
            <?php }

            $grand_total = !is_null($sub_contract_requisition->vat_inclusive) ? $total_amount * 1.18 : $total_amount;
            ?>

            <tr>
                <th style="text-align: right" colspan="3">GRAND TOTAL</th>
                <th style="text-align: right"><?= $currency->symbol . '  ' . number_format($grand_total, 2) ?></th>
            </tr>
        <?php } ?>
    </tfoot>
</table><br />
<table style=" font-size: 12px" width="100%">
    <tr>
        <td colspan="3">
            <hr />
        </td>
    </tr>
    <tr>
        <td style=" width:25%; vertical-align: top">
            <strong>Requested By: </strong>
            <?php if ($sub_contract_requisition->status == 'PENDING') { ?>
                <br /><br />
                <span style="text-decoration: underline">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span>
            <?php } ?>
            <br /><?= $sub_contract_requisition->requester()->full_name() ?>
        </td>
        <td style=" width:25%; vertical-align: top">
            <strong>Request Date: </strong><br /><?= $sub_contract_requisition->request_date != null ? custom_standard_date($sub_contract_requisition->request_date) : '' ?>
        </td>
        <td style=" vertical-align: top">
            <strong>Requesting Comments</strong><br /><?= $sub_contract_requisition->requesting_comments ?>
        </td>
    </tr>
    <?php
    foreach ($chain_levels as $chain_level) {
        $has_approval = isset($sub_contract_requisition_approvals[$chain_level->{$chain_level::DB_TABLE_PK}]);
        $approval = $has_approval ?  $sub_contract_requisition_approvals[$chain_level->{$chain_level::DB_TABLE_PK}] : null; ?>

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
                    <?= custom_standard_date($approval->approval_date) ?>
                </td>
                <td style=" width:50%; vertical-align: top">
                    <strong>Comments</strong><br />
                    <?= nl2br($approval->approving_comments) ?>
                </td>
            </tr>
            <?php
        } else {
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
    <?php   }
        }
    }
    ?>
</table>

