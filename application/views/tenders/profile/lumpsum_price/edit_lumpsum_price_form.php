<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/30/2018
 * Time: 11:55 AM
 */
?>
<div class="modal-dialog">

    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Edit Lumpsum Price Form</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="description" class="control-label">Description</label>
                            <input type="hidden" name="tender_lumpsum_price_id" value="<?= $lumpsum_price->{$lumpsum_price::DB_TABLE_PK} ?>">
                            <input type="text" class="form-control" required name="description" id = "description" value="<?= $lumpsum_price->description ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="amount" class="control-label">Amount</label>
                            <input type="text" class="form-control number_format" required name="amount" id = "amount" value="<?= $lumpsum_price->amount ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_edit_lumpsum_price">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

