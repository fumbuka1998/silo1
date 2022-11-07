<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 23/10/2018
 * Time: 10:51
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">MATERIAL ITEM AVAILABILITY</h2>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td  style=" vertical-align: top">
            <strong>From: </strong><?= $from ?>
        </td>
        <td  style=" vertical-align: top">
            <strong>To: </strong><?= $to ?>
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
<?php
$this->load->view('inventory/reports/all_locations_material_item_availability_table');
?>

