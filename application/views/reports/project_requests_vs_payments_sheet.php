<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/30/2018
 * Time: 12:56 PM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">REQUEST VS PAYMENTS REPORT</h2>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td  style=" vertical-align: top">
            <strong>Project: </strong><?= $project->project_name ?>
        </td>
        <td style="  vertical-align: top">
            <strong>From: </strong><?= custom_standard_date($from) ?>
        </td>
        <td style="  vertical-align: top">
            <strong>To: </strong><?= custom_standard_date($to) ?>
        </td>
    </tr>
</table>
<br/>
<?php
$this->load->view('reports/project_requests_vs_payments_table');
?>

