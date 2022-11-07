<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 2/15/2019
 * Time: 3:39 PM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">SALES REPORT</h2>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td  style=" vertical-align: top">
            <strong>From: </strong><?= $from ?>
        </td>
        <td  style=" vertical-align: top">
            <strong>To: </strong><?= $to ?>
        </td>
        <td  style=" vertical-align: top">
            <strong>Project: </strong><?= $project ? $project->project_name : "ALL" ?>
        </td>
    </tr>
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <?php
    if($location != false) {
        ?>
        <tr>
            <td style="width: 40%;vertical-align: top">
                <strong>Location: </strong><br/><?= $location->location_name ?>
            </td>
            <?php
            if($sub_location != false) {
            ?>
            <td style="vertical-align: top">
                <strong>Sub Location: </strong><br/><?= $sub_location->sub_location_name ?>
            </td>
            <?php
            }
            ?>
        </tr>
        <?php
    }
     ?>
</table>
<br/>
<?php
$this->load->view('inventory/reports/inventory_location_sales_table');
?>
