
<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 5/17/2017
 * Time: 2:02 PM
 */
$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">SUPPLIED ITEMS</h2>
<br/>

<table width="100%">
    <tr>
        <td style=" width:50%">
            <strong>Vendor: </strong><?= $stakeholder->stakeholder_name ?>
        </td>
        <td style=" width:25%">
            <strong>From: </strong><?= custom_standard_date($from) ?>
        </td>
        <td style=" width:25%">
            <strong>To: </strong><?= custom_standard_date($to) ?>
        </td>
    </tr>
</table>
<br/>
<?php $this->load->view('procurements/vendors/supplied_items_report_table');