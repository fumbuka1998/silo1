<?php if(check_privilege('Finance Actions')){ ?>
    <span>
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
            Actions
        </button>
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>

        <ul class="dropdown-menu" role="menu">
             <li>
                <a class="btn btn-block btn-xs" data-toggle="modal" style="text-align: left"
                   data-target="#approved_cash_requisition_transaction_documents_<?= $requisition_approval_id ?>">
                    <i class="fa fa-bookmark"></i> Transaction Documents
                </a>
            </li>
            <?php
            if($amount_to_be_paid > 0 && check_privilege('Make Payment')) {
				if(!$has_stock_items) {
					?>
					<li>
						<a class="btn btn-block btn-xs" data-toggle="modal" style="text-align: left"
						   data-target="#payment_voucher_<?= $requisition_approval_id . '_' . $account_id ?>">
							<i class="fa fa-credit-card-alt"></i> Make Payment
						</a>
					</li>
					<?php if ($request_type == 'payment_request_invoice') { ?>
						<li>
                        <a class="btn btn-block btn-xs" data-toggle="modal" style="text-align: left"
						   data-target="#journal_entry<?= $approved_item->{$approved_item::DB_TABLE_PK} ?>">
                            <i class="fa fa-calendar-check-o"></i> Journal
                        </a>
                    </li>
					<?php }
				}
				if($request_type == 'requisition' && !$imprest_voucher_id){
				?>
                <li>
                    <a class="btn btn-block btn-xs" data-toggle="modal" style="text-align: left" data-target="#imprest_voucher_<?= $requisition_approval_id ?>">
                        <i class="fa fa-exchange"></i> Create Imprest
                    </a>
                </li>
         <?php }
            }
            if($imprest_voucher_id && isset($retirements) && check_privilege('Make Payment')) {?>
                <li>
					<a class="btn btn-block btn-xs" data-toggle="modal" style="text-align: left" data-target="#retirement_examination_modal_<?= $imprest_voucher_id ?>">
						<i class="fa fa-reorder"></i> Examine Retirement
					</a>
				</li>
                <?php
            }
            ?>
        </ul>

    </div>
</span>
	<div id="approved_cash_requisition_transaction_documents_<?= $requisition_approval_id ?>" class="modal fade transactions_document_modal" role="dialog">
		<?php
		$this->load->view('finance/transactions/approved_cash_requests/approved_payments_transactions_document_modal');
		?>
	</div>
    <?php if($amount_to_be_paid > 0) { ?>
        <div id="payment_voucher_<?= $requisition_approval_id . '_' . $account_id ?>"
             class="modal fade payment_voucher_form" role="dialog">
            <?php $this->load->view('finance/transactions/approved_cash_requests/payment_voucher_form'); ?>
        </div>
		<?php if($request_type == 'payment_request_invoice'){ ?>
		<div id="journal_entry<?= $approved_item->{$approved_item::DB_TABLE_PK} ?>" class="modal fade journal_voucher_entry_form2" role="dialog">
			<?php
			$this->load->view('finance/transactions/approved_cash_requests/journal_entry_form');
			?>
		</div>
		<?php }
		if($request_type == 'requisition') {
			?>
			<div id="imprest_voucher_<?= $requisition_approval_id ?>" class="modal fade imprest_voucher_form" role="dialog">
				<?php
				$this->load->view('finance/transactions/approved_cash_requests/imprest/imprest_voucher_form');
				?>
			</div>
			<?php
		}
    }
	if (isset($retirements)) {
		?>
		<div id="retirement_examination_modal_<?= $imprest_voucher_id ?>" class="modal fade retirement_examination_modal" role="dialog">
			<?php
			$this->load->view('finance/transactions/approved_cash_requests/imprest/retirement_examination_modal');
			?>
		</div>
<?php }
} ?>






