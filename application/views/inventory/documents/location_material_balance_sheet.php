<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 5/17/2017
 * Time: 2:02 PM
 */
$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">MATERIAL STOCK REPORT</h2>
<br/>

<table width="100%">
    <tr>
        <td>
            <strong>LOCATION: </strong><br/><?= $location_name ?>
        </td>
        <td>
            <strong>Project: </strong><br/><?= $project_name ?>
        </td>
        <td>
            <strong>As Per: </strong><br/><?= custom_standard_date($to) ?>
        </td>
    </tr>
</table>
<br/>
<?php $this->load->view('inventory/reports/material_balance_report_table');