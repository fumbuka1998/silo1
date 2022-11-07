<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 12/19/2018
 * Time: 10:20 AM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">BUSINESS ENQUIRY</h2>
<br/>
<table style="font-size: 11px"  width="100%" cellpadding="4" cellspacing="0">
    <tr>
        <td style="width: 70%">
            <h2>No. <?= $enquiry->enquiry_number() ?></h2>
            <b>Date: </b><?= custom_standard_date($enquiry->enquiry_date) ?><br/>
            <?php
            ?>
            <br/>
            <br/>
            <b>M/s:</b><br/>
            <?= $enquiry->enquiry_to()->stakeholder_name ?><br/>
            <?= nl2br($enquiry->enquiry_to()->address) ?>
        </td>
        <td nowrap="nowrap" style="vertical-align: top">
            <?php $company_details = get_company_details(); ?>
            <?= $company_details->company_name; ?><br/>
            Email: <?= $company_details->email; ?><br/>
            Telephone: <?= $company_details->telephone; ?><br/>
            Mobile: <?= $company_details->mobile; ?><br/>
            Fax: <?= $company_details->fax; ?><br/>
            VRN: <?= $company_details->vrn; ?><br/>
            TIN: <?= $company_details->tin; ?><br/>
        </td>
    </tr>
</table>
<br/>
<table style="font-size: 10px" width="100%" cellspacing="0" border="1">
    <thead>
        <tr>
            <th>S/N</th><th>Item Description</th><th>Unit</th><th>Quantity</th><th>Remarks</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $sn = 0;
    $material_items = $enquiry->material_items();
    foreach($material_items as $material_item){
        $sn++;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= wordwrap($material_item->material_item()->item_name, 40,'<br/>') ?></td>
            <td><?= $material_item->material_item()->unit()->symbol ?></td>
            <td><?= $material_item->quantity ?></td>
            <td><?= $material_item->remarks ?></td>
        </tr>
        <?php
    }

    $asset_items = $enquiry->asset_items();
    foreach($asset_items as $asset_item){
        $sn++;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= wordwrap($asset_item->asset_item()->asset_name, 40,'<br/>') ?></td>
            <td></td>
            <td><?= $asset_item->quantity ?></td>
            <td><?= $asset_item->remarks ?></td>
        </tr>
        <?php
    }

    $service_items = $enquiry->service_items();
    foreach($service_items as $service_item){
        $sn++;
        ?>
        <tr>
            <td><?= $sn ?></td>
            <td><?= wordwrap($service_item->description, 40,'<br/>') ?></td>
            <td><?= $service_item->measurement_unit()->symbol ?></td>
            <td><?= $service_item->quantity ?></td>
            <td><?= $service_item->remarks ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <tr>
        <td style=" vertical-align: top">
            <strong>Comments : </strong><br/><?= $enquiry->comments != null ? $enquiry->comments : 'N/A' ?>
        </td>
    </tr>
</table>


