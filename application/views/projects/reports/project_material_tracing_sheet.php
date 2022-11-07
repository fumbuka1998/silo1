<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/9/2016
 * Time: 3:57 AM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">MATERIAL TRACING REPORT</h2>
<br/>
<table width="100%">
    <tr>
        <td  style="width: 33.33%; vertical-align: top">
            <strong>Project</strong><br/><?= $project->project_name ?>
        </td>
        <td  style="width: 33.33%; vertical-align: top">
            <strong>Printed By</strong><br/><?= $this->session->userdata('employee_name') ?>
        </td>
        <td style="width: 33.33%;  vertical-align: top">
            <strong>Date and Time: </strong><br/><?= standard_datetime() ?>
        </td>
    </tr>
</table>
<br/>
<?php
$this->load->view('projects/reports/project_material_tracing_table');
?>

