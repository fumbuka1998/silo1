<?php
$edit = isset($item);
?>


<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Permanent Labour Budget</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
                            <label for="cost_center_id">Cost Center </label>
                            <?= form_dropdown('cost_center_id',$cost_center_options,
                                $edit ? isset($item->task_id) ? $item->task_id : '' : '',
                                ' class="form-control searchable" '
                            ) ?>
                            <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="labor_type_id" class="control-label">Labour Type</label>
                            <select name="casual_labour_type_id" class="form-control <?= $edit ? '' : ' searchable budget_casual_labour_type_selector' ?>">
                                <?php
                                if($edit){
                                    ?>
                                    <option value="<?= $item->casual_labour_type_id ?>" selected><?= $casual_labour_type->name ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <input type="hidden" name="item_id" value="<?= $edit ? $item->{$item::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="rate_mode" class="control-label">Rate Mode</label>
                            <?php
                            $options = [
                                'hourly' => 'Hourly',
                                'daily' => 'Daily'
                            ];
                            echo form_dropdown('rate_mode',$options,$edit ? $item->rate_mode : '', ' required class="form-control"')
                            ?>


                        </div>
                        <div class="form-group col-md-4">
                            <label for="duration" class="control-label">Duration</label>
                            <input type="text" name="duration" class="form-control"  value="<?= $edit ? $item->duration : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="allowance_rate" class="control-label">Rate</label>
                            <input class="form-control number_format" name="rate" value="<?= $edit ? $item->rate : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="no_of_staff" class="control-label">No of Workers</label>
                            <input type="text" name="no_of_workers" class="form-control"  value="<?= $edit ? $item->no_of_workers : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="amount" class="control-label">Total Amount</label>
                            <input class="form-control number_format" readonly name="total_amount"  value="<?= $edit ? $item->rate*$item->no_of_workers : '' ?>">
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $item->description : ''  ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_casual_labour_budget_item">Save</button>
            </div>
        </form>
    </div>
</div>