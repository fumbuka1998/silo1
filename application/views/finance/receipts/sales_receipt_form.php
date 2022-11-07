<?php
    $edit = isset($receipt);
    if($edit){
        $credit_account = $receipt->credit_account();
        $stock_sale_junction = $receipt->stock_sale_junction();
        $stock_sale = $stock_sale_junction->stock_sale();
        $credit_account = $receipt->credit_account();
    }
?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Stock Sales Receipt</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Receipt Date</label>
                            <input type="text" class="form-control datepicker" required name="receipt_date" value="<?= $edit ? $receipt->receipt_date : '' ?>" >
                            <input type="hidden" name="receipt_id" value="<?= $edit ? $receipt->{$receipt::DB_TABLE_PK} : '' ?>">
                            <input type="hidden" name="currency_id" value="<?= $edit ? $receipt->currency_id : '' ?>">
                        </div>
                        <div class="form-group col-md-4 ">
                            <label for="sales_id" class="control-label">Sales</label>
                            <?= form_dropdown('sales_id', $edit ? [$stock_sale->{$stock_sale::DB_TABLE_PK} => $stock_sale->sale_number()] : $sales_options, '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4 ">
                            <label for="currency_id" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id', $edit ? [$receipt->currency_id => $stock_sale->currency()->currency_name] : [], $edit ? $receipt->currency_id : '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="exchange_rate" class="control-label">Exchange Rate</label>
                            <input type="text" <?= !$edit || $receipt->currency_id ? 'readonly' : '' ?> class="form-control" name="exchange_rate" value="<?= $edit ? $receipt->exchange_rate : 1 ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="amount" class="control-label">Amount</label>
                            <input type="text" class="form-control number_format"  name="amount" value="<?= $edit ? $receipt->amount() : '' ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Vat</label>
                            <input type="text" class="form-control number_format" name="vat" value="<?= $edit ? $stock_sale_junction->vat : '' ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Credit Account</label>
                            <?= form_dropdown('credit_account_id', $edit ? [$credit_account->{$credit_account::DB_TABLE_PK} => $credit_account->account_name] : [], '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Debit Account</label>
                            <?= form_dropdown('debit_account_id', $debit_account_options, $edit ? $receipt->debit_account_id : '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="control-label">Reference</label>
                            <input type="text" class="form-control" name="reference" value="<?= $edit ? $receipt->reference : '' ?>" required>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="comments" class="control-label">Comments</label>
                            <textarea class="form-control" name="comments" ><?= $edit ? $receipt->remarks : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_sales_receipt">Save</button>
            </div>
        </form>
    </div>
</div>