
<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 5/17/2017
 * Time: 2:02 PM
 */
$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">PROJECT COST TRACKING SHEET</h2>
<br/>

<table width="100%">
    <tr>
        <td style=" width:50%">
            <strong>Project: </strong><?= $project->project_name ?>
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
<?php $this->load->view('projects/reports/cost_tracking_table');