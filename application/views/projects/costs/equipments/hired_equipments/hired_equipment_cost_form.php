<?php

$edit = isset($hired_equipment_cost);

?>
<div class="modal-dialog">
    <div class="modal-content">
        <form class="equipment_cost_form">
            <div class="modal-header">
                <button type="button" class="close specific_modal_hide" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Hired Equipment cost</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
                            <label for="cost_center_id">Cost Center </label>
                            <?= form_dropdown( 'cost_center_id',$cost_center_options,
                                $edit ? $hired_equipment_cost->task_id :'',
                                ' class="form-control searchable" ' ) ?>

                            <input type="hidden" name="project_id" value="<?= $edit ? $hired_equipment_cost->project_id:$project->{$project::DB_TABLE_PK}?>">
                            <input type="hidden" name="hired_equipment_cost_id" value="<?= $edit ? $hired_equipment_cost->{$hired_equipment_cost::DB_TABLE_PK}:''?>">

                        </div>

                        <div class="form-group col-md-6">
                            <label for="asset_group_id" class="control-label">Equipment group </label>

                            <select name="asset_group_id"
                            <?= form_dropdown('asset_group_id',$asset_group_options, $edit ? $hired_equipment_cost->equipment()->asset_group_id: '', ' class="form-control searchable asset_group_selector" '
                                ) ?>
                            </select>

                        </div>
                        <div class="form-group col-md-6">
                            <label for="hired_equipment_id" class="control-label">Equipment name </label>
                            <select name="hired_equipment_id"
                            <?= form_dropdown('hired_equipment_id',$asset_item_options, $edit ? $hired_equipment_cost->hired_equipment_id: '', ' class="form-control searchable equipment_option_selector"'
                                ) ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="start_dae" class="control-label">Start Date </label>
                            <input type="text" name="start_date" class="form-control datepicker" value="<?= $edit ? $hired_equipment_cost->start_date: ''?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="end_date" class="control-label">End Date </label>
                            <input type="text" name="end_date" class="form-control datepicker" value="<?= $edit ? $hired_equipment_cost->end_date: ''?>">
                            </div>

                        <div class="form-group col-md-4">
                            <label for="rate_mode" class="control-label">Rate Mode</label>

                            <?php
                            $options = [
                                ''=>'',
                                'hourly' => 'Hourly',
                                'daily' => 'Daily'
                            ];
                            echo form_dropdown('rate_mode',$options,$edit ? $hired_equipment_cost->rate_mode : '', ' required class="form-control"')
                            ?>

                        </div>

                        <div class="form-group col-md-4">
                            <label for="rate" class="control-label">Rate</label>
                            <input class="form-control number_format" name="rate" value="<?= $edit ? $hired_equipment_cost->rate : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="amount" class="control-label">Amount</label>
                            <input class="form-control " readonly name="amount"  value="">
                        </div>

                        <div class="form-group col-xs-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $hired_equipment_cost->description : ''  ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"  class="btn btn-default btn-sm save_hired_equipment_cost_btn">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>