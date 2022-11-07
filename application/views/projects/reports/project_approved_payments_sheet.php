<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 9/16/2018
 * Time: 11:53 PM
 */

$this->load->view('includes/letterhead');
?>
    <h2 style="text-align: center">PROJECT APPROVED PAYMENTS REPORT</h2>
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
$this->load->view('projects/reports/project_approved_payments_table');
?>