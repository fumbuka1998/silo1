<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Cash Requisition Approval</h4>
        </div>
        <div class="modal-body">
            <div class='row'>
                <div class="form-group col-md-3">
                    <label for="approved_date" class="control-label">Approved Date</label>
                    <input type="text" class="form-control datepicker" required name="approved_date" value="<?= date('Y-m-d') ?>">
                    <input type="hidden" name="requisition_id" value="<?= $requisition_id ?>">
                </div>
                <div class="col-xs-12 table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Description</th><th>Quantity</th><th>Rate</th><th>Amount</th><th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach ($material_items as $item){
    ?>
                                <tr>
                                    <td width="40%">
                                        <?= $item->description ?>
                                        <input name="item_id" type="hidden" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                    </td>
                                    <td><input name="quantity" class="form-control" value="<?= $item->requested_quantity ?>"></td>
                                    <td><input name="rate" class="form-control number_format" value="<?= $item->requested_rate ?>"></td>
                                    <td><input name="amount" readonly class="form-control number_format" value="<?= $item->requested_rate*$item->requested_quantity ?>"></td>
                                </tr>
                        <?php
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="form-group col-xs-12">
                    <label for="remarks" class="control-label">Remarks</label>
                    <textarea class="form-control" name="remarks"></textarea>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-sm btn-default approve_cash_requisition">
                Save
            </button>
        </div>
    </div>
</div>