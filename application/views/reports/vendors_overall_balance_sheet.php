<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 22/10/2018
 * Time: 08:32
 */

$this->load->view('includes/letterhead');
?>
    <h2 style="text-align: center">VENDORS OVERALL BALANCE </h2>
    <br/>
    <table style="font-size: 12px" width="100%">
        <tr>
            <td  style=" vertical-align: top">
                <strong>As Of: </strong><?= $as_of ?>
            </td>
        </tr>
    </table>
    <br/>
<?php
    $this->load->view('reports/vendors_overall_balance_table');
?>