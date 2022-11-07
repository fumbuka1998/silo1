<?php
    $edit = isset($contract)
?>
<div class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Contract Information</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="start_date" class="control-label">Start Date</label>
                            <input type="text" class="form-control datepicker" required name="start_date" value="<?= $edit ? $contract->start_date : '' ?>">
                            <input type="hidden" name="employee_id" value="<?= $edit ? $contract->employee_id : $employee->{$employee::DB_TABLE_PK} ?>">
                            <input type="hidden" name="contract_id" value="<?= isset($contract) ? $contract->{$contract::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="end_date" class="control-label">End Date</label>
                            <input type="text" class="form-control datepicker" required name="end_date" value="<?= $edit ? $contract->end_date : '' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="salary" class="control-label">Salary</label>
                            <input type="text" class="form-control number_format" required name="salary" value="<?= $edit ? $contract->salary : '' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="salary" class="control-label">Description</label>
                            <textarea class="form-control" name="description"><?= $edit ? $contract->description : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-default btn-sm save_contract_button" type="button">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>