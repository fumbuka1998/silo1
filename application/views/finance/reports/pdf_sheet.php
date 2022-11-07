<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/13/2018
 * Time: 4:07 PM
 */

$this->load->view('includes/letterhead');
?>
    <h2 style="text-align: center"><?= strtoupper($name_string) ?> REPORT</h2>
    <br/>
    <table style="font-size: 13px" width="100%">
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
$this->load->view('finance/'.$table_view);
?>