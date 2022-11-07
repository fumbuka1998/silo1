<?php
$edit = isset($requisition);

$currency_options = isset($currency_options) ? $currency_options : currency_dropdown_options();
$forward_to_dropdown = isset($forward_to_dropdown) ? $forward_to_dropdown : [];

if ($edit) {
    $cost_center = $requisition->project();
    $requisition_cost_center_options = [$cost_center->{$cost_center::DB_TABLE_PK} => $cost_center->project_name];
    $requisition_cost_center_id = $cost_center->{$cost_center::DB_TABLE_PK};
    $grand_total = 0;
    $approval_module_options = [$requisition->approval_module_id => $requisition->approval_module()->module_name];
} else {
    $approval_module_options['2'] = 'Project Requisition';
    $requisition_cost_center_options = [$project->{$project::DB_TABLE_PK} => $project->project_name];
    $requisition_cost_center_id = $project->{$project::DB_TABLE_PK};
}

?>
<div class="modal-dialog" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Sub-Contracts Payment Requisition Form</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12 top_fields">
                        <div class="form-group col-md-2">
                            <label for="request_date" class="control-label">Request Date</label>
                            <input type="text" class="form-control datepicker" required name="request_date" value="<?= $edit ? $requisition->request_date : date('Y-m-d') ?>">
                            <input type="hidden" name="sub_contract_requisition_id" value="<?= $edit ? $requisition->{$requisition::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="required_date" class="control-label">Required Date</label>
                            <input type="text" class="form-control datepicker" name="required_date" value="<?= $edit && $requisition->required_date != null ? $requisition->required_date : '' ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="requisition_type" class="control-label">Requisition Type</label>
                            <?= form_dropdown('approval_module_id', $approval_module_options, $edit ? $requisition->approval_module_id : 2, ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-3 ">
                            <label for="cost_center_id" class="control-label">Requesting For</label>
                            <?= form_dropdown('requisition_cost_center_id', $requisition_cost_center_options, $requisition_cost_center_id, ' class="form-control searchable" ') ?>
                        </div>

                        <div class="form-group col-md-2 ">
                            <label for="rate" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id', $currency_options, $edit ? $requisition->currency_id : '', ' class="form-control searchable"') ?>
                        </div>
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-hover table-bordered table-responsive">
                            <thead>

                                <tr style="display: none;" class="row_template">
                                    <td>
                                        <?= form_dropdown('sub_contract_id', $sub_contract_options, '', ' class="form-control"') ?>
                                    </td>
                                    <td>
                                        <?= form_dropdown('certificate_id', [], '', ' class="form-control" ') ?>
                                    </td>
                                    <td>
                                        <input style="text-align: right" name="amount" class="form-control" value="">
                                        <input type="hidden" name="row_vat_amount" value="">
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                <tr>
                                    <th style="width: 353px">Sub Contractor</th>
                                    <th style="width: 353px">Certificate No.</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>

                            </thead>
                            <tbody>
                                <?php
                                if (!$edit) { ?>
                                    <tr>
                                        <td>
                                            <?= form_dropdown('sub_contract_id', $sub_contract_options, '', ' class="form-control "') ?>
                                        </td>
                                        <td style="width: 353px">
                                            <?= form_dropdown('certificate_id', [], '', ' class="form-control " ') ?>
                                        </td>
                                        <td>
                                            <input style="text-align: right" name="amount" class="form-control" value="">
                                            <input type="hidden" name="row_vat_amount" value="">
                                        </td>
                                        <td>
                                            <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                    <?php } else {
                                    $sub_contract_requisition_items = $requisition->sub_contract_requisition_items();
                                    foreach ($sub_contract_requisition_items as $item) {
                                        $certificate = $item->certificate();
                                        $sub_contract = $certificate->sub_contract();
                                        $grand_total += $item->requested_amount

                                    ?>
                                        <tr>
                                            <td>
                                                <?= form_dropdown('sub_contract_id', $sub_contract_options, $sub_contract->{$sub_contract::DB_TABLE_PK}, ' class="form-control searchable"') ?>
                                            </td>
                                            <td>
                                                <?= form_dropdown('certificate_id', $item->certificate()->drop_down_options(), $certificate->{$certificate::DB_TABLE_PK}, ' class="form-control searchable" ') ?>
                                            </td>
                                            <td>
                                                <input style="text-align: right" name="amount" class="form-control number_format" value="<?= $item->requested_amount ?>">
                                                <input type="hidden" name="row_vat_amount" value="<?= $edit && $requisition->vat_inclusive == 1 ? $item->requested_amount* $requisition->vat_percentage : '' ?>">
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
                                <?php
                                $vat_amount = 0;
                                if ($edit && $requisition->vat_inclusive == 1) {
                                    $vat_amount = $grand_total * 0.18;
                                    $grand_total += $vat_amount;
                                } ?>
                                <tr style="<?= $edit && $requisition->vat_inclusive != 1 ? 'display: none' : '' ?>" id="vat_amount_row">
                                    <td colspan="2" style="text-align: right">VAT</td>
                                    <td id="vat_amount_display" style="text-align: right;">
                                        <?php if ($edit && $requisition->vat_inclusive == 1) { ?>
                                            <span><?= number_format($vat_amount, 2) ?></span>
                                        <?php } ?>
                                    </td>
                                    <td><input type="hidden" name="vat_amount" value="<?= $edit && $requisition->vat_inclusive == 1 ? $vat_amount : '' ?>"></td>
                                </tr>

                                <tr class="text_styles">
                                    <th colspan="2" style="text-align: right">TOTAL</th>
                                    <th class="number_format total_amount_display" style="text-align: right">
                                        <?= $edit ? $grand_total : 0 ?></th>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-xs-12">
                        <div class="pull-right">
                            <button type="button" class="btn btn-default btn-xs row_adder">
                                <i class="fa fa-plus"></i> Certificate
                            </button>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group col-md-8">
                            <label for="comments" class="control-label">Comments</label>
                            <textarea name="comments" class="form-control"><?= $edit ? $requisition->requesting_comments : '' ?></textarea>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="foward_to" class="control-label ">Forward To</label>
                            <?= form_dropdown('foward_to', $forward_to_dropdown, $edit ? $requisition->foward_to : '', 'class="form-control searchable foward_to_options"') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_sub_contract_requisition">Submit</button>
            </div>
        </form>
    </div>
</div>