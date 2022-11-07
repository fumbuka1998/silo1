<?php
    $edit = isset($requisition);
    $material_options = material_item_options();
    $action_level = (isset($project) && $project->manager_access()  )|| !isset($project) ? 'second_step' : 'first_step';
    $tool_types_options = tool_types_options();
    $vendor_options = isset($vendor_options) ? $vendor_options : vendors_options();
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
                        <input type="text" class="form-control datepicker" required name="request_date" value="<?= $edit ? $requisition->request_date : date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="required_date" class="control-label">Required Date</label>
                        <input type="text" class="form-control datepicker" name="required_date" value="<?= $edit && $requisition->required_date != null ? $requisition->required_date : '' ?>">
                    </div>
                </div>
                <div class="col-xs-12 table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Material/Tool Type</th><th>Quantity</th><th>Unit</th>
                                <?php if($action_level == 'second_step'){ ?>
                                <th>Price</th>
                                    <th>Amount</th>
                                    <th>Currency</th>
                                <?php } ?>
                                <th>Requesting For</th><?php if($action_level == 'second_step'){ ?><th>Vendor</th><?php } ?><th>Remarks</th><th></th>
                            </tr>
                            <tr class="material_row_template" style="display: none">
                                <td width="20%">
                                    <?= form_dropdown('material_id',$material_options,'',' class="form-control"') ?>
                                    <input type="hidden" name="item_type" value="material">
                                </td>
                                <td width="10%"><input type="text" name="quantity" class="form-control"></td>
                                <td class="unit_display"></td>
                                <?php if($action_level == 'second_step'){ ?>
                                <td><input type="text" name="rate" class="form-control number_format"></td>
                                <td><input type="text" name="amount" readonly class="form-control number_format"></td>
                                <td><?= form_dropdown('currency_id',$currency_options,'',' class="form-control"') ?></td>
                                <?php } ?>
                                <td>
                                    <?= form_dropdown('cost_center_id',$cost_center_options, '',' class="form-control"') ?>
                                </td>
                                <?php if($action_level == 'second_step'){ ?>
                                <td style="width: 15%">
                                    <?= form_dropdown('vendor_id',$vendor_options, '',' class="form-control vendor_id"') ?>
                                </td>
                                <?php } else {
                                    ?>
                                    <input type="hidden" name="vendor_id" value="">
                                <?php
                                } ?>
                                <td><textarea name="remarks" rows="1" class="form-control"></textarea></td>
                                <td>
                                    <button type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>

                            <!--<tr class="tools_row_template" style="display: none">
                                <td width="20%">
                                    <?/*= form_dropdown('tool_type_id',$tool_types_options,'',' class="form-control"') */?>
                                    <input type="hidden" name="item_type" value="tool">
                                </td>
                                <td width="10%"><input type="text" name="quantity" class="form-control"></td>
                                <td>PCS</td>
                                <?php /*if($action_level == 'second_step'){ */?>
                                <td><input type="text" name="rate" class="form-control number_format"></td>
                                <td><input type="text" name="amount" readonly class="form-control number_format"></td>
                                <?php /*} */?>
                                <td>
                                    <?/*= form_dropdown('cost_center_id',$cost_center_options, '',' class="form-control"') */?>
                                </td>
                                <?php /*if($action_level == 'second_step'){ */?>
                                <td style="width: 15%">
                                    <?/*= form_dropdown('vendor_id',$vendors_options, '',' class="form-control vendor_id"') */?>
                                </td>
                                <?php /*} else {
                                    */?>
                                    <input type="hidden" name="vendor_id" value="">
                                    <?php
/*                                } */?>
                                <td><textarea name="remarks" rows="1" class="form-control"></textarea></td>
                                <td>
                                    <button type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>-->
                        </thead>
                        <tbody>
                            <?php if(!$edit){ ?>
                            <tr>
                                <td width="20%">
                                    <?= form_dropdown('material_id',$material_options,'',' class="form-control searchable"') ?>
                                    <input type="hidden" name="item_type" value="material">
                                </td>
                                <td width="10%"><input type="text" name="quantity" class="form-control"></td>
                                <td class="unit_display"></td>
                                <?php if($action_level == 'second_step'){ ?>
                                <td><input type="text"  name="rate" class="form-control number_format"></td>
                                <td><input type="text" name="amount" readonly class="form-control number_format"></td>
                                <td><?= form_dropdown('currency_id',$currency_options,'',' class="form-control"') ?></td>
                                <?php } else {
                                    ?>
                                    <input type="hidden" name="rate" value="0" class="number_format">
                                <?php
                                } ?>
                                <td>
                                    <?= form_dropdown('cost_center_id',$cost_center_options, '',' class="form-control searchable"') ?>
                                </td>
                                <?php if($action_level == 'second_step'){ ?>
                                <td style="width: 15%">
                                    <?= form_dropdown('vendor_id',$vendor_options, '',' class="form-control vendor_id searchable"') ?>
                                </td>
                                <?php } else {
                                    ?>
                                    <input type="hidden" class="vendor_id" value="">
                                    <?php
                                } ?>
                                <td><textarea name="remarks" rows="1" class="form-control"></textarea></td>
                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            <?php } else {
                                $material_items = $requisition->material_items();
                                foreach($material_items as $item){
                                    $material = $item->material_item();
                                    $unapproved_quantity = $requisition->status == 'INITIATED' ? $item->initiated_quantity : $item->requested_quantity;
                            ?>
                                    <tr>
                                        <td width="20%">
                                            <?= form_dropdown('material_id',$material_options,$item->material_item_id,' class="form-control searchable"') ?>
                                            <input type="hidden" name="item_type" value="material">
                                        </td>
                                        <td width="10%"><input type="text" name="quantity" value="<?= $unapproved_quantity ?>" class="form-control"></td>
                                        <td class="unit_display"><?= $material->unit()->symbol ?></td>
                                        <?php if($action_level == 'second_step'){ ?>
                                        <td><input type="text" name="rate" value="<?= $item->requested_price ?>" class="form-control number_format"></td>
                                        <td><input type="text" name="amount" readonly value="<?= $item->requested_quantity*$item->requested_price ?>" class="form-control number_format"></td>
                                        <td><?= form_dropdown('currency_id',$currency_options,'',' class="form-control"') ?></td>
                                        <?php } else {
                                            ?>
                                            <input type="hidden" name="rate" value="<?= $item->requested_price ?>" class="number_format">
                                        <?php
                                        } ?>
                                        <td>
                                            <?= form_dropdown('cost_center_id',$cost_center_options, $item->task_id,' class="form-control searchable"') ?>
                                        </td>
                                        <?php if($action_level == 'second_step'){ ?>
                                        <td style="width: 15%">
                                            <?= form_dropdown('vendor_id',$vendor_options, $item->requested_vendor_id,' class="form-control vendor_id"') ?>
                                        </td>
                                        <?php } else {
                                            ?>
                                            <input type="hidden" class="vendor_id" value="">
                                            <?php
                                        } ?>
                                        <td><textarea name="remarks" rows="1" class="form-control"><?= $item->requesting_remarks ?></textarea></td>
                                        <td>
                                            <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                            <?php
                                }

                                $tools_items = $requisition->tools_items();
                                foreach($tools_items as $item){
                                    $material = $item->tool_type();
                                    $unapproved_quantity = $requisition->status == 'INITIATED' ? $item->initiated_quantity : $item->requested_quantity;
                                    ?>
                                    <tr>
                                        <td width="20%">
                                            <?= form_dropdown('tool_type_id',$tool_types_options,$item->tool_type_id,' class="form-control searchable"') ?>
                                            <input type="hidden" name="item_type" value="tools">
                                        </td>
                                        <td width="10%"><input type="text" name="quantity" value="<?=  $unapproved_quantity ?>" class="form-control"></td>
                                        <td class="unit_display">PCS</td>
                                        <?php if($action_level == 'second_step'){ ?>
                                        <td><input type="text" name="rate" value="<?= $item->requested_price ?>" class="form-control number_format"></td>
                                        <td><input type="text" name="amount" readonly value="<?= $item->requested_quantity*$item->requested_price ?>" class="form-control number_format"></td>
                                        <?php } else {
                                            ?>
                                            <input type="hidden" name="rate" value="<?= $item->requested_price ?>" class="form-control">
                                        <?php
                                        } ?>
                                        <td>
                                            <?= form_dropdown('cost_center_id',$cost_center_options, $item->task_id,' class="form-control searchable"') ?>
                                        </td>
                                        <?php if($action_level == 'second_step'){ ?>
                                        <td style="width: 15%">
                                            <?= form_dropdown('vendor_id',$vendor_options, $item->requested_vendor_id,' class="form-control vendor_id"') ?>
                                        </td>
                                        <?php } else {
                                            ?>
                                            <input type="hidden" class="vendor_id" value="">
                                            <?php
                                        } ?>
                                        <td><textarea name="remarks" rows="1" class="form-control"><?= $item->requesting_remarks ?></textarea></td>
                                        <td>
                                            <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                            <?php
                                }
                            }?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th style="text-align: right" colspan="4">
                                    <?php if($action_level == 'second_step'){ ?>
                                    Total
                                    <?php } ?>
                                </th>
                                <?php if($action_level == 'second_step'){ ?>
                                <th  style="text-align: right" class="total_amount_display"></th>
                                <?php } ?>
                                <td colspan="4" style="text-align: right">
                                    <button type="button" class="btn btn-xs btn-default material_row_adder">
                                        <i class="fa fa-plus"></i> Material
                                    </button><!--
                                    <button type="button" class="btn btn-xs btn-default tools_row_adder">
                                        <i class="fa fa-plus"></i> Tools
                                    </button>-->
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-xs-12">
                    <div class="form-group">
                        <label for="comments" class="control-label">Comments</label>
                        <textarea name="comments" class="form-control"><?= $edit ? $requisition->requesting_comments : '' ?></textarea>
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