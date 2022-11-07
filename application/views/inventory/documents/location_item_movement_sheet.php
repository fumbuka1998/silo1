<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 5/17/2017
 * Time: 2:02 PM
 */
$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">ITEM MOVEMENT REPORT</h2>
<br/>

<table width="100%">
    <tr>
        <td>
            <strong>Location: </strong><br/><?= $location_name ?>
        </td>
        <td>
            <strong>Project: </strong><br/><?= $project_name ?>
        </td>
        <td>
            <strong>Item: </strong><br/><?= $material_item->item_name ?>
        </td>
    </tr>
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <tr>
        <td>
            <strong>UOM: </strong><?= $material_item->unit()->symbol ?>
        </td>
        <td>
            <strong>From: </strong><?= custom_standard_date($from) ?>
        </td>
        <td>
            <strong>To: </strong><?= custom_standard_date($to) ?>
        </td>
    </tr>
</table>
<br/>
<?php $this->load->view('inventory/reports/item_movement_report_table');