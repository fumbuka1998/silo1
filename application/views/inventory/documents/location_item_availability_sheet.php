<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 5/17/2017
 * Time: 2:02 PM
 */
$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">LOCATION ITEM AVAILABILITY</h2>
<br/>

<table cellspacing="10" width="100%">
    <tr>
        <td style="width: 40%; vertical-align: top">
            <strong>Location: </strong><br/><?= $location_name ?>
        </td>
        <td style="min-width: 40%; vertical-align: top">
            <strong>Project: </strong><br/><?= $project_name ?>
        </td>
        <td style="vertical-align: top">
            <strong>As of: </strong><br/><?= custom_standard_date($to) ?>
        </td>
    </tr>
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <tr>
        <td style="width: 40%;vertical-align: top">
            <strong>Item: </strong><br/><?= $material_item->item_name ?>
        </td>
        <td style="vertical-align: top">
            <strong>UOM: </strong><br/><?= $material_item->unit()->symbol ?>
        </td>
    </tr>
</table>
<br/>
<?php $this->load->view('inventory/reports/item_availability_table');