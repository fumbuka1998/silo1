<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 10/10/2018
 * Time: 11:25
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">PROJECT(S) FINANCIAL STATUS REPORT</h2>
<br/>
<table style="font-size: 13px" width="100%">
    <tr>
        <td  style=" vertical-align: top">
            <strong>As Of: </strong><?= $as_of ?>
        </td>
    </tr>
</table>
<br/>
<?php
    $this->load->view('reports/project_financial_status_table');
?>

