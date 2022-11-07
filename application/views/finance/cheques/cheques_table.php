<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 5/28/2018
 * Time: 2:20 PM
 *
 */

if(!empty($cheques)) {
?>
<table <?php if($print){ ?> style="font-size: 10px" width="100%" border="1" cellspacing="0"  <?php } ?> class="table table-bordered table-hover">
    <thead>
    <tr>
        <th>S/N</th><th>Date</th><th>Payee Name</th><th>Cheque Number</th><th>Bank</th><th>Amount</th>
    </tr>
    </thead>
    <tbody>

    <?php
    $count = 1;
    foreach ($cheques AS $cheque) {
            ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= $cheque['date'] ?></td>
                <td><?= $cheque['payee_name'] ?></td>
                <td><?= $cheque['cheque_number'] ?></td>
                <td><?= $cheque['bank'] ?></td>
                <td style="text-align: right"><?= $cheque['currency_symbol'].' '.number_format($cheque['amount'],2) ?></td>
            </tr>
            <?php
            $count++;
        }
    ?>
    </tbody>
</table>
<?php } else { ?>
<div style="text-align: center" class="alert alert-info col-xs-12">
    No Cheques present
</div>
<?php } ?>


