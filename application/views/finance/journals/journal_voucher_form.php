<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 4/4/2019
 * Time: 5:49 PM
 */
?>

<div class="modal-dialog modal-lg">
    <form>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">New Journal Voucher</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">

                            <div class="form-group col-md-3">
                                <label for="transaction_date" class="control-label">Transaction Date</label>
                                <input type="text" class="form-control" name="transaction_date" value="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="transaction_type" class="control-label">Transaction Type</label>
                                <?= form_dropdown('transaction_type', [], '', 'class="form-control searchable"') ?>
                            </div>
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="remarks" class="control-label">Remarks</label>
                            <textarea class="form-control" name="remarks"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_jornal">Save</button>
            </div>
        </div>
    </form>
</div>
