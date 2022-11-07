<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/9/2018
 * Time: 10:19 AM
 */

$this->load->view('includes/letterhead');
?>
    <h2 style="text-align: center">ASSET(S) ITEM AVAILABILITY</h2>
    <br/>
    <table style="font-size: 12px" width="100%">
        <?php if($filtered){ ?>
        <tr>
            <td  style=" vertical-align: top">
                <strong><?= $asset_item->asset_name ?></strong>
            </td>
            <td  style=" vertical-align: top">
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td  style=" vertical-align: top">
                <strong>From: </strong><?= custom_standard_date($from) ?>
            </td>
            <td  style=" vertical-align: top">
                <strong>To: </strong><?= custom_standard_date($to) ?>
            </td>
        </tr>
    </table>
    <br/>
<?php
$this->load->view('assets/reports/assets_availability_table');
?>