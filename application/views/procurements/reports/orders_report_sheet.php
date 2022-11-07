<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 6/8/2018
 * Time: 7:53 AM
 */
$this->load->view('includes/letterhead');
?>
<?php
if($report_type == 'RECEIVED'){
  ?>
    <h2 style="text-align: center">REVEIVED PURCHASE ORDER</h2>
    <br/>
    <?php
} else if($report_type == 'CANCELLED'){
    ?>
    <h2 style="text-align: center">CANCELLED PURCHASE ORDER</h2>
    <br/>
    <?php

}else if($report_type == 'PENDING'){
    ?>
    <h2 style="text-align: center">PENDING PURCHASE ORDERS</h2>
    <br/>
    <?php
} else if($report_type == 'CLOSED'){
    ?>
    <h2 style="text-align: center">CLOSED PURCHASE ORDERS</h2>
    <br/>
    <?php
}else{
    ?>
    <h2 style="text-align: center">ALL PURCHASE ORDERS</h2>
    <br/>
    <?php
}

?>

    <table width="100%">
        <tr>
            <td style=" width:50%">

            </td>
        </tr>
        <tr>
            <td style=" width:50%">
                <strong>From: </strong><?= custom_standard_date($from) ?>
            </td>
            <td style=" width:50%">
                <strong>To: </strong><?= custom_standard_date($to) ?>
            </td>
        </tr>
    </table>
    <br/>
<?php $this->load->view('procurements/reports/orders_report_table');
