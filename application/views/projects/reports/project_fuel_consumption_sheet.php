<?php

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">PROJECT FUEL CONSUMPTION REPORT</h2>
<br />
<table width="100%">
    <tr>
        <td style="width: 25%; vertical-align: top">
            <strong>Project</strong><br /><?= $project->project_name ?>
        </td>
        <td style="width: 25%; vertical-align: top">
            <strong>Printed By</strong><br /><?= $this->session->userdata('employee_name') ?>
        </td>
        <td style=" width:25%; vertical-align: top">
            <strong>From: </strong><?= custom_standard_date($from) ?>
        </td>
        <td style=" width:25%; vertical-align: top">
            <strong>To: </strong><?= custom_standard_date($to) ?>
        </td>
    </tr>
    <tr>
        <td style="width: 33.33%;  vertical-align: top">
            <strong>Date and Time: </strong><br /><?= date('d/m/Y H:i:s') ?>
        </td>
    </tr>
</table>
<br />
<?php
$this->load->view('projects/reports/project_fuel_consumption_table');
?>