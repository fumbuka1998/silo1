<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/20/2018
 * Time: 1:19 PM
 */

$this->load->view('includes/letterhead');
?>
    <h2 style="text-align: center">ASSET STOCK REPORT</h2>
    <br/>

    <table width="100%">
        <tr>
            <td>
                <strong>LOCATION: </strong><br/><?= $location_name ?>
            </td>
            <td>
                <strong>Project: </strong><br/><?= $project_name ?>
            </td>
            <td>
                <strong>As Per: </strong><br/><?= custom_standard_date($to) ?>
            </td>
        </tr>
    </table>
    <br/>
<?php $this->load->view('inventory/reports/asset_stock_table');