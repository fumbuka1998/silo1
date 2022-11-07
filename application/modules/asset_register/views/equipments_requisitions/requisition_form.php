<?php
    $edit = isset($requisition);
    $material_options = material_item_dropdown_options();
    $asset_group_options = asset_group_dropdown_options();
    $vendor_options = isset($vendor_options) ? $vendor_options : vendor_dropdown_options();
    $currency_options = isset($currency_options) ? $currency_options : currency_dropdown_options();
    $measurement_unit_options = isset($measurement_unit_options) ? $measurement_unit_options : measurement_unit_dropdown_options();
    $has_project = $edit ? $requisition->project_requisition() : false;

    if($edit){
        $grand_total= 0;
        $approval_module_options = $requisition->approval_module_id == '1' ? ['1' => 'General Requisition'] : ['2' => 'Project Requisition'];
    }
    if($has_project){
        $project = $has_project->project();
        $requisition_cost_center_options = [$project->{$project::DB_TABLE_PK} => $project->project_name];
        $requisition_cost_center_id = $has_project->project_id;
    } else if($edit){
        $cost_center = $requisition->cost_center_requisition()->cost_center();
        $requisition_cost_center_options = [$cost_center->{$cost_center::DB_TABLE_PK} => $cost_center->cost_center_name];
        $requisition_cost_center_id = $cost_center->{$cost_center::DB_TABLE_PK};
    } else {
        $requisition_cost_center_options = [];
        $requisition_cost_center_id = '';
    }
    $cost_center_options = $has_project ? $project->cost_center_options() : [];
?>
<div class="modal-dialog" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Equipment Requisition Form</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-3">
                        <label for="request_date" class="control-label">Request Date</label>
                        <input type="hidden" name="requisition_id" value="<?= $edit ? $requisition->{$requisition::DB_TABLE_PK} : '' ?>">
                        <input type="text" class="form-control datepicker" required name="request_date" value="<?= $edit ? $requisition->request_date : date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="required_date" class="control-label">Required Date</label>
                        <input type="text" class="form-control datepicker" name="required_date" value="<?= $edit && $requisition->required_date != null ? $requisition->required_date : '' ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="requisition_type" class="control-label">Requisition Type</label>
                        <?= form_dropdown('approval_module_id',$approval_module_options, $edit ? $has_project ? 2 : 1 : '', ' class="form-control" ') ?>
                    </div>
                    <div class="form-group col-md-3 ">
                        <label for="cost_center_id" class="control-label">Requesting For</label>
                        <?= form_dropdown('requisition_cost_center_id', $requisition_cost_center_options, $requisition_cost_center_id, ' class="form-control searchable" ') ?>
                    </div>

                    <div class="form-group col-md-3 ">
                        <label for="rate" class="control-label">Currency</label>
                        <?= form_dropdown('currency_id',$currency_options,'',' class="form-control"') ?>
                    </div>
                </div>
                <div class="col-xs-12 table-responsive">
                    <table class="table table-hover">
                        <thead>
                      
                        <tr style="display: none;" class="material_row_template">
                            <td>
                                <div class="form-group col-xs-12">
                                    <label for="asset_group_id" class="control-label">Asset Group</label>
                                    <?= form_dropdown('asset_group_id',$asset_group_options,'',' class="form-control"') ?>
                                    <input type="hidden" name="item_type" value="equipment">
                                </div>

                                <div class="form-group col-xs-12">
                                    <label for="cost_center_id" class="control-label">Expense Account</label>
                                    <?= form_dropdown('expense_account_id',$expense_accounts_options, '',' class="form-control"') ?>
                                </div>

                            </td>
                            <td>
                             
                                <div class="form-group col-xs-6">
                                    <label for="quantity" class="control-label">Quantity</label>
                                    <input type="text" name="quantity" class="form-control" >
                                </div>

                                <div class="form-group col-md-6">

                                        <?php  $rate_options=['daily'=>'Daily','hourly'=>'Hourly'];?>

                                        <?php
                                            echo form_label('Rate Mode','rate_mode');
                                            echo form_dropdown('rate_mode', $rate_options,'', " class = ' form-control' required ");
                                        ?>

                                </div>


                                <div class="form-group col-xs-6">
                                    <label for="vendor_id" class="control-label">Recommend Vendor</label>
                                    <?= form_dropdown('vendor_id',$vendor_options, '',' class="form-control vendor_id"') ?>
                                </div>

                                <div class="form-group col-xs-6">
                                    <label for="duration" class="control-label">Duration</label>
                                    <input type="number" name="duration" min="0" class="form-control" >
                                </div>
                            </td>
                            <td>
                                <div class="form-group col-xs-12 ">
                                    <label for="rate" class="control-label">Rate</label>
                                    <input type="text" class="form-control money" name="rate" value="" required>
                                </div>

                                <div
                                    <?php if(!$has_project){ ?>
                                        style="display: none;"
                                    <?php } ?>

                                    class="form-group col-xs-12 cost_center_form_group">
                                    <label for="cost_center_id" class="control-label">Cost Center</label>
                                    <?= form_dropdown('cost_center_id',[], '',' class="form-control"') ?>
                                </div>

                            </td>
                            <td>

                                <div class="form-group col-xs-12 ">
                                    <label for="rate" class="control-label">Amount</label>
                                    <input type="text" readonly class="form-control amount" name="amount" value="" required>
                                </div>
                            </td>
                            <td>
                                <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                            </td>
                        </tr>

                        </thead>
                        <tbody>
                        <?php if(!$edit){ ?>
                        <tr>
                            <td>
                                <div class="form-group col-xs-12">
                                    <label for="asset_group_id" class="control-label">Asset Group</label>
                                    <?= form_dropdown('asset_group_id',$asset_group_options,'',' class="form-control searchable"') ?>
                                    <input type="hidden" name="item_type" value="equipment">
                                </div>

                                <div class="form-group col-xs-12">
                                    <label for="expense_account_id" class="control-label">Expense Account</label>
                                    <?= form_dropdown('expense_account_id',$expense_accounts_options, '',' class="form-control searchable"') ?>
                                </div>
                            </td>
                            <td>
                              
                                 <div class="form-group col-xs-6">
                                    <label for="quantity" class="control-label">Quantity</label>
                                    <input type="text" name="quantity" class="form-control" >
                                </div>

                                <div class="form-group col-md-6">

                                        <?php  $rate_options=['daily'=>'Daily','hourly'=>'Hourly'];?>

                                        <?php
                                            echo form_label('Rate Mode','rate_mode');
                                            echo form_dropdown('rate_mode', $rate_options,'', " class = ' form-control searchable' required ");
                                        ?>

                                </div>

                                <div class="form-group col-xs-6">
                                    <label for="vendor_id" class="control-label">Recommend Vendor</label>
                                    <?= form_dropdown('vendor_id',$vendor_options, '',' class="form-control searchable vendor_id"') ?>
                                </div>
                                 <div class="form-group col-xs-6">
                                    <label for="duration" class="control-label">Duration</label>
                                    <input type="number" name="duration" min="0" class="form-control" >
                                </div>

                            </td>

                            <td>
                                <div class="form-group col-xs-12">
                                    <label for="rate" class="control-label">Rate</label>
                                    <input type="text" class="form-control money" name="rate" value="" required>
                                </div>

                                <div style="display: none;" class="form-group col-xs-12 cost_center_form_group">
                                    <label for="cost_center_id" class="control-label">Cost Center</label>
                                    <?= form_dropdown('cost_center_id',[], '',' class="form-control searchable"') ?>
                                </div>
                            </td>
                            <td>
                                <div class="form-group col-xs-12">
                                    <label for="rate" class="control-label">Amount</label>
                                    <input type="text" readonly class="form-control amount" name="amount" value="" required>
                                </div>
                            </td>
                            <td>
                                <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                            </td>
                        </tr>
                        <?php } else {
                            $equipment_items = $requisition->equipment_items();
                            foreach ($equipment_items as $item) {
                                $grand_total += $requested_amount = $item->requested_rate*$item->requested_quantity;
                                $material = $item->asset_group();
                                ?>
                                <tr>
                                    <td>
                                        <div class="form-group col-xs-12">
                                            <label for="asset_group_id" class="control-label">Asset Group</label>
                                            <?= form_dropdown('asset_group_id',$asset_group_options,$item->asset_group_id,' class="form-control searchable"') ?>
                                            <input type="hidden" name="item_type" value="equipment">
                                        </div>
 
                                        <div class="form-group col-xs-12">
                                            <label for="expense_account_id" class="control-label">Expense Account</label>
                                            <?= form_dropdown('expense_account_id',$expense_accounts_options, $item->expense_account_id,' class="form-control searchable"') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group col-xs-6">
                                            <label for="quantity" class="control-label">Quantity</label>
                                           
                                             <input type="text" name="quantity" value="<?= $item->requested_quantity ?>" class="form-control">
                                                
                                        </div>

                                        <div class="form-group col-md-6">

                                        <?php  $rate_options=['daily'=>'Daily','hourly'=>'Hourly'];?>

                                        <?php
                                            echo form_label('Rate Mode','rate_mode');
                                            echo form_dropdown('rate_mode', $rate_options,$item->rate_mode," class = ' form-control searchable' required ");
                                        ?>

                                       </div>

                                         

                                        <div class="form-group col-xs-6">
                                            <label for="vendor_id" class="control-label">Recommend Vendor</label>
                                            <?= form_dropdown('vendor_id',$vendor_options, $item->requested_vendor_id,' class="form-control searchable vendor_id"') ?>
                                        </div>

                                    <div class="form-group col-xs-6">
                                        <label for="duration" class="control-label">Duration</label>
                                        <input type="number" name="duration" min="0" value="<?=  $item->duration  ?>" class="form-control" >
                                    </div>

                                    </td>
                                    <td>
                                        <div class="form-group col-xs-12">
                                            <label for="rate" class="control-label">Rate</label>
                                            <input type="text" class="form-control number_format" name="rate" value="<?=  $item->requested_rate  ?>" required>
                                        </div>

                                        <div
                                            <?php if(!$has_project){ ?>
                                                style="display: none;"
                                            <?php } ?>

                                            class="form-group col-xs-12 cost_center_form_group">
                                            <label for="cost_center_id" class="control-label">Cost Center</label>
                                            <?= form_dropdown('cost_center_id',$cost_center_options, '',' class="form-control searchable"') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group col-md-6">
                                            <label for="rate" class="control-label">Amount</label>
                                            <input type="text" readonly class="form-control number_format amount" name="amount" value="<?= $requested_amount ?>" required>
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
                        <tr>
                            <th>TOTAL</th>
                            <th colspan="3" class="number_format total_amount_display" style="text-align: right"><?= $edit ? $grand_total : 0 ?></th>
                        </tr>
                        <tr>
                            <td style="text-align: right" colspan="6">
                                <button type="button" class="btn btn-default btn-xs material_row_adder">
                                    Add Row
                                </button>
                                &nbsp;&nbsp;
                               
                            </td>
                            <td></td>
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
            <button type="button" class="btn btn-sm btn-default save_equipment_requisition">Submit</button>
        </div>
        </form>
    </div>
</div>