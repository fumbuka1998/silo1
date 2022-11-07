<?php
if(check_privilege('Make Payment')){
	?>
<span class="pull-right">
	<form>
		<a type="button" id="to_statement_link" type_and_id="<?= $account_type_and_id ?>" title="Click To View <?= ucfirst($account_name) ?>'s Statement"  style="cursor: pointer">
		   <?= $symbol.' '.accountancy_number($running_balance) ?>
		</a>
		<input type="hidden" name="currency_id" value="<?= $currency->{$currency::DB_TABLE_PK} ?>">
	</form>
</span>
<?php } ?>

