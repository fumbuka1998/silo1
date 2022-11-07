<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/27/2018
 * Time: 11:31 AM
 */
?>
<div class="modal-dialog">
    <form>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Tender Submission Form</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="col-md-6">
                            <label for="date_procured control_label">Date Submitted</label>
                            <input type="text" class="form-control datepicker" required name="date_submitted" value="">
                        </div>
                        <div class="col-md-6">
                            <label for="date_procured control_label">Supervisor Name</label>
                            <input type="text" class="form-control " required name="supervisor_name" value="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_submission">Save</button>
            </div>
        </div>
    </form>
</div>


