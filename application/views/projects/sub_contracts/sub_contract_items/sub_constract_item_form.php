<?php
$sub_contract_id = $sub_contract->{$sub_contract::DB_TABLE_PK};
?>
<div class="modal-body">
    <div class='box-body '>
        <div class='row '>
            <form>
                <div class="form-group col-xs-12">
                    <div class="form-group col-md-6">
                        <label class="control-label">Task</label>
                        <?= form_dropdown('task_id', $cost_center_options, '', ' class="form-control searchable" ') ?>
                        <input type="hidden" name="sub_contract_id" value="<?= $sub_contract_id ?>">
                    </div>
                </div>


                <div class="form-group col-xs-12">
                    <div class="form-group col-md-2">
                        <label class="control-label"> Start Date</label>
                        <input type="text" name="start_date" class="form-control datepicker">
                    </div>

                    <div class="form-group col-md-2">
                        <label class="control-label">End Date</label>
                        <input type="text" name="end_date" class="form-control datepicker">
                    </div>

                    <div class="form-group col-md-4">
                        <label class="control-label">Description </label>
                        <input type="text" name="description" class="form-control " value="">
                    </div>
                    <div class="form-group col-md-2">
                        <label class="control-label">Amount</label>
                        <input type="text" name="contract_sum" class="form-control number_format" value="">
                    </div>
                    <div class="form-group col-md-1">
                        <label class="control-label">VAT+</label><br />
                        <input type="checkbox" name="vat_inclusive">
                    </div>


                    <div class="form-group col-md-1">
                        <label class="control-label"></label>
                        <button type="button" class="btn btn-sm btn-default btn-block save_sub_contract_item">Save</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <hr />
                <table class="table table-bordered table-hover table-striped sub_contract_contents_area " sub_contract_id="<?= $sub_contract->{$sub_contract::DB_TABLE_PK} ?>">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="4">Total</th>
                            <th class="total_sub_contract_amount_display" style="text-align: right"></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>