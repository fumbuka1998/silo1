<?php
$edit = isset($requisition);

$main_location_options = isset($main_location_options) ? $main_location_options : locations_options('main');
$asset_options = asset_item_dropdown_options();
$stakeholder_options = isset($stakeholder_options) ? $stakeholder_options : stakeholder_dropdown_options();
$accounts_options = isset($accounts_options) ? $accounts_options : account_dropdown_options(['CASH IN HAND']);
$currency_options = isset($currency_options) ? $currency_options : currency_dropdown_options();
$measurement_unit_options = isset($measurement_unit_options) ? $measurement_unit_options : measurement_unit_dropdown_options();
$forward_to_dropdown = isset($forward_to_dropdown) ? $forward_to_dropdown : [];

$has_project = $edit ? $requisition->project_requisition() : false;

if ($has_project) {
    $project = $has_project->project();
    $requisition_cost_center_options = [$project->{$project::DB_TABLE_PK} => $project->project_name];
    $requisition_cost_center_id = $has_project->project_id;
} else if ($edit) {
    $cost_center = $requisition->cost_center_requisition()->cost_center();
    $requisition_cost_center_options = [$cost_center->{$cost_center::DB_TABLE_PK} => $cost_center->cost_center_name];
    $requisition_cost_center_id = $cost_center->{$cost_center::DB_TABLE_PK};
    $accounts_options = isset($accounts_options) ? $accounts_options : account_dropdown_options(['CASH IN HAND']);
} else {
    $requisition_cost_center_options = [];
    $requisition_cost_center_id = '';
}

if ($edit) {
    $total_amount = 0;
    $approval_module_options = $requisition->approval_module_id == '1' ? ['1' => 'General Requisition'] : ['2' => 'Project Requisition'];
    $material_options = $requisition->approval_module_id == '1' ? material_item_dropdown_options('all') : material_item_dropdown_options('all');
}


if (isset($project)) {
    $approval_module_options['2'] = 'Project Requisition';
    $cost_center_options[$project->{$project::DB_TABLE_PK}] = $project->project_name;
    $requisition_cost_center_options = [$project->{$project::DB_TABLE_PK} => $project->project_name];
} else {
    $cost_center_options = $has_project ? $project->cost_center_options() : [];
}

$source_types = [
    '' => '&nbsp;',
    'store' => 'Store',
    'cash' => 'Cash',
    'vendor' => 'Vendor'
];

$service_source_types = [
    '' => '&nbsp;',
    'cash' => 'Cash',
    'vendor' => 'Vendor'
];
?>
<div class="modal-dialog" style="width: 93%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Requisition Form</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12 top_fields">
                        <div class="form-group col-md-2">
                            <label for="request_date" class="control-label">Request Date</label>
                            <input type="hidden" name="requisition_id" value="<?= $edit ? $requisition->{$requisition::DB_TABLE_PK} : '' ?>">
                            <input type="text" class="form-control" required name="request_date" value="<?= $edit ? $requisition->request_date : date('Y-m-d') ?>" readonly>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="required_date" class="control-label">Required Date</label>
                            <input type="text" class="form-control datepicker" name="required_date" value="<?= $edit && $requisition->required_date != null ? $requisition->required_date : '' ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="requisition_type" class="control-label">Requisition Type</label>
                            <?= form_dropdown('approval_module_id', $approval_module_options, $edit ? $has_project ? 2 : 1 : '', ' class="form-control searchable" ') ?>
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
                        <table class="table table-hover table-bordered table-responsive" id="items_table">
                            <thead>
                                <div style="display: none">
                                    <?= form_dropdown('stakeholder_selector_template', $stakeholder_options, '') ?>
                                    <?= form_dropdown('main_location_selector_template', $main_location_options, '') ?>
                                    <?= form_dropdown('account_selector_template', $accounts_options, '') ?>
                                </div>
                                <tr style="display: none" class="row_display_item_availability">
                                    <td class="item_display" colspan="8">

                                    </td>
                                </tr>

                                <tr style="display: none;" class="cash_row_template">
                                    <td style="width: 25%;">
                                        <input name="description" placeholder="Cash Item Description" class="form-control">
                                        <input type="hidden" name="item_type" value="cash">
                                    </td>
                                    <td style="width:15%;">
                                        <input type="text" name="quantity" class="form-control">
                                    </td>
                                    <td style="width: 3%;">
                                        <?= form_dropdown('uom_id', $measurement_unit_options, '', ' class="form-control stakeholder_id"') ?>
                                    </td>
                                    <td style="width:15%;">
                                        <input type="text" class="form-control money" name="rate" value="" required>
                                    </td>
                                    <td>
                                        <input type="text" readonly class="form-control amount" name="amount" value="" required>
                                    </td>
                                    <td></td>
                                    <td>
                                        <div class="form-group col-xs-12">
                                            <input type="text" class="form-control" placeholder="Payee name" name="payee" value="" required>
                                        </div>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                <tr style="display: none;" class="service_row_template">
                                    <td style="width: 25%;">
                                        <input name="service_description" placeholder="Service Description" class="form-control">
                                        <input type="hidden" name="item_type" value="service">
                                    </td>
                                    <td style="width:6%;">
                                        <input type="text" name="quantity" class="form-control input">
                                    </td>
                                    <td style="width: 3%;">
                                        <?= form_dropdown('uom_id', $measurement_unit_options, '', ' class="form-control stakeholder_id"') ?>
                                    </td>
                                    <td style="width:15%;">
                                        <input type="text" class="form-control money" name="rate" value="" required>
                                    </td>
                                    <td>
                                        <input type="text" readonly class="form-control amount" name="amount" value="" required>
                                    </td>
                                    <td>
                                        <?= form_dropdown(
                                            'source_type',
                                            $service_source_types,
                                            '',
                                            ' class="form-control" '
                                        ) ?>
                                    </td>
                                    <td>
                                        <div class="form-group col-xs-12 source_selector">
                                            <?= form_dropdown('source_id', ['' => '&nbsp'], '', ' class="form-control"') ?>
                                        </div>
                                        <div style="display: none" class="form-group col-xs-12 payee_input_div">
                                            <input type="text" class="form-control" placeholder="Payee name" name="payee" value="" required>
                                        </div>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                <tr style="display: none;" class="material_row_template">
                                    <td style="width:25%;">
                                        <?= form_dropdown('material_id', $material_options, '', ' class="form-control"') ?>
                                        <input type="hidden" name="item_type" value="material">
                                    </td>

                                    <td style="width:6%;">
                                        <input type="text" name="quantity" class="form-control">
                                    </td>
                                    <td style="text-align: center" class="unit_display"></td>
                                    <td style="width:6%;">
                                        <input type="text" class="form-control money" name="rate" value="" required>
                                    </td>
                                    <td>
                                        <input type="text" readonly class="form-control amount" name="amount" value="" required>
                                    </td>
                                    <td>
                                        <?= form_dropdown(
                                            'source_type',
                                            $source_types,
                                            '',
                                            ' class="form-control" '
                                        ) ?>
                                    </td>
                                    <td>
                                        <div class="form-group col-xs-12 source_selector">
                                            <?= form_dropdown('source_id', ['' => '&nbsp'], '', ' class="form-control"') ?>
                                        </div>
                                        <div style="display: none" class="form-group col-xs-12 payee_input_div">
                                            <input type="text" class="form-control" placeholder="Payee name" name="payee" value="" required>
                                        </div>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                <tr style="display: none;" class="asset_row_template">

                                    <td style="width:25%;">
                                        <?= form_dropdown('asset_item_id', $asset_options, '', ' class="form-control"') ?>
                                        <input type="hidden" name="item_type" value="asset">
                                    </td>

                                    <td style="width:6%;">
                                        <input type="text" name="quantity" class="form-control">
                                    </td>
                                    <td></td>
                                    <td style="width:6%;">
                                        <input type="text" class="form-control money" name="rate" value="" required>
                                    </td>
                                    <td>
                                        <input type="text" readonly class="form-control amount" name="amount" value="" required>
                                    </td>
                                    <td>
                                        <?= form_dropdown(
                                            'source_type',
                                            $source_types,
                                            '',
                                            ' class="form-control" '
                                        ) ?>
                                    </td>
                                    <td>
                                        <div class="form-group col-xs-12 source_selector">
                                            <?= form_dropdown('source_id', ['' => '&nbsp'], '', ' class="form-control"') ?>
                                        </div>
                                        <div style="display: none" class="form-group col-xs-12 payee_input_div">
                                            <input type="text" class="form-control" placeholder="Payee name" name="payee" value="" required>
                                        </div>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Material/Description</th>
                                    <th style="width: 110px">Quantity</th>
                                    <th>unit</th>
                                    <th>Rate</th>
                                    <th>Amount</th>
                                    <th>Source Type</th>
                                    <th>Source/Payee</th>
                                    <th></th>
                                </tr>

                            </thead>
                            <tbody>
                                <?php
                                if (!$edit) { ?>
                                    <tr>
                                        <td>
                                            <?= form_dropdown('material_id', $material_options, '', ' class="form-control name" requisition_material_selector"') ?>
                                            <input type="hidden" name="item_type" value="material">
                                        </td>
                                        <td>
                                            <input type="text" name="quantity" class="form-control">
                                        </td>
                                        <td style="text-align: center" class="unit_display"></td>
                                        <td style="width:12%;">
                                            <input type="text" class="form-control money" name="rate" value="" required>
                                        </td>
                                        <td>
                                            <input type="text" readonly class="form-control amount" name="amount" value="" required>
                                        </td>
                                        <td>
                                            <?= form_dropdown('source_type', $source_types, '', ' class="form-control" ') ?>
                                        </td>
                                        <td style="width: 20%;">
                                            <div class="form-group col-xs-12 source_selector">
                                                <?= form_dropdown('source_id', ['' => '&nbsp'], '', ' class="form-control"') ?>
                                            </div>
                                            <div style="display: none" class="form-group col-xs-12 payee_input_div">
                                                <input type="text" class="form-control" placeholder="Payee name" name="payee" value="" required>
                                            </div>
                                        </td>
                                        <td>
                                            <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                    <?php } else {
                                    $material_items = $requisition->material_items();
                                    foreach ($material_items as $item) {
                                        $total_amount += $requested_amount = $item->requested_rate * $item->requested_quantity;
                                        $material = $item->material_item();

                                        if ($item->source_type == 'vendor') {
                                            $sources_options = $stakeholder_options;
                                            $source_id = $item->requested_vendor_id;
                                        } else if ($item->source_type == 'cash') {
                                            $sources_options = $account_options;
                                            $source_id = $item->requested_account_id;
                                        } else if ($item->source_type == 'imprest') {
                                            $sources_options = $account_options;
                                            $source_id = $item->requested_account_id;
                                        } else {
                                            $sources_options = $main_location_options;
                                            $source_id = $item->requested_location_id;
                                        }

                                    ?>
                                        <tr>
                                            <td style="width: 25%;">
                                                <?= form_dropdown('material_id', $material_options, $item->material_item_id, ' class="form-control"') ?>
                                                <input type="hidden" name="item_type" value="material">
                                            </td>
                                            <td style="width:15%;">
                                                <input type="text" name="quantity" value="<?= $item->requested_quantity ?>" class="form-control">
                                            </td>
                                            <td>
                                                <span class="unit_display"><?= $material->unit()->symbol ?></span>
                                            </td>
                                            <td style="width:6%;">
                                                <input type="text" class="form-control number_format" name="rate" value="<?= $item->requested_rate ?>" required>
                                            </td>

                                            <td>
                                                <input type="text" readonly class="form-control number_format amount" name="amount" value="<?= $requested_amount ?>" required>
                                            </td>

                                            <td>
                                                <?= form_dropdown('source_type', $source_types, $item->source_type, ' class="form-control" ') ?>
                                            </td>

                                            <td style="width: 20%">
                                                <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-xs-12 source_selector">
                                                    <?= form_dropdown('source_id', $sources_options, $source_id, ' class="form-control"') ?>
                                                </div>

                                                <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-xs-12 payee_input_div">
                                                    <input type="text" class="form-control" placeholder="Payee name" name="payee" value="<?= $item->payee ?>" required>
                                                </div>
                                            </td>
                                            <td>
                                                <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover">
                                                    <i class="fa fa-close"></i></button>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    $asset_items = $requisition->asset_items();
                                    foreach ($asset_items as $item) {
                                        $total_amount += $requested_amount = $item->requested_rate * $item->requested_quantity;

                                        if ($item->source_type == 'vendor') {
                                            $sources_options = $stakeholder_options;
                                            $source_id = $item->requested_vendor_id;
                                        } else if ($item->source_type == 'cash') {
                                            $sources_options = $account_options;
                                            $source_id = '';
                                        } else if ($item->source_type == 'imprest') {
                                            $sources_options = $account_options;
                                            $source_id = $item->requested_account_id;
                                        } else {
                                            $sources_options = $main_location_options;
                                            $source_id = $item->requested_location_id;
                                        }
                                    ?>

                                        <tr>
                                            <td style="width:25%;">
                                                <?= form_dropdown('asset_item_id', $asset_options, $item->asset_item_id, ' class="form-control"') ?>
                                                <input type="hidden" name="item_type" value="asset">
                                            </td>

                                            <td style="width:6%;">
                                                <input type="text" name="quantity" value="<?= $item->requested_quantity ?>" class="form-control">
                                            </td>
                                            <td></td>
                                            <td style="width:6%;">
                                                <input type="text" class="form-control number_format" name="rate" value="<?= $item->requested_rate ?>" required>
                                            </td>
                                            <td>
                                                <input type="text" readonly class="form-control number_format amount" name="amount" value="<?= $requested_amount ?>" required>
                                            </td>
                                            <td>
                                                <?= form_dropdown('source_type', $source_types, $item->source_type, ' class="form-control" ') ?>
                                            </td>

                                            <td style="width: 20%">
                                                <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-xs-12 source_selector">
                                                    <?= form_dropdown('source_id', $sources_options, $source_id, ' class="form-control"') ?>
                                                </div>

                                                <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-xs-12 payee_input_div">
                                                    <input type="text" class="form-control" placeholder="Payee name" name="payee" value="<?= $item->payee ?>" required>
                                                </div>
                                            </td>
                                            <td>
                                                <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                            </td>
                                        </tr>

                                    <?php
                                    }
                                    $service_items = $requisition->service_items();
                                    foreach ($service_items as $item) {
                                        $total_amount += $requested_amount = $item->requested_rate * $item->requested_quantity;
                                        $service = $item->service_items();

                                        if ($item->source_type == 'vendor') {
                                            $sources_options = $stakeholder_options;
                                            $source_id = $item->requested_vendor_id;
                                        } else if ($item->source_type == 'imprest') {
                                            $sources_options = $account_options;
                                            $source_id = $item->requested_account_id;
                                        } else {
                                            $sources_options = $account_options;
                                            $source_id = '';
                                        }
                                    ?>
                                        <tr>
                                            <td style="width: 25%;">
                                                <input name="service_description" value="<?= $item->description ?>" class="form-control">
                                                <input type="hidden" name="item_type" value="service">
                                            </td>
                                            <td style="width:6%;">
                                                <input type="text" name="quantity" class="form-control" value="<?= $item->requested_quantity ?>">
                                            </td>
                                            <td>
                                                <?= form_dropdown('uom_id', $measurement_unit_options, $item->measurement_unit_id, ' class="form-control"') ?>
                                            </td>
                                            <td style="width:15%;">
                                                <input type="text" class="form-control number_format" name="rate" value="<?= $item->requested_rate ?>" required>
                                            </td>
                                            <td>
                                                <input type="text" readonly class="form-control number_format amount" name="amount" value="<?= $requested_amount ?>" required>
                                            </td>
                                            <td>
                                                <?= form_dropdown('source_type', $source_types, $item->source_type, ' class="form-control" ') ?>
                                            </td>

                                            <td style="width: 20%">
                                                <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-xs-12 source_selector">
                                                    <?= form_dropdown('source_id', $sources_options, $source_id, ' class="form-control"') ?>
                                                </div>

                                                <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-xs-12 payee_input_div">
                                                    <input type="text" class="form-control" placeholder="Payee name" name="payee" value="<?= $item->payee ?>" required>
                                                </div>
                                            </td>
                                            <td>
                                                <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    $cash_items = $requisition->cash_items();
                                    foreach ($cash_items as $item) {
                                        $total_amount += $requested_amount = $item->requested_rate * $item->requested_quantity;
                                    ?>
                                        <tr>
                                            <td style="width:25%;">
                                                <input name="description" value="<?= $item->description ?>" class="form-control">
                                                <input type="hidden" name="item_type" value="cash">
                                            </td>
                                            <td style="width:6%;">
                                                <input type="text" name="quantity" class="form-control" value="<?= $item->requested_quantity ?>">
                                            </td>
                                            <td>
                                                <?= form_dropdown('uom_id', $measurement_unit_options, $item->measurement_unit_id, ' class="form-control"') ?>
                                            </td>
                                            <td style="width:15%;">
                                                <input type="text" class="form-control number_format" name="rate" value="<?= $item->requested_rate ?>" required>
                                            </td>
                                            <td>
                                                <input type="text" readonly class="form-control number_format amount" name="amount" value="<?= $requested_amount ?>" required>
                                            </td>
                                            <td></td>
                                            <td>
                                                <div class="form-group col-xs-12">
                                                    <input type="text" class="form-control" name="payee" value="<?= $item->payee ?>">
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

                                <tr class="text_styles">
                                    <th>TOTAL</th>
                                    <th colspan="4" class="number_format total_amount_display" style="text-align: right">

                                        <?= $edit ? $total_amount : 0 ?></th>

                                    <td colspan="3"></td>
                                </tr>

                                <tr>
                                    <td colspan="4" style="text-align: right">FREIGHT CHARGES</td>
                                    <td><input name="freight" class="form-control money text-right" value="<?= $edit ? $requisition->freight : '' ?>"></td>
                                    <td colspan="4" rowspan="4">
                                        <div style="margin: r -1px; margin: l -1px;" class="row form-group">
                                            <div class="form-group col-md-6 ">
                                                <input <?= $edit && $requisition->vat_inclusive == 'VAT PRICED' ? 'disabled' : '' ?> type="checkbox" name="vat_inclusive" <?= $edit && !is_null($requisition->vat_inclusive) ? 'checked' : '' ?>>
                                                <input type="hidden" name="vat_priced_po" value="<?= $edit && $requisition->vat_inclusive == 'VAT PRICED' ? 'true' : 'false' ?>">
                                                <label for="vat_inclusive" class="control-label text-center">Include VAT</label>
                                            </div>
                                            <div class="form-group col-md-6 " <?= $edit ? ($requisition->vat_inclusive == null ? 'style="display: none;"' : '') : 'style="display: none;"' ?>>
                                                <?php $vat_options = array(0 => 'VAT@0%', 15 => 'VAT@15%', 18 => 'VAT@18%') ?>
                                                <?= form_dropdown('vat_percentage', $vat_options, $edit ? $requisition->vat_percentage : '', ' class="form-control searchable"') ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="text-align: right">INSPECTION AND OTHER CHARGES</td>
                                    <td><input name="inspection_and_other_charges" class="form-control money text-right" value="<?= $edit ? $requisition->inspection_and_other_charges : '' ?>"></td>
                                </tr>
                                <?php
                                if ($edit) {
                                    if ($requisition->vat_inclusive == 'VAT COMPONENT') {
                                        $grand_total = ($total_amount + $requisition->inspection_and_other_charges + $requisition->freight) * 1.18;
                                        $vat_amount = number_format((($total_amount + $requisition->inspection_and_other_charges + $requisition->freight) * 0.18), 2);
                                    } else {
                                        $grand_total = ($total_amount + $requisition->inspection_and_other_charges + $requisition->freight);
                                        $vat_amount = '';
                                    }
                                } else {
                                    $vat_amount = '';
                                }
                                ?>
                                <tr>
                                    <td colspan="4" style="text-align: right;">VAT</td>
                                    <td><input style="text-align: right; width: 150px;" name="vat" class="form-control money text-right" value="<?= $vat_amount ?>" readonly></td>
                                </tr>
                                <tr class="text_styles">
                                    <th colspan="4" style="text-align: right">GRAND TOTAL</th>
                                    <th class="grand_total_display" style="text-align: right"><?= $edit ? number_format(($grand_total), 2) : 0 ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-xs-12">
                        <div class="pull-right">
                            <button type="button" class="btn btn-default btn-xs material_row_adder">
                                <i class="fa fa-plus"></i> Material
                            </button>

                            <button type="button" class="btn btn-default btn-xs asset_row_adder">
                                <i class="fa fa-plus"></i> Asset
                            </button>

                            <button type="button" class="btn btn-default btn-xs cash_row_adder">
                                <i class="fa fa-plus"></i> Cash
                            </button>

                            <button type="button" class="btn btn-default btn-xs service_row_adder">
                                <i class="fa fa-plus"></i> Service
                            </button>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group col-md-8">
                            <label for="comments" class="control-label">Comments</label>
                            <textarea name="comments" class="form-control"><?= $edit ? $requisition->requesting_comments : '' ?></textarea>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="foward_to" class="control-label ">Forward For Special Approval</label>
                            <?= form_dropdown('foward_to', $forward_to_dropdown, $edit ? $requisition->foward_to : '', 'class="form-control searchable foward_to_options"') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default suspend_requisition">Suspend</button>
                <button type="button" class="btn btn-sm btn-default save_requisition">Submit</button>
            </div>
        </form>
    </div>
</div>