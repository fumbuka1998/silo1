<?php
$edit = isset($item);
?>
<div class="modal-dialog">
    <div class="modal-content">
        <form class="equipment_budget_form">
            <div class="modal-header">
                <button type="button" class="close specific_modal_hide" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Equipment Budget</h4>
            </div>
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
                            <label for="ownership" class="control-label">Ownership</label>

                            <?php
                            $options = [
                                'owned' => 'Owned',
                                'hired' => 'Hired'
                            ];
                            echo form_dropdown('ownership',$options,$edit ? $item->ownership : '', ' required class="form-control"')
                            ?>


                        </div>
                        <div class="form-group col-md-4">
                            <label for="" class="control-label">Equipment Type</label>
                            <select name="equipment_type_id"
                                    class="form-control <?= $edit ? '' : 'searchable equipment_type_selector' ?>">
                                <?php
                                if($edit){
                                    ?>
                                    <option value="<?= $item->equipment_type_id ?>" selected><?= $equipment_type->name ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <input type="hidden" name="item_id" value="<?= $edit ? $item->{$item::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="days" class="control-label">Days</label>
                            <input type="text" name="days" class="form-control"  value="<?= $edit ? $item->days : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="quantity" class="control-label">Quantity</label>
                            <input type="text" name="quantity" class="form-control"  value="<?= $edit ? $item->quantity : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="rate" class="control-label">Rate Per day</label>
                            <input class="form-control number_format" name="rate" value="<?= $edit ? $item->rate : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="amount" class="control-label">Amount</label>
                            <input class="form-control number_format" readonly name="amount"  value="<?= $edit ? $item->rate*$item->quantity : '' ?>">
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $item->description : ''  ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"  class="btn btn-default btn-sm save_equipment_budget_item">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>