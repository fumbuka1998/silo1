<?php

$edit = isset($inspection);
$status_options = [
    '' => '',
    'Active' => 'Active',
    'Overdue' => 'Overdue',
    'Closed' => 'Closed'
];
if($edit) {
    $checked_params = [];

    foreach ($inspection->inspection_category()->inspection_category_parameters() as $param) {

        foreach ($param->inspection_category_parameter_type() as $param_type) {
            $checked_params[] = $param_type->parameter_type_id;
        }
    }
}

$checked_parameters = !empty($edit) ? $checked_params : [];
?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Inspection Form</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12 table-responsive">
                        <table id="" class="table table-striped table-hover" style="table-layout: fixed">
                            <thead>
                            <tr>
                                <th style="width: 14%">Inspection Date</th>
                                <th style="width: 25%">Inspector</th>
                                <th>Site</th>
                                <th style="width: 20%">Location</th>
                                <th style="width: 12%">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><input type="text" name="inspection_date" value="<?= $edit ? $inspection->inspection_date : '' ?>" class="form-control datepicker"></td>
                                <td><?= form_dropdown('inspector_id', $inspectors_options, $edit ? $inspection->inspector_id : '', ' class="form-control searchable" ') ?></td>
                                <td style="width: 20%;"><?= form_dropdown('site_id', $projects_options, $edit ? $inspection->site_id : '', ' class="form-control searchable" ') ?></td>
                                <td><input type="text" name="location" value="<?= $edit ? $inspection->location : '' ?>" class="form-control "></td>
                                <td><?= form_dropdown('status', $status_options, $edit ? $inspection->status : '', ' class="form-control searchable" ') ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $inspection->description : '' ?></textarea>
                            <input type="hidden" name="category_id" value="<?= $category_id ?>">
                            <input type="hidden" value="<?= $edit ? $inspection->{$inspection::DB_TABLE_PK} : '' ?>" name="inspection_id">
                            <input type="hidden" name="inspection_type" value="Site Inspection"/>
                        </div>
                    </div>
                </div>

                <div class='row '>
                    <div class="col-xs-12">
                        <?php
                        foreach ($category_parameters as $parameter) {
                            $parameter_id = $parameter->id;
                            ?>
                            <div class="form-group col-sm-12 mb-3 parameter_categories">
                                <input type="hidden" name="parameter_id" id="parameter_id" value="<?= $parameter_id ?>">
                                <div class="row col-xs-12">
                                    <button type="button" class="btn btn-default col-xs-12 mb-1" data-toggle="collapse" data-target="#parameter_id_<?= $parameter_id ?>"><?= $parameter->name ?></button>
                                    <div id="parameter_id_<?= $parameter_id ?>" class="collapse mt-2">
                                        <br/><br/>
                                        <p>
                                        <table class="table" style="width: 100%">
                                            <thead>
                                            <tr>
                                                <th style="width: 10px">SNO.</th>
                                                <th>Parameter Type</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                            </thead>
                                            <tbody id="table_parameter_type_<?= $parameter_id ?>">
                                            <?php
                                            $row = 1;
                                            foreach($parameter->parameter_types() as $parameter_type) {
                                                $parameter_type_id = $parameter_type->{$parameter_type::DB_TABLE_PK};
                                                ?>
                                                <tr>
                                                    <td><?= $row++ ?></td>
                                                    <td><?= $parameter_type->name ?></td>
                                                    <td style="text-align: right;font-weight: bold">
                                                        <input type="checkbox" class="checkbox" name="parameter_type_id" id="parameter_type_id_<?= $parameter_type_id ?>" value="<?= $parameter_type_id ?>"{{ in_array($parameter_type_id, $checked_parameters) ? 'checked' : '' }}>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm save_Inspection">
                        Save
                    </button>
                </div>
        </form>
    </div>
</div>