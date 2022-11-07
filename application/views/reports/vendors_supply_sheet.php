<?php
/**
 * Created by PhpStorm.
 * User: miralearn
 * Date: 03/11/2018
 * Time: 09:24
 */

$this->load->view('includes/letterhead');
?>
    <h2 style="text-align: center">VENDOR SUPPLY REPORT</h2>
    <br/>

    <table style="font-size: 12px" width="100%">
        <tr>
            <td>
                <strong>From: </strong><br/><?= custom_standard_date($from) ?>
            </td>
            <td>
                <strong>To: </strong><br/><?= custom_standard_date($to) ?>
            </td>
        </tr>
    </table>
    <br/>
<?php
    $this->load->view('reports/vendors_supply_report');
?>
