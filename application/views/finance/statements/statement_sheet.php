<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 5/28/2018
 * Time: 2:18 PM
 */
$this->load->view('includes/letterhead');
?>
    <h2 style="text-align: center">ACCOUNT STATEMENT</h2>
    <br/>
    <table width="100%">
        <tr>
            <td style=" width:50%">
                <strong>Account: </strong><br/><?= $account_name ?>
            </td>
            <td style=" width:50%">
                <strong>Currency: </strong><?= $currency->name_and_symbol() ?>
            </td>
            </tr>
        <tr>
            <td style=" width:50%">
                <strong>From: </strong><?= custom_standard_date($start_date) ?>
            </td>
            <td style=" width:50%">
                <strong>To: </strong><?= custom_standard_date($end_date) ?>
            </td>
        </tr>
    </table>
    <br/>
<?php $this->load->view('finance/statements/statement_transactions_table');

