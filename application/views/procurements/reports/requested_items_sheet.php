<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/13/2018
 * Time: 4:07 PM
 */

$this->load->view('includes/letterhead');
?>
    <h2 style="text-align: center">REQUESTED ITEMS REPORT</h2>
    <br/>
    <table style="font-size: 13px" width="100%">
        <tr>
            <td  style=" vertical-align: top">
                <strong>From: </strong><?= $from ?>
            </td>
            <td  style=" vertical-align: top">
                <strong>To: </strong><?= $to ?>
            </td>
        </tr>
    </table>
    <br/>
<?php
$this->load->view('procurements/reports/requested_items_table');
?>