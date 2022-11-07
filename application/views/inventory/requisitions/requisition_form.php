<?php
    $edit = isset($requisition);
    $material_options = material_item_options();
    $action_level = (isset($project) && $project->manager_access()  )|| !isset($project) ? 'second_step' : 'first_step';
    $initiating_step = $action_level == 'first_step';
    $cost_center_options = (isset($project) && $project) || ($edit && $location->project_id != null) ? $project->cost_center_options() : ['' => 'General Uses'];
?>
<div class="modal-dialog" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Requisition Form</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-3">
                        <label for="request_date" class="control-label">Request Date</label>
                        <input type="hidden" name="location_id" value="<?= $location->{$location::DB_TABLE_PK} ?>">
                        <input type="hidden" name="requisition_id" value="<?= $edit ? $requisition->{$requisition::DB_TABLE_PK} : '' ?>">
                        <input type="hidden" name="action_level" value="<?= $action_level ?>">
                        <input type="text" class="form-control datepicker" required name="request_date" value="<?= $edit ? ( $initiating_step ? $requisition->initiated_date : $requisition->request_date) : date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="required_date" class="control-label">Required Date</label>
                        <input type="text" class="form-control datepicker" name="required_date" value="<?= $edit && $requisition->required_date != null ? $requisition->required_date : '' ?>">
                    </div>
                </div>
                <div class="col-xs-12 table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr style="display: none;" class="material_row_template">
                            <td>
                                <div class="form-group col-xs-12">
                                    <label for="material_id" class="control-label">Material</label>
                                    <?= form_dropdown('material_id',$material_options,'',' class="form-control"') ?>
                                    <input type="hidden" name="item_type" value="material">
                                </div>

                                <div class="form-group col-xs-12">
                                    <label for="cost_center_id" class="control-label">Requesting For</label>
                                    <?= form_dropdown('cost_center_id',$cost_center_options, '',' class="form-control"') ?>
                                </div>
                            </td>
                            <td>
                                <div class="form-group col-xs-12">
                                    <label for="quantity" class="control-label">Quantity</label>
                                    <div class="input-group">
                                        <input type="text" name="quantity" class="form-control" >
                                        <span class="input-group-addon unit_display"></span>
                                    </div>
                                </div>
                                <?php if($action_level == 'second_step'){ ?>
                                <div class="form-group col-xs-12">
                                    <label for="vendor_id" class="control-label">Vendor</label>
                                    <?= form_dropdown('vendor_id',$vendor_options, '',' class="form-control vendor_id"') ?>
                                </div>
                            </td>
                            <td>
                                <div class="form-group col-xs-12 tons_capacity_form_group">
                                    <label for="rate" class="control-label">Rate</label>
                                    <input type="text" class="form-control number_format" name="rate" value="" required>
                                </div>
                                <div class="form-group col-xs-12 tons_capacity_form_group">
                                    <label for="rate" class="control-label">Amount</label>
                                    <input type="text" readonly class="form-control amount" name="amount" value="" required>
                                </div>
                                <div class="form-group col-xs-12 tons_capacity_form_group">
                                    <label for="rate" class="control-label">Currency</label>
                                    <?= form_dropdown('currency_id',$currency_options,'',' class="form-control"') ?>
                                </div>
                                <?php } ?>
                            </td>
                            <td>
                                <div class="form-group col-xs-12">
                                    <label for="remarks" class="control-label">Remarks</label>
                                    <textarea type="text" class="form-control remarks" rows="8" cols="60" name="remarks" value=""></textarea>
                                </div>
                            </td>
                            <td>
                                <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="6">
                                <button type="button" class="btn btn-default btn-xs pull-right material_row_adder">
                                    Add Row
                                </button>
                            </td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(!$edit){ ?>
                        <tr>
                            <td>
                                <div class="form-group col-xs-12">
                                    <label for="material_id" class="control-label">Material</label>
                                    <?= form_dropdown('material_id',$material_options,'',' class="form-control searchable"') ?>
                                    <input type="hidden" name="item_type" value="material">
                                </div>

                                <div class="form-group col-xs-12">
                                    <label for="cost_center_id" class="control-label">Requesting For</label>
                                    <?= form_dropdown('cost_center_id',$cost_center_options, '',' class="form-control searchable"') ?>
                                </div>
                            </td>
                            <td>
                                <div class="form-group col-xs-12">
                                    <label for="quantity" class="control-label">Quantity</label>
                                    <div class="input-group">
                                        <input type="text" name="quantity" class="form-control">
                                        <span class="input-group-addon unit_display"><?= $edit ? $unit_symbol : '&nbsp;' ?></span>
                                    </div>
                                </div>
                            <?php if($action_level == 'second_step'){ ?>
                                <div class="form-group col-xs-12">
                                    <label for="vendor_id" class="control-label">Vendor</label>
                                    <?= form_dropdown('vendor_id',$vendor_options, '',' class="form-control searchable vendor_id"') ?>
                                </div>
                            </td>
                            <td>
                                <div class="form-group col-xs-12 tons_capacity_form_group">
                                    <label for="rate" class="control-label">Rate</label>
                                    <input type="text" class="form-control number_format" name="rate" value="" required>
                                </div>
                                <div class="form-group col-xs-12 tons_capacity_form_group">
                                    <label for="rate" class="control-label">Amount</label>
                                    <input type="text" readonly class="form-control amount" name="amount" value="" required>
                                </div>
                                <div class="form-group col-xs-12 tons_capacity_form_group">
                                    <label for="rate" class="control-label">Currency</label>
                                    <?= form_dropdown('currency_id',$currency_options,'',' class="form-control"') ?>
                                </div>
                            <?php } ?>
                            </td>
                            <td>
                                <div class="form-group col-xs-12">
                                    <label for="remarks" class="control-label">Remarks</label>
                                    <textarea type="text" class="form-control remarks" rows="8" cols="60" name="remarks" value=""></textarea>
                                </div>
                            </td>
                            <td>
                                <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                            </td>
                        </tr>
                        <?php } else {
                            $material_items = $requisition->material_items();
                            foreach ($material_items as $item) {
                                $material = $item->material_item();
                                $unapproved_quantity = $requisition->status == 'INITIATED' ? $item->initiated_quantity : $item->requested_quantity;
                                ?>
                                <tr>
                                    <td>
                                        <div class="form-group col-xs-12">
                                            <label for="material_id" class="control-label">Material</label>
                                            <?= form_dropdown('material_id',$material_options,$item->material_item_id,' class="form-control searchable"') ?>
                                            <input type="hidden" name="item_type" value="material">
                                        </div>
 
                                        <div class="form-group col-xs-12">
                                            <label for="cost_center_id" class="control-label">Requesting For</label>
                                            <?= form_dropdown('cost_center_id',$cost_center_options, '',' class="form-control searchable"') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group col-xs-12">
                                            <label for="quantity" class="control-label">Quantity</label>
                                            <div class="input-group">
                                                <input type="text" name="quantity" value="<?= $unapproved_quantity ?>" class="form-control">
                                                <span class="input-group-addon unit_display"><?= $material->unit()->symbol ?></span>
                                            </div>
                                        </div>
                                        <?php if($action_level == 'second_step'){ ?>
                                        <div class="form-group col-xs-12">
                                            <label for="vendor_id" class="control-label">Vendor</label>
                                            <?= form_dropdown('vendor_id',$vendor_options, $item->requested_vendor_id,' class="form-control searchable vendor_id"') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group col-xs-12">
                                            <label for="rate" class="control-label">Rate</label>
                                            <input type="text" class="form-control number_format" name="rate" value="<?=  $item->requested_price  ?>" required>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label for="rate" class="control-label">Amount</label>
                                            <input type="text" readonly class="form-control amount" name="amount" value="<?= $item->requested_price * $item->requested_quantity ?>" required>
                                        </div>
                                        <div class="form-group col-xs-12">
                                            <label for="rate" class="control-label">Currency</label>
                                            <?= form_dropdown('currency_id',$currency_options,$item->requested_currency_id,' class="form-control"') ?>
                                        </div>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <div class="form-group col-xs-12">
                                            <label for="remarks" class="control-label">Remarks</label>
                                            <textarea type="text" class="form-control remarks" rows="5" name="remarks" value=""><?= $item->requesting_remarks ?></textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
                <div class="col-xs-12">
                    <div class="form-group">
                        <label for="comments" class="control-label">Comments</label>
                        <textarea name="comments" class="form-control"><?= $edit ? ( $initiating_step ? $requisition->initiating_comments : $requisition->requesting_comments) : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-default save_requisition">Submit</button>
        </div>
        </form>
    </div>
</div>