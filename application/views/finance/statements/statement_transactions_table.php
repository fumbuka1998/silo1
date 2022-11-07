<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 5/28/2018
 * Time: 2:20 PM
 *
 */
?>
<table <?php if($print_pdf){ ?> style="font-size: 10px" width="100%" border="1" cellspacing="0"  <?php } else { ?> style="table-layout: fixed" <?php } ?>  class="table table-bordered table-hover">
    <thead>
    <tr style="background-color: #97a0b3">
        <th style="width: 8%">Date</th><th style="width: 8%">Transaction Type</th><th>Description</th><th style="width: 13%">Reference</th><th style="width: 15%; text-align: right">Debit</th><th style="width: 15%; text-align: right">Credit</th><th style="width: 15%; text-align: right">Balance</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?= set_date($start_date) ?></td>
        <td style="font-style: italic" colspan="5">Opening Balance</td>
		<?php
		$balance = $opening_balance;
		$currency_symbol = $currency->symbol;
		if($balance > 0){ ?>
			<td style="text-align: right"><?= $currency_symbol.' '. accountancy_number($balance).' Dr' ?></td>
		<?php } else if($balance < 0) { ?>
			<td style="text-align: right"><?= $currency_symbol.' '. accountancy_number($balance).' Cr' ?></td>
		<?php } else { ?>
			<td style="text-align: right"><?= $currency_symbol.' '. accountancy_number($balance) ?></td>
		<?php }  ?>
    </tr>
    <?php
    $total_credit = $total_debit = 0;
    $credit_count = 0;
    foreach ($transactions as $transaction){
        $transaction = (object) $transaction;
        $balance = $balance+$transaction->debit-$transaction->credit;
        if($transaction->debit != 0 || $transaction->credit != 0){
            $total_credit += $transaction->credit;
            $total_debit += $transaction->debit;
            ?>
            <tr>
                <td><?= set_date($transaction->transaction_date) ?></td>
                <td><?= ucwords($transaction->transaction_type) ?></td>
                <td><?= wordwrap($transaction->remarks,50,'<br/>') ?></td>
                <td><?= $transaction->reference ?></td>
                <td style="text-align: right"><?= $transaction->debit != 0 ? $currency_symbol.' '.  number_format($transaction->debit, 2) : '' ?></td>
                <td style="text-align: right"><?= $transaction->credit != 0 ? $currency_symbol.' '. number_format($transaction->credit, 2) : '' ?></td>
                <?php
                    if($transaction->credit != 0){
                        $credit_count++;
                    }

                    if($balance > 0){ ?>
                    <td style="text-align: right"><?= $currency_symbol.' '. accountancy_number($balance).' Dr' ?></td>
                <?php } else if($balance < 0) { ?>
                    <td style="text-align: right"><?= $currency_symbol.' '. accountancy_number($balance).' Cr' ?></td>
                <?php } else { ?>
						<td style="text-align: right"><?= $currency_symbol.' '. accountancy_number($balance) ?></td>
				<?php }  ?>
            </tr>
            <?php
        }
    }
    ?>
    <tr style="background-color: #97a0b3">
        <td colspan="4" style="text-align: right"><strong>CURRENT TOTAL</strong></td><td style="text-align: right"><strong><?= $currency_symbol.' '. number_format($total_debit,2) ?></strong></td><td style="text-align: right"><strong><?= $currency_symbol.' '. number_format($total_credit,2) ?></strong></td><td></td>
    </tr>
    </tbody>
</table>



