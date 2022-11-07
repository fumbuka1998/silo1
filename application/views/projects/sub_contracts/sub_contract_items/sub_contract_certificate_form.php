<?php
$sub_contract_id = $sub_contract->{$sub_contract::DB_TABLE_PK};
?>
<div class="modal-body">
    <div class='box-body '>
        <div class='row '>
            <form id="certifcate_form">
                <div class="col-md-12">
                    <div class="row col-md-12">
                        <div class="form-group col-md-2">
                            <label class="control-label">Certificate No</label>
                            <input type="text" name="certificate_number" class="form-control">
                            <input type="hidden" name="sub_contract_id" class="form-control" value="<?= $sub_contract_id ?>">
                        </div>
                        <div class="form-group col-md-2">
                            <label class="control-label ">Certificate Date</label>
                            <input type="text" name="certificate_date" class="form-control datepicker">
                        </div>
                        <div class="form-group col-md-8">
                            <label class="control-label ">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="1"></textarea>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover cert_tasks_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th style="width: 30%; text-align: right;">Amount</th>
                                        <th style="width: 3%;"></th>
                                    </tr>
                                    <tr style="display: none" class="row_template">
                                        <td>
                                            <?= form_dropdown('certified_task_id', [], '', ' class="certified_task_id form-control" ') ?>
                                        </td>
                                        <td>
                                            <input type="text" name="amount" class="form-control number_format" style="text-align: right">
                                        </td>
                                        <td>
                                            <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <?= form_dropdown('certified_task_id', [], '', ' class="certified_task_id form-control searchable" ') ?>
                                        </td>
                                        <td>
                                            <input type="text" name="amount" class="form-control number_format" style="text-align: right">
                                        </td>
                                        <td>
                                            <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th style="text-align: right;">TOTAL</th>
                                        <th style="text-align: right" class="total_amount_display">0.00</th>
                                        <th colspan="2">
                                            <div class="form-group col-md-6 ">
                                                <input type="checkbox" name="vat_inclusive">
                                                <label for="vat_inclusive" class="control-label text-left">VAT+</label>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="4">
                                            <span class="pull-right">
                                                <button type="button" class="btn btn-xs btn-default row_adder" initialized="true">
                                                    <i class="fa fa-plus"></i> Task
                                                </button>
                                            </span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="form-group col-xs-1">
                    <label class="control-label"></label>
                    <button type="button" class="btn btn-sm btn-default btn-block save_sub_contract_certificate">Save</button>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="col-xs-12 table-responsive">
                    <hr />
                    <table class="table table-bordered table-hover table-striped certificates_contents_area " sub_contract_id="<?= $sub_contract_id ?>" style="table-layout: fixed;">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Certificate No</th>
                                <th style="width: 10%;">Certificate Date</th>
                                <th>Remarks</th>
                                <th style="width: 20%;">Certified Amount</th>
                                <th style="width: 10%;"></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total</th>
                                <th class="total_certified_amount_display" style="text-align: right"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>