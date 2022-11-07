<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 5/28/2018
 * Time: 2:18 PM
 */
$this->load->view('includes/letterhead');
?>
    <h2 style="text-align: center">CHEQUE LIST</h2>
    <br/>
    <table width="100%">
        <tr>
            <td style=" width:50%">
                <strong>From: </strong><?= $from ?>
            </td>
            <td style=" width:50%">
                <strong>To: </strong><?= $to ?>
            </td>
        </tr>
    </table>
    <br/>
<?php

$this->load->view('finance/cheques/cheques_table');

