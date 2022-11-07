<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/9/2016
 * Time: 3:57 AM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">PROJECT INVENTORY MOVEMENT</h2>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td  style=" vertical-align: top">
            <strong>Project: </strong><?= $project->project_name ?>
        </td>
        <td style="  vertical-align: top">
            <strong>As of: </strong><?= custom_standard_date($as_of) ?>
        </td>
    </tr>
</table>
<br/>
<?php
    $this->load->view('reports/project_inventory_movement_table');
?>

