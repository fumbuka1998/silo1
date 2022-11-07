<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Cash Requisition</h4>
        </div>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-4">
                        <label for="request_date" class="control-label">Request Date</label>
                        <input type="text" class="form-control datepicker" required name="request_date" value="">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="required_date" class="control-label">Required Date</label>
                        <input type="text" class="form-control datepicker" required name="required_date" value="">
                        <input type="hidden" name="account_id" value="<?= $account->{$account::DB_TABLE_PK} ?>">
                    </div>
                </div>
                <div class="col-xs-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Descriptions</th><th>Unit</th><th>Quantity</th><th>Price</th><th>Amount</th>
                                <td>
                                    <button class="btn btn-default btn-xs row_adder"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                            <tr class="row_template" style="display: none">
                                <td><input type="text" name="descriptions" class="form-control"></td>
                                <td><?= form_dropdown('unit_id',$measurement_unit_options,'',' class="form-control" ') ?></td>
                                <td><input type="text" name="quantity" class="form-control"></td>
                                <td><input type="text" name="rate" class="form-control number_format"></td>
                                <td><input type="text" readonly name="amount" class="form-control number_format"></td>
                                <td>
                                    <button class="btn btn-xs btn-default row_remover"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="descriptions" class="form-control"></td>
                                <td width="120px"><?= form_dropdown('unit_id',$measurement_unit_options,'',' class="form-control searchable" ') ?></td>
                                <td><input type="text" name="quantity" class="form-control"></td>
                                <td><input type="text" name="rate" class="form-control number_format"></td>
                                <td><input type="text" readonly name="amount" class="form-control number_format"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="form-group col-xs-12">
                        <label for="remarks" class="control-label">Remarks</label>
                        <textarea class="form-control" name="remarks"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-sm btn-default save_cash_requisition">Submit</button>
        </div>
    </div>
</div>