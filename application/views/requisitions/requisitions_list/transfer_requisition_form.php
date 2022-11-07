<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 5/3/2018
 * Time: 10:40 AM
 */

$edit = isset($requisition);

$main_location_options = isset($main_location_options) ? $main_location_options : locations_options('main');
$destination_location_0ption = locations_options('all');
$asset_options = asset_item_dropdown_options();
$measurement_unit_options = isset($measurement_unit_options) ? $measurement_unit_options : measurement_unit_dropdown_options();
$has_project = $edit ? $requisition->project_requisition() : false;

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

if($edit){
    $approval_module_options = $requisition->approval_module_id == '1' ? ['1' => 'General Requisition'] : ['2' => 'Project Requisition'];
    $material_options = $requisition->approval_module_id == '1' ? material_item_dropdown_options('all') : material_item_dropdown_options($project->category_id);
}

if(isset($project)){
    $approval_module_options['2'] = 'Project Requisition';
    $cost_center_options[$project->{$project::DB_TABLE_PK}] = $project->project_name;
    $requisition_cost_center_options = [$project->{$project::DB_TABLE_PK} => $project->project_name];
} else {
    $cost_center_options = $has_project ? $project->cost_center_options() : [];
}

?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Transfer Requisition Form</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12 top_fields">
                        <div class="form-group col-md-4">
                            <label for="request_date" class="control-label">Request Date</label>
                            <input type="hidden" name="requisition_id" value="<?= $edit ? $requisition->{$requisition::DB_TABLE_PK} : '' ?>">
                            <input type="text" class="form-control datepicker" required name="request_date" value="<?= $edit ? $requisition->request_date : date('Y-m-d') ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="required_date" class="control-label">Required Date</label>
                            <input type="text" class="form-control datepicker" name="required_date" value="<?= $edit && $requisition->required_date != null ? $requisition->required_date : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="requisition_type" class="control-label">Requisition Type</label>
                            <?= form_dropdown('approval_module_id',$approval_module_options, $edit ? $has_project ? 2 : 1 : '', ' class="form-control searchable" ') ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="cost_center_id" class="control-label">Requesting For</label>
                            <?= form_dropdown('requisition_cost_center_id', $requisition_cost_center_options, $requisition_cost_center_id, ' class="form-control searchable" ') ?>
                        </div>

                        <div class="form-group col-md-4 ">
                            <label for="rate" class="control-label">Source Location</label>
                            <?= form_dropdown('source_location_id',$main_location_options,$edit ? $requisition->source_sub_location_id : '',' class="form-control searchable"') ?>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="rate" class="control-label">Destination Location</label>
                            <?= form_dropdown('destination_location_id',$destination_location_0ption,'','class="form-control searchable"') ?>
                        </div>
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr style="display: none;" class="material_row_template">
                                <td>
                                    <?= form_dropdown('material_id', [],'',' class="form-control"') ?>
                                    <input type="hidden" name="item_type" value="material">
                                </td>

                                <td>
                                    <div class="input-group">
                                        <input type="text" name="quantity" class="form-control" >
                                        <span class="input-group-addon unit_display"></span>
                                    </div>
                                </td>

                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>

                            <tr style="display: none;" class="asset_row_template">
                                <td>
                                    <?= form_dropdown('asset_item_id',[],'',' class="form-control"') ?>
                                    <input type="hidden" name="item_type" value="asset">
                                </td>

                                <td></td>

                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>

                            <tr>
                                <th>Material/Description</th>
                                <th>Quantity</th>
                                <th></th>
                            </tr>

                            </thead>
                            <tbody>
                            <?php
                            if(!$edit){ ?>
                                <tr>
                                    <td>
                                        <?= form_dropdown('material_id', [],'',' class="form-control searchable"') ?>
                                        <input type="hidden" name="item_type" value="material">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" name="quantity" class="form-control">
                                            <span class="input-group-addon unit_display"><?= $edit ? $unit_symbol : '&nbsp;' ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                            <?php } else {
                                $material_items = $requisition->material_items();
                                foreach ($material_items as $item) {
                                    $grand_total += $requested_amount = $item->requested_rate*$item->requested_quantity;
                                    $material = $item->material_item();

                                    ?>
                                    <tr>
                                        <td style="width: 25%;">
                                            <?= form_dropdown('material_id',$material_options,$item->material_item_id,' class="form-control"') ?>
                                            <input type="hidden" name="item_type" value="material">
                                        </td>
                                        <td style="width:15%;">
                                            <div class="input-group">
                                                <input type="text" name="quantity" value="<?= $item->requested_quantity ?>" class="form-control">
                                                <span class="input-group-addon unit_display"><?= $material->unit()->symbol ?></span>
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
                        <div class="pull-right">
                            <button type="button" class="btn btn-default btn-xs material_row_adder">
                                Add Material Item
                            </button>

                            <button type="button" class="btn btn-default btn-xs asset_row_adder">
                                Add Asset Item
                            </button>
                        </div>
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
                <button type="button" class="btn btn-sm btn-default suspend_transfer_requisition">Suspend</button>
                <button type="button" class="btn btn-sm btn-default save_transfer_requisition">Submit</button>
            </div>
        </form>
    </div>
</div>


