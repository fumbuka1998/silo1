<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/8/2017
 * Time: 11:02 AM
 */

$this->load->view('includes/letterhead');
$has_project = $requisition->project_requisition();
if($has_project){
    $project = $has_project->project();
}
$full_access = !$has_project || ($has_project && $project->manager_access()) || check_permission('Administrative Actions');
$approved = $requisition->status == 'APPROVED';
?>
<h2 style="text-align: center">REQUISITION APPROVAL CHAIN SHEET</h2>
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
        <th>Requested Quantity</th>
        <th>Requested Rate</th>
        <th>Requested Amount</th>
        <th>Requested Vendor</th>
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
            <td style="text-align: right"><?= $item->currency_symbol().' '. number_format($item->requested_rate,3) ?></td>
            <td style="text-align: right"><?= $item->currency_symbol().' '.  number_format(($item->requested_quantity*$item->requested_rate),3) ?></td>
            <td><?= $item->requested_vendor_id != null ? $item->requested_vendor()->vendor_name : '' ?></td>
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
            <td style="text-align: right"><?= $item->currency_symbol().' '. number_format($item->requested_rate,2) ?></td>
            <td style="text-align: right"><?= $item->currency_symbol().' '.  number_format(($item->requested_quantity*$item->requested_rate),3) ?></td>
            <td>N/A</td>
        </tr>
        <?php
    }

    ?>
    </tbody>
</table><br/>
<?php
  $approvals = $requisition->requisition_approvals();

    foreach ($approvals as $approval){
        ?>

        <table style="font-size: 11px" width="100%" cellspacing="0" border="1">
            <thead>
            <tr>
                <th>Item Description</th>
                <th>Unit</th>
                <th>Approved Information</th>
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
                    <?php
                        $sources = $approval->material_items($item->{$item::DB_TABLE_PK});
                        ?>
                        <td style="width: 45%">
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
                                        <td style="text-align: right"><?= number_format($source->approved_rate,3) ?></td>
                                        <td style="text-align: right"><?= number_format(($source->approved_quantity*$source->approved_rate),3) ?></td>
                                        <td><?= $source->currency()->symbol ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </td>
                </tr>
                <?php
            }

            $cash_items = $requisition->cash_items();
            foreach ($cash_items as $item){
                ?>
                <tr>
                    <td><?= $item->description ?></td>
                    <td><?= $item->measurement_unit()->symbol ?></td>
                    <?php
                        $sources = $approval->cash_items($item->{$item::DB_TABLE_PK});
                        ?>
                        <td style="width: 45%">
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
                                        <td style="text-align: right"><?= number_format($source->approved_rate,3) ?></td>
                                        <td style="text-align: right"><?= number_format(($source->approved_quantity*$source->approved_rate),3) ?></td>
                                        <td><?= $source->currency()->symbol ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </td>
                </tr>
                <?php
            }

            ?>
            </tbody>
        </table><br/>

        <table width="100%">
            <tr>
                <td style=" width:33%">
                    <strong>Checked By: </strong><?= $approval->created_by()->full_name() ?>
                </td>
                <td style=" width:33%">
                    <strong>Action Date </strong><?= custom_standard_date($approval->approved_date) ?>
                </td>
                <td style=" width:33%">
                    <strong>Comments: </strong><?= $approval->approving_comments != '' ? $approval->approving_comments : 'N/A' ?>
                </td>
            </tr>
        </table>
        <hr/>
<?php
    }
?>


