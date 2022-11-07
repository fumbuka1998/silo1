<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/27/2018
 * Time: 12:31 AM
 */
$edit = isset($tender);
$modal_heading = $edit ? $tender->tender_name : 'New Tender';
?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $modal_heading ?></h4>
            </div>
            <form>
                <div class="modal-body">
                    <div class='row'>
                        <div class="col-xs-12">
                            <div class="col-md-4">
                                <label for="tender_name control_label">Tender Name</label>
                                <input type="text" class="form-control" required name="tender_name" value="<?= $edit ? $tender->tender_name : '' ?>">
                                <input type="hidden" name="tender_id" value="<?= $edit ? $tender->{$tender::DB_TABLE_PK} : ''?>">
                            </div>
                            <div class="col-md-4">
                                <label for="project_category_id control_label">Project Category</label>
                                <?= form_dropdown('project_category_id',$project_categories,  $edit ? $tender->project_category_id : '', ' class="form-control searchable"') ?>
                            </div>
                            <div class="col-md-4">
                                <label for="client_id control_label">Client</label>
                                <?= form_dropdown('client_id',$client_options,  $edit ? $tender->client_id : '', ' class="form-control searchable"') ?>
                            </div>
                            <div class="col-md-4">
                                <label for="date_announced control_label">Date Announced</label>
                                <input type="text" class="form-control datepicker" required name="date_announced" value="<?= $edit ? $tender->date_announced : date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="submission_deadline control_label">Submission Deadline</label>
                                <input type="text" class="form-control datepicker" required name="submission_deadline" value="<?= $edit ? $tender->submission_deadline : date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="date_procured control_label">Date Procured</label>
                                <input type="text" class="form-control datepicker" required name="date_procured" value="<?= $edit ? $tender->date_procured : date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="procurement_cost control_label">Procurement Cost</label>
                                <input type="text" class="form-control  number_format" required name="procurement_cost" value="<?= $edit ? $tender->procurement_cost : '' ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="procurement_currency_id control_label">Procurement Currency</label>
                                <?= form_dropdown('procurement_currency_id',$currency_options,  $edit ? $tender->procurement_currency_id : '', ' class="form-control searchable"') ?>
                            </div>
                            <div class="col-md-4">
                                <label for="supervisor_id control_label">Supervisor</label>
                                <?= form_dropdown('supervisor_id',$supervisors_options,  $edit ? $tender->supervisor_id : '', ' class="form-control searchable"') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-default save_tender">Save</button>
            </div>
        </form>
    </div>
</div>
