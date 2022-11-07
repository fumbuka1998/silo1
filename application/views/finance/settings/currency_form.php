<?php $edit = isset($currency) ?>
<div class="modal-dialog modal-sm">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Currency Info</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group  col-xs-12">
                        <label for="currency_name" class="control-label">Currency Name</label>
                        <input type="text" class="form-control" required name="currency_name" value="<?= $edit ? $currency->currency_name : '' ?>">
                        <input type="hidden" name="currency_id" value="<?= $edit ? $currency->{$currency::DB_TABLE_PK} : '' ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="symbol" class="control-label">Symbol</label>
                        <input type="text" class="form-control" maxlength="4" required name="symbol" value="<?= $edit ? $currency->symbol : '' ?>">
                    </div>
                    <?php if(!$edit){ ?>
                    <div class="form-group col-md-6">
                        <label for="rate_to_native" class="control-label">Rate To Native</label>
                        <input type="text" class="form-control exchange_rate" required name="rate_to_native" value="">
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-default save_currency">Save</button>
        </div>
        </form>
    </div>
</div>