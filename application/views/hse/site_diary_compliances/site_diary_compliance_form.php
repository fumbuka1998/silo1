<?php

$edit = isset($site_diary_compliance);
$status_options = [
    '' => '',
    'Active' => 'Active',
    'Overdue' => 'Overdue',
    'Closed' => 'Closed'
];

?>

<div class="modal-dialog " style="width: 70%;">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $edit ? 'Edit Site Diary Compliance ' : 'Site Diary Compliance Register Form'?> </h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12 table-responsive">
                        <table id="" class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th> Date</th>
                                <th> Site</th>
                                <th>Supervisor</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td style="width: 20%;"><input type="text" name="date" value="<?= $edit ? $site_diary_compliance->date : '' ?>" class="form-control datepicker"></td>
                                <td style="width: 20%;"><?= form_dropdown('site_id', $projects_options, $edit ? $site_diary_compliance->site_id : '', ' class="form-control searchable" ') ?></td>
                                <td><?= form_dropdown('supervisor_id', employee_options(),  $edit ? $site_diary_compliance->supervisor_id : '', ' class="form-control searchable" ') ?></td>
                                <input type="hidden" name="site_diary_compliance_id" value="<?= $edit ? $site_diary_compliance->{$site_diary_compliance::DB_TABLE_PK} : ''?>">
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class='row'>
                    <div class="form-group col-md-12 col-sm-12">
                        <label  class="col-sm-3 control-label">Remarks</label>
                        <div class="col-sm-12">
                            <textarea name="remarks" class="form-control"><?= $edit ? $site_diary_compliance->remarks : ''?></textarea>
                        </div>
                    </div>
                </div>

                <div class='row '>
                    <div class="col-xs-12">

                        <table class="table table-bordered table-hover" width="100%" style="table-layout: fixed">
                            <thead>
                            <tr>
                                <th style="width: 40%">Site Work</th><th style="width: 05%">C</th><th style="width: 05%">N/C</th><th style="width: 05%">N/A</th><th style="width: 30%">Comments</th><th style="width: 5%"></th>
                            </tr>
                            <tr style="display: none" class="site_work_row_template">
                                <td style="width: 40%;">
                                    <textarea name="description" rows="1" class="form-control"></textarea>
                                </td>
                                <td style="width: 05%; padding-top: 20px;">
                                    <input type="checkbox" name="status" value="C" />
                                </td>
                                <td style="width: 05%; padding-top: 20px;">
                                    <input type="checkbox" name="status" value="N/C" />
                                </td>
                                <td style="width: 05%; padding-top: 20px;">
                                    <input type="checkbox" name="status" value="N/A" />
                                </td>
                                <td style="width: 30%">
                                    <textarea name="comments" class="form-control" rows="1"></textarea>
                                </td>
                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-sm btn-danger site_work_row_remover">
                                        <i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(!$edit) {
                           ?>
                            <tr>
                                <td style="width: 40%;">
                                    <textarea name="description" rows="1" class="form-control"></textarea>
                                </td>
                                <td style="width: 05%; padding-top: 20px;">
                                    <input type="checkbox" name="status" value="C" />
                                </td>
                                <td style="width: 05%; padding-top: 20px;">
                                    <input type="checkbox" name="status" value="N/C" />
                                </td>
                                <td style="width: 05%; padding-top: 20px;">
                                    <input type="checkbox" name="status" value="N/A" />
                                </td>
                                <td style="width: 30%">
                                    <textarea name="comments" class="form-control" rows="1"></textarea>
                                </td>
                                <td>
                                </td>
                            </tr>
                            <?php } else {
                                foreach ($site_diary_compliance->site_diary_complience_statuses() as $complience_status){
                                ?>
                            <tr>
                                <td style="width: 40%;">
                                    <textarea name="description" rows="1" class="form-control"><?= $complience_status->description ?></textarea>
                                </td>
                                <td style="width: 05%; padding-top: 20px;">
                                    <input type="checkbox" name="status" value="C" />
                                </td>
                                <td style="width: 05%; padding-top: 20px;">
                                    <input type="checkbox" name="status" value="N/C" />
                                </td>
                                <td style="width: 05%; padding-top: 20px;">
                                    <input type="checkbox" name="status" value="N/A" />
                                </td>
                                <td style="width: 30%">
                                    <textarea name="comments" class="form-control" rows="1"><?= $complience_status->comments ?></textarea>
                                </td>
                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-sm btn-danger site_work_row_remover">
                                        <i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            <?php
                                } }
                            ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="6">
                                    <button type="button" class="btn btn-default btn-xs site_work_row_adder pull-right">Add Site Work</button>
                                    <span class="pull-right">&nbsp;</span>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm save_site_diary_compliance">
                        Save
                    </button>
                </div>
        </form>
    </div>
</div>


