<?php
$edit = isset($item)
?>
<div class="modal-dialog modal-lg" style="width: 90%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Permanent Labour Cost</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-md-6">
                    <div class="form-group col-xs-12">
                        <label for="cost_center_id">Cost Center </label>
                        <?= form_dropdown('cost_center_id',$cost_center_options,
                            $edit ? isset($item->task_id) ? $item->task_id : '' : '',
                            ' class="form-control searchable" '
                        ) ?>
                        <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>"/>
                    </div>
                </div>
                <div class="col-xs-12 table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Employee</th><th>Duration Mode</th><th>Dates</th><th>Duration</th><th>Allowance</th></th><th>Description</th><th></th>
                            </tr>
                            <tr class="row_template" style="display: none">
                                <td><?= form_dropdown('member_id',$team_member_options,'',' class="form-control" ') ?></td>
                                <td><?= form_dropdown('working_mode', [
                                        'date_range' => 'Date Range',
                                        'hours' => 'Hours',
                                        'single_day' => 'One Day'
                                    ],'',' class="form-control" ') ?>
                                </td>
                                <td>
                                    <span class="date_range_input">
                                        <input type="text" name="start_date" placeholder="From" class="form-control datepicker"><br/>
                                        <input type="text" name="end_date" placeholder="To" class="form-control datepicker">
                                    </span>
                                    <span style="display: none" class="single_date_input">
                                        <input type="text" name="cost_date" placeholder="Cost Date" class="form-control datepicker">
                                    </span>
                                </td>
                                <td>
                                    <input type="number" readonly step="any" name="duration" class="form-control">
                                    <input type="hidden" step="any" name="salary_rate">
                                </td>
                                <td>
                                    <input name="allowance" class="form-control number_format">
                                </td>
                                <td>
                                    <textarea name="description" class="form-control"></textarea>
                                </td>
                                <td>
                                    <span class="pull-right">
                                        <button type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </span>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= form_dropdown('member_id',$team_member_options,'',' class="form-control" ') ?></td>
                                <td><?= form_dropdown('working_mode', [
                                        'date_range' => 'Date Range',
                                        'hours' => 'Hours',
                                        'single_day' => 'One Day'
                                    ],'',' class="form-control" ') ?>
                                </td>
                                <td nowrap="nowrap">
                                    <span class="date_range_input">
                                        <input type="text" name="start_date" placeholder="From" class="form-control datepicker"><br/>
                                        <input type="text" name="end_date" placeholder="To" class="form-control datepicker">
                                    </span>
                                    <span style="display: none" class="single_date_input">
                                        <input type="text" name="cost_date" placeholder="Cost Date" class="form-control datepicker">
                                    </span>
                                </td>
                                <td>
                                    <input type="number" readonly step="any" name="duration" class="form-control">
                                    <input type="hidden" step="any" name="salary_rate">
                                </td>
                                <td>
                                    <input name="allowance" class="form-control number_format">
                                </td>
                                <td>
                                    <textarea name="description" class="form-control"></textarea>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>

                        <tfoot>
                        <tr>
                            <th colspan="6"></th>
                            <th>
                                <button type="button" class="btn btn-xs btn-default row_adder"><i class="fa fa-plus"></i> Add Row</button>
                            </th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm save_permanent_labour_cost">Save</button>
        </div>
        </form>
    </div>
</div>