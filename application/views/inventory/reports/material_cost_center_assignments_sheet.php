<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 2/16/2019
 * Time: 6:07 AM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">COST CENTER ASSIGNMENTS REPORT</h2>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td  style=" vertical-align: top">
            <strong>From: </strong><?= $from ?>
        </td>
        <td  style=" vertical-align: top">
            <strong>To: </strong><?= $to ?>
        </td>
    </tr>
    <tr>
        <td colspan="3"><hr/></td>
    </tr>
    <?php
    if($source != false) {
        ?>
        <tr>
            <td style="width: 40%;vertical-align: top">
                <strong>Source: </strong><br/><?= $source->project_name ?>
            </td>
            <?php
            if($destination != false) {
                ?>
                <td style="vertical-align: top">
                    <strong>Destination: </strong><br/><?= $destination->project_name ?>
                </td>
                <?php
            }
            ?>
        </tr>
        <?php
    }
    ?>
</table>
<br/>
<?php
$this->load->view('inventory/reports/material_cost_center_assignments_table');
?>
