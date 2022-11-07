<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/30/2018
 * Time: 2:16 PM
 */

$material_options = material_item_dropdown_options();
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">


                <?php if (check_privilege('Project Actions')) { ?>
                    <div class="row">
                        <form>
                            <div class="form-group col-xs-3">
                                <label for="project_plan_task_id" class="control-label">Task Name</label>
                                <?= form_dropdown('project_plan_task_id', [], '', 'class="form-control project_plan_tasks_display"') ?>
                            </div>
                            <div class="form-group col-xs-2">
                                <label for="material_item_id" class="control-label">Item Name</label>
                                <?= form_dropdown('material_item_id', $material_options, '', 'class="form-control "') ?>
                            </div>
                            <div class="form-group col-xs-2">
                                <label for=quantity" class="control-label">Quantity</label>
                                <div class="input-group">
                                    <span class="input-group-addon unit_display"></span>
                                    <input type="text" class="form-control " name="quantity" value="">
                                </div>
                                <input type="hidden" name="project_plan_id" value="<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>">
                                <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for=rate" class="control-label">Rate</label>
                                <input type="text" class="form-control number_format" name="rate" value="">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="amount" class="control-label">Amount</label>
                                <input type="text" class="form-control number_format" name="amount" readonly>
                            </div>

                            <div class="form-group col-xs-1">
                                <label class="control-label"></label>
                                <button type="button" class="btn btn-sm btn-default btn-block submit_plan_material_budget" >Save</button>
                            </div>
                        </form>
                    </div>

                <?php
                }
                ?>


                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <hr/>
                            <table class="table table-bordered table-hover table-striped plan_materials_budget_list" project_plan_id="<?= $project_plan->{$project_plan::DB_TABLE_PK} ?>">
                                <thead>
                                <tr>
                                    <th>Task Name</th>
                                    <th>Item Name</th>
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                    <th>Rate</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th colspan="5">Total</th>
                                    <th class="total_plan_material_budget_display" style="text-align: right"></th>
                                    <th ></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>