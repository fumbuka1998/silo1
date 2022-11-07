<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/5/2016
 * Time: 7:16 PM
 */

    $this->load->view('includes/letterhead');
?>
<hr/>
<h2 style="text-align: center">Audit Trail Report</h2>
<br/>
<table width="100%">
    <tr>
        <td>
            <strong>Action: </strong><?= $action ?>
        </td>
        <td>
            <strong>Project: </strong><?= $project ?>
        </td>
        <td>
            <strong>From: </strong><?= $from ?>
        </td>
        <td>
            <strong>To: </strong><?= $to ?>
        </td>
        <td>
            <strong>Printed On: </strong><?= strftime('%d/%m/%Y %H:%M:%S') ?>
        </td>

    </tr>
</table>
<br/>
<?php $this->load->view('administrative_actions/audit_trail_table'); ?>
