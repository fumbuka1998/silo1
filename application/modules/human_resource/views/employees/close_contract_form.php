<?php
$edit = isset($employee_contract);
/*
              employee_contract_id  ...............hidden
              close_date
              reason
              attachment

              */

?>
<div class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Close Contract Information</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="close_date" class="control-label">Close Date</label>
                            <input type="text" class="form-control datepicker" required name="close_date" value="">

                            <input type="hidden" name="employee_contract_id" value="<?= $edit ? $employee_contract->{$employee_contract::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="end_date" class="control-label">Attachment</label>
                            <input type="file" class="form-control "  name="attachment" value="">
                        </div>
                        <div class="form-group col-md-12">

                            <textarea name="reason" class="form-control" placeholder="Reason for close.."> </textarea>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-default btn-sm close_contract_button" type="button">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>