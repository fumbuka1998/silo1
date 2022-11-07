<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/23/2018
 * Time: 12:33 PM
 */

$this->load->view('includes/letterhead');
$project = $sub_contract_requisition->project();
$full_access = $project->manager_access() || check_permission('Administrative Actions');
$approved = $sub_contract_requisition->status == 'APPROVED';
$currency = $sub_contract_requisition->currency();

?>
<h2 style="text-align: center">SUB CONTRACT REQUISITION APPROVAL CHAIN</h2>
<br/>
<table style="font-size: 11px" width="100%">
    <tr>
        <td style=" width:20%; vertical-align: top">
            <strong>Requisition No: </strong><br/><?= $sub_contract_requisition->sub_contract_requisition_number() ?>
        </td>
        <td style=" width:20%;  vertical-align: top">
            <strong>Required Date: </strong><br/><?= $sub_contract_requisition->required_date != null ? custom_standard_date($sub_contract_requisition->required_date) : 'N/A' ?>
        </td>
        <td style=" width:60%;  vertical-align: top">
            <strong>Requested For: </strong><br/><?= $sub_contract_requisition->cost_center_name() ?>
        </td>
    </tr>
</table>
<br/>
<table style="font-size: 10px" width="100%" cellspacing="0" border="1">
    <thead>
    <tr>
        <th>S.No</th>
        <th>Contractor</th>
        <th>Certificate No.</th>
        <th nowrap="true">Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sn = $total_amount = 0;
    $sub_contract_requisition_items = $sub_contract_requisition->sub_contract_requisition_items();
    foreach($sub_contract_requisition_items as $item){
        $sn++;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= $item->certificate()->sub_contract()->stakeholder()->stakeholder_name ?></td>
            <td><?= $item->certificate()->certificate_number ?></td>
            <?php
            if ($approved) {

                $approved_item = $item->approved_item($last_approval->{$last_approval::DB_TABLE_PK});
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
        <th style="text-align: right"><?= $currency->symbol .'  '. number_format($total_amount,2) ?></th>
    </tr>

    <?php
        if(($approved && !is_null($last_approval->vat_inclusive) && $last_approval->vat_inclusive != 0) || (!is_null($sub_contract_requisition->vat_inclusive) && $sub_contract_requisition->vat_inclusive != 0)){
            $vat_amount = $total_amount*0.18;
            $grand_total = $total_amount*1.18;
            ?>
            <tr>
                <th colspan="3" style="text-align: right">VAT</th>
                <th style="text-align: right"><?= $currency->symbol .'  '. number_format($vat_amount,2) ?></th>
            </tr>
            <tr>
                <th colspan="3" style="text-align: right">GRAND TOTAL</th>
                <th style="text-align: right"><?= $currency->symbol .'  '. number_format($grand_total,2) ?></th>
            </tr>
            <?php
        }
    ?>
    </tfoot>
</table>
<br/>
<?php
  $approvals = $sub_contract_requisition->sub_contract_requisition_approvals();

    foreach ($approvals as $approval){
        $total_approved_amount = 0;
        ?>
        <pagebreak>
        <br/>
        <table width="100%">
            <tr>
                <td style=" width:50%">
                    <strong><?= ucwords(strtolower($approval->approval_chain_level()->label)) ?> By: </strong><?= $approval->created_by()->full_name() ?>
                </td>
                <td style=" width:50%">
                    <strong>Action Date </strong><?= custom_standard_date($approval->approval_date) ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <strong>Comments: </strong><?= $approval->approving_comments != '' ? $approval->approving_comments : 'N/A' ?>
                </td>
            </tr>
        </table>
        <br/>
        <table style="font-size: 11px" width="100%" cellspacing="0" border="1">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Contractor</th>
                    <th>Certificate No.</th>
                    <th>Approved Amount</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sub_contract_requisition_items = $sub_contract_requisition->sub_contract_requisition_items();
            $sn = 0;
            foreach($sub_contract_requisition_items as $item){
                $sn++;
                $approval->requisition_approval_items($item->{$item::DB_TABLE_PK});
                $total_approved_amount += $approved_amount = $approval->requisition_approval_items($item->{$item::DB_TABLE_PK})->approved_amount;
                ?>
                <tr>
                    <td><?= $sn ?></td>
                    <td><?= $item->certificate()->sub_contract()->stakeholder()->stakeholder_name ?></td>
                    <td><?= $item->certificate()->certificate_number ?></td>
                    <td style="text-align: right"><?= $currency->symbol . ' ' . number_format($approved_amount, 2) ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th style="text-align: left"  colspan="3">TOTAL</th>
                    <th style="text-align: right"><?= $currency->symbol . ' ' .  number_format($total_approved_amount, 2) ?></th>
                </tr>


                <?php
                if(!is_null($last_approval->vat_inclusive) && $last_approval->vat_inclusive != 0){
                    $vat_amount = $total_approved_amount*0.18;
                    $grand_total = $total_approved_amount*1.18;
                    ?>
                    <tr>
                        <th colspan="3" style="text-align: right">VAT</th>
                        <th style="text-align: right"><?= $currency->symbol .'  '. number_format($vat_amount,2) ?></th>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align: right">GRAND TOTAL</th>
                        <th style="text-align: right"><?= $currency->symbol .'  '. number_format($grand_total,2) ?></th>
                    </tr>
                    <?php
                }
                ?>
            </tfoot>
        </table>
        <br/>
<?php
    }
?>


