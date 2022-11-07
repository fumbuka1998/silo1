<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 5/17/2017
 * Time: 2:02 PM
 */
$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">VENDOR ORDERS STATEMENT</h2>
<br/>

<table style="font-size: 12px" width="100%">
    <tr>
        <td style=" width:50%">
            <strong>Vendor: </strong><?= $vendor->vendor_name ?>
        </td>
    </tr>
    <tr>
        <td colspan="3" style=" width:25%">
            <strong>Currency: </strong><?= $currency->name_and_symbol() ?>
        </td>
        <td style=" width:25%">
            <strong>From: </strong><?= custom_standard_date($from) ?>
        </td>
        <td style=" width:25%">
            <strong>To: </strong><?= custom_standard_date($to) ?>
        </td>
    </tr>
</table>
<br/>
<?php $this->load->view('procurements/vendors/orders_statement_table');