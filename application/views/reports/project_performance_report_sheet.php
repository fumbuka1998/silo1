<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 5/3/2018
 * Time: 10:19 AM
 */
$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">PROJECT CERTIFICATE REPORT</h2>
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
$this->load->view('reports/project_certificate_table');
?>
