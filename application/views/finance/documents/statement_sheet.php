<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/25/2016
 * Time: 6:40 PM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">ACCOUNT STATEMENT</h2>
<br/>
<table width="100%">
    <tr>
        <td>
            <strong>From:</strong> <?= $account->account_name ?>
        </td>
        <td width="18%">
            <strong>From:</strong> <?= custom_standard_date($from) ?>
        </td>
        <td width="18%">
            <strong>To:</strong> <?= custom_standard_date($to) ?>
        </td>
    </tr>
</table>
<br/>
<?php $this->load->view('finance/account_profile/statement_transactions_table');


