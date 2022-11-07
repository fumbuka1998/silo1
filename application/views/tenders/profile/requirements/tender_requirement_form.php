<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/27/2018
 * Time: 11:23 AM
 */
?>
<div class="modal-dialog">
    <form>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Tender Requirements</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="col-md-6">
                            <label for="tender_requirement_id control_label">Requirement Name</label>
                            <?= form_dropdown('tender_requirement_id',$requirement_type_options,  '', ' class="form-control searchable"') ?>
                            <input type="hidden" name="tender_id" value="<?= $tender->{$tender::DB_TABLE_PK} ?>">
                        </div>
                        <div class=" col-md-12">
                            <label for="descriptions" class="control-label">Descriptions</label>
                            <textarea class="form-control" name="description"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_requirement">Save</button>
            </div>
        </div>
    </form>
</div>
