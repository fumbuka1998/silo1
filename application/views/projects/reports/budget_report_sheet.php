
<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 5/17/2017
 * Time: 2:02 PM
 */
$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">PROJECT BUDGET SUMMARY</h2>
<br/>

<table width="100%">
    <tr>
        <td>
            <strong>Project: </strong><?= $project->project_name ?>
        </td>
    </tr>
</table>
<br/>
<?php $this->load->view('projects/reports/budget_report_table');