
<?php
//$edit =isset($tax_rate_item);
?>
<div class="modal-dialog modal-lg">

    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Edit Tax Item</h4>
            </div>

            <div class="modal-body">
                <div class='row'>

                        <div class="form-group col-md-6">
                           <input type="hidden" name="tax_rate_item_id" value="">

                            <label for="minimum" class="control-label">Minimum</label>
                            <input type="text" class="form-control "  name="minimum" required value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="maximum" class="control-label">Maximum</label>
                            <input type="text" class="form-control " required name="maximum" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="rate" class="control-label">Rate(%)</label>
                            <input type="text" class="form-control " required name="rate" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="additional_amount" class="control-label">Additional Amount</label>
                            <input type="text" class="form-control "  name="additional_amount" value="">
                        </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm  save_tax_rate_item">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
