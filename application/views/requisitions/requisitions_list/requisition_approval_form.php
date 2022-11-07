<?php
$source_types_options = [
    '' => '',
    'vendor' => 'Vendor',
    'cash' => 'Cash',
    'store' => 'Store',
];

$last_approval_id =  $last_approval ? $last_approval->{$last_approval::DB_TABLE_PK} : 0;

?>
<div class="modal-dialog" style="width: 95%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Requisition Approval</h4>
        </div>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-2">
                        <label for="approve_date" class="control-label">Approve Date</label>
                        <input type="hidden" name="requisition_id" value="<?= $requisition->{$requisition::DB_TABLE_PK} ?>">
                        <input type="hidden" name="has_sources" value="<?= $current_approval_level->change_source ? 'true' : 'false' ?>">
                        <input type="hidden" name="approval_chain_level_id" value="<?= $requisition->foward_to != null ? $requisition->foward_to : $current_approval_level->{$current_approval_level::DB_TABLE_PK} ?>">
                        <input type="text" class="form-control datepicker" required name="approve_date" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="currency_id" class="control-label">Currency</label>
                        <?php
                        $currency = $requisition->currency();
                        echo form_dropdown(
                            'currency_id',
                            [
                                $currency->{$currency::DB_TABLE_PK} => $currency->name_and_symbol()
                            ],
                            $currency->{$currency::DB_TABLE_PK},
                            ' class=" form-control" readonly'
                        )
                        ?>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="control-label" for="email">Requisition No:</label>
                        <div>
                            <span class="form-control-static"><?= $requisition->requisition_number() ?></span>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="control-label" for="email">Requested For:</label>
                        <div>
                            <span class="form-control-static"><?= wordwrap($requisition->cost_center_name(), 45, '<br/>') ?></span>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="control-label" for="email">Requested By:</label>
                        <div>
                            <span class="form-control-static"><?= $requisition->requester()->full_name() ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 table-responsive">
                    <?php
                    $material_items = $requisition->material_items();

                    $approval_chain_level_options = $current_approval_level->previous_level_options();

                    $cash_items = $requisition->cash_items();

                    $asset_items = $requisition->asset_items();

                    $service_items = $requisition->service_items();

                    $total_amount = 0;

                    if ($current_approval_level->change_source) { ?>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Item Information</th>
                                    <th>UOM</th>
                                    <th>Sources Information</th>
                                </tr>
                                <tr class="material_source_row_template" style="display: none">
                                    <td>
                                        <?= form_dropdown('source_type', $source_types_options, '', ' class="form-control" ') ?>
                                    </td>
                                    <td>
                                        <?= form_dropdown('source', $stakeholder_options, '', ' class="form-control" ') ?>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control source_approved_quantity" required name="quantity" value="">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control number_format" required name="rate" value="">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control number_format" readonly name="amount" value="">
                                    </td>
                                    <td>
                                        <button class="btn btn-xs btn-default row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                                <tr class="cash_source_row_template" style="display: none">
                                    <td>
                                        <?= form_dropdown('source', $account_options, '', ' class="form-control" ') ?>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control source_approved_quantity" required name="quantity" value="">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control number_format" required name="rate" value="">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control number_format" readonly name="amount" value="">
                                    </td>
                                    <td>
                                        <button class="btn btn-xs btn-default row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                                <tr class="sources_options_templates" style="display: none">
                                    <td>
                                        <?= form_dropdown('cashbook_options', $account_options, '') ?>
                                        <?= form_dropdown('stakeholder_options', $stakeholder_options, '') ?>
                                        <?= form_dropdown('main_location_options', $main_location_options, '') ?>
                                    </td>
                                </tr>
                            </thead>
                            <tbody class="major_table_tbody">
                                <?php
                                foreach ($material_items as $item) {
                                    $material_item = $item->material_item();
                                ?>
                                    <tr>
                                        <td style="width: 20% !important;">
                                            <?= wordwrap($material_item->item_name, 50, '<br/>') ?>
                                            <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                            <input type="hidden" name="item_type" value="material">
                                        </td>
                                        <td><?= $material_item->unit()->symbol ?></td>
                                        <td width="75%">
                                            <table class="table table-bordered table-hover sources_table">
                                                <thead>
                                                    <tr>
                                                        <th>Source Type</th>
                                                        <th width="40%">Source/Vendor/Payee</th>
                                                        <th>Quantity</th>
                                                        <th>Rate</th>
                                                        <th>Amount</th>
                                                        <th>
                                                            <!--<button title="Add Source" class="btn btn-default btn-xs pull-right material_source_adder"><i class="fa fa-plus"></i></button>-->
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if ($last_approval) {
                                                        $approved_material_items = $last_approval->material_items('all', $item->{$item::DB_TABLE_PK});
                                                        foreach ($approved_material_items as $approved_item) {
                                                            $quantity = $approved_item->approved_quantity;
                                                            $rate = $approved_item->approved_rate;
                                                            $total_amount += $quantity * $rate;
                                                            $source_options = [];
                                                            $source_id = '';
                                                            if ($approved_item->source_type == 'vendor') {
                                                                $source_options = $stakeholder_options;
                                                                $source_id = $approved_item->vendor_id;
                                                            } else if ($approved_item->source_type == 'cash') {
                                                                $source_options = $account_options;
                                                                $source_id = $approved_item->account_id;
                                                            } else if ($approved_item->source_type == 'imprest') {
                                                                $source_options = $account_options;
                                                                $source_id = $approved_item->account_id;
                                                            } else if ($approved_item->source_type == 'store') {
                                                                $source_options = $main_location_options;
                                                                $source_id = $approved_item->location_id;
                                                            }
                                                    ?>
                                                            <tr>
                                                                <td>
                                                                    <?= form_dropdown('source_type', $source_types_options, $approved_item->source_type, ' class="form-control" ') ?>
                                                                </td>
                                                                <td class="sources_container">
                                                                    <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-xs-12 source_selector_div">
                                                                        <?= form_dropdown('source', $source_options, $source_id, ' class="form-control searchable" ' . ($approved_item->source_type == 'cash' ? 'disabled' : '')) ?>
                                                                    </div>

                                                                    <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-xs-12 payee_input_div">
                                                                        <input type="text" class="form-control" placeholder="Payee name" name="payee" value="<?= $approved_item->payee ?>" required>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control source_approved_quantity" required name="quantity" value="<?= $approved_item->approved_quantity ?>">
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                        <input type="text" class="form-control money" required name="rate" value="<?= round($approved_item->approved_rate, 2) ?>">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                        <input type="text" class="form-control money" required name="amount" value="<?= round(($approved_item->approved_rate * $approved_item->approved_quantity), 2) ?>">
                                                                    </div>
                                                                </td>
                                                                <td></td>
                                                            </tr>
                                                        <?php
                                                        }
                                                    } else {
                                                        if ($item->source_type == 'vendor') {
                                                            $source_options = $stakeholder_options;
                                                            $source_id = $item->requested_vendor_id;
                                                        } else if ($item->source_type == 'store') {
                                                            $source_options = $main_location_options;
                                                            $source_id = $item->requested_location_id;
                                                        } else if ($item->source_type == 'cash') {
                                                            $source_options = $account_options;
                                                            $source_id = $item->requested_account_id;
                                                        } else if ($item->source_type == 'imprest') {
                                                            $source_options = $account_options;
                                                            $source_id = $item->requested_account_id;
                                                        } else {
                                                            $source_options = [];
                                                            $source_id = '';
                                                        }
                                                        $total_amount += $item->requested_quantity * $item->requested_rate;
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?= form_dropdown('source_type', $source_types_options, $item->source_type, ' class="form-control" ') ?>
                                                            </td>
                                                            <td class="sources_container">
                                                                <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-xs-12 source_selector_div">
                                                                    <?= form_dropdown('source', $source_options, $source_id, ' class="form-control searchable" ' . ($item->source_type == 'cash' ? 'disabled' : '')) ?>
                                                                </div>

                                                                <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-xs-12 payee_input_div">
                                                                    <input type="text" class="form-control" placeholder="Payee name" name="payee" value="<?= $item->payee ?>" required>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control source_approved_quantity" required name="quantity" value="<?= $item->requested_quantity ?>">
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                    <input type="text" class="form-control money" required name="rate" value="<?= round($item->requested_rate, 2) ?>">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                    <input type="text" class="form-control money" readonly name="amount" value="<?= round(($item->requested_rate * $item->requested_quantity), 2) ?>">
                                                                </div>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                <?php
                                }

                                foreach ($asset_items as $item) {
                                ?>
                                    <tr>
                                        <td>
                                            <?= wordwrap($item->asset_item()->asset_name, 50, '<br/>') ?>
                                            <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                            <input type="hidden" name="item_type" value="asset">
                                        </td>
                                        <td>No.</td>
                                        <td width="75%">
                                            <table class="table table-bordered table-hover sources_table">
                                                <thead>
                                                    <tr>
                                                        <th>Source Type</th>
                                                        <th width="40%">Source/Vendor/Payee</th>
                                                        <th>Quantity</th>
                                                        <th>Rate</th>
                                                        <th>Amount</th>
                                                        <th>
                                                            <!--<button title="Add Source" class="btn btn-default btn-xs pull-right material_source_adder"><i class="fa fa-plus"></i></button>-->
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if ($last_approval) {
                                                        $approved_asset_items = $last_approval->asset_items('all', $item->{$item::DB_TABLE_PK});
                                                        foreach ($approved_asset_items as $approved_item) {
                                                            $quantity = $approved_item->approved_quantity;
                                                            $rate = $approved_item->approved_rate;
                                                            $total_amount += $quantity * $rate;
                                                            $source_options = [];
                                                            $source_id = '';
                                                            if ($approved_item->source_type == 'vendor') {
                                                                $source_options = $stakeholder_options;
                                                                $source_id = $approved_item->vendor_id;
                                                            } else if ($approved_item->source_type == 'cash') {
                                                                $source_options = $account_options;
                                                                $source_id = '';
                                                            } else if ($approved_item->source_type == 'imprest') {
                                                                $source_options = $account_options;
                                                                $source_id = $approved_item->account_id;
                                                            } else if ($approved_item->source_type == 'store') {
                                                                $source_options = $main_location_options;
                                                                $source_id = $approved_item->location_id;
                                                            }
                                                    ?>
                                                            <tr>
                                                                <td>
                                                                    <?= form_dropdown('source_type', $source_types_options, $approved_item->source_type, ' class="form-control" ') ?>
                                                                </td>
                                                                <td class="sources_container">
                                                                    <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-xs-12 source_selector_div">
                                                                        <?= form_dropdown('source', $source_options, $source_id, ' class="form-control searchable" ' . ($approved_item->source_type == 'cash' ? 'disabled' : '')) ?>
                                                                    </div>

                                                                    <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-xs-12 payee_input_div">
                                                                        <input type="text" class="form-control" placeholder="Payee name" name="payee" value="<?= $approved_item->payee ?>" required>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control source_approved_quantity" required name="quantity" value="<?= $approved_item->approved_quantity ?>">
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                        <input type="text" class="form-control money" required name="rate" value="<?= round($approved_item->approved_rate, 2) ?>">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                        <input type="text" class="form-control money" required name="amount" value="<?= round(($approved_item->approved_rate * $approved_item->approved_quantity), 2) ?>">
                                                                    </div>
                                                                </td>
                                                                <td></td>
                                                            </tr>
                                                        <?php
                                                        }
                                                    } else {
                                                        if ($item->source_type == 'vendor') {
                                                            $source_options = $stakeholder_options;
                                                            $source_id = $item->requested_vendor_id;
                                                        } else if ($item->source_type == 'store') {
                                                            $source_options = $main_location_options;
                                                            $source_id = $item->requested_location_id;
                                                        } else if ($item->source_type == 'cash') {
                                                            $source_options = [];
                                                            $source_id = '';
                                                        } else if ($item->source_type == 'imprest') {
                                                            $source_options = $account_options;
                                                            $source_id = $item->requested_account_id;
                                                        } else {
                                                            $source_options = [];
                                                            $source_id = '';
                                                        }
                                                        $total_amount += $item->requested_quantity * $item->requested_rate;
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?= form_dropdown('source_type', $source_types_options, $item->source_type, ' class="form-control" ') ?>
                                                            </td>
                                                            <td class="sources_container">
                                                                <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-xs-12 source_selector_div">
                                                                    <?= form_dropdown('source', $source_options, $source_id, ' class="form-control searchable" ' . ($item->source_type == 'cash' ? 'disabled' : '')) ?>
                                                                </div>

                                                                <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-xs-12 payee_input_div">
                                                                    <input type="text" class="form-control" placeholder="Payee name" name="payee" value="<?= $item->payee ?>" required>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control source_approved_quantity" required name="quantity" value="<?= $item->requested_quantity ?>">
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                    <input type="text" class="form-control money" required name="rate" value="<?= round($item->requested_rate, 2) ?>">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                    <input type="text" class="form-control money" readonly name="amount" value="<?= round(($item->requested_rate * $item->requested_quantity), 2) ?>">
                                                                </div>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                <?php
                                }

                                foreach ($service_items as $item) {
                                ?>
                                    <tr>
                                        <td>
                                            <?= wordwrap($item->description, 50, '<br/>') ?>
                                            <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                            <input type="hidden" name="item_type" value="service">
                                        </td>
                                        <td>No.</td>
                                        <td width="75%">
                                            <table class="table table-bordered table-hover sources_table">
                                                <thead>
                                                    <tr>
                                                        <th>Source Type</th>
                                                        <th width="40%">Source/Vendor/Payee</th>
                                                        <th>Quantity</th>
                                                        <th>Rate</th>
                                                        <th>Amount</th>
                                                        <th>
                                                            <!--<button title="Add Source" class="btn btn-default btn-xs pull-right material_source_adder"><i class="fa fa-plus"></i></button>-->
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if ($last_approval) {
                                                        $approved_service_items = $last_approval->service_items('all', $item->{$item::DB_TABLE_PK});
                                                        foreach ($approved_service_items as $approved_item) {

                                                            $quantity = $approved_item->approved_quantity;
                                                            $rate = $approved_item->approved_rate;
                                                            $total_amount += $quantity * $rate;
                                                            $source_options = [];
                                                            $source_id = '';
                                                            if ($approved_item->source_type == 'vendor') {
                                                                $source_options = $stakeholder_options;
                                                                $source_id = $approved_item->vendor_id;
                                                            } else if ($approved_item->source_type == 'imprest') {
                                                                $source_options = $account_options;
                                                                $source_id = $approved_item->account_id;
                                                            } else if ($approved_item->source_type == 'cash') {
                                                                $source_options = $account_options;
                                                                $source_id = '';
                                                            }
                                                    ?>
                                                            <tr>
                                                                <td>
                                                                    <?= form_dropdown('source_type', $source_types_options, $approved_item->source_type, ' class="form-control" ') ?>
                                                                </td>
                                                                <td class="sources_container">
                                                                    <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-xs-12 source_selector_div">
                                                                        <?= form_dropdown('source', $source_options, $source_id, ' class="form-control searchable" ' . ($approved_item->source_type == 'cash' ? 'disabled' : '')) ?>
                                                                    </div>

                                                                    <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-xs-12 payee_input_div">
                                                                        <input type="text" class="form-control" placeholder="Payee name" name="payee" value="<?= $approved_item->payee ?>" required>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control source_approved_quantity" required name="quantity" value="<?= $approved_item->approved_quantity ?>">
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                        <input type="text" class="form-control money" required name="rate" value="<?= round($approved_item->approved_rate, 2) ?>">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                        <input type="text" class="form-control money" required name="amount" value="<?= round(($approved_item->approved_rate * $approved_item->approved_quantity), 2) ?>">
                                                                    </div>
                                                                </td>
                                                                <td></td>
                                                            </tr>
                                                        <?php
                                                        }
                                                    } else {
                                                        if ($item->source_type == 'vendor') {
                                                            $source_options = $stakeholder_options;
                                                            $source_id = $item->requested_vendor_id;
                                                        } else if ($item->source_type == 'cash') {
                                                            $source_options = [];
                                                            $source_id = '';
                                                        } else if ($item->source_type == 'imprest') {
                                                            $source_options = $account_options;
                                                            $source_id = $item->requested_account_id;
                                                        } else {
                                                            $source_options = [];
                                                            $source_id = '';
                                                        }
                                                        $total_amount += $item->requested_quantity * $item->requested_rate;
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?= form_dropdown('source_type', $source_types_options, $item->source_type, ' class="form-control" ') ?>
                                                            </td>
                                                            <td class="sources_container">
                                                                <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-xs-12 source_selector_div">
                                                                    <?= form_dropdown('source', $source_options, $source_id, ' class="form-control searchable" ' . ($item->source_type == 'cash' ? 'disabled' : '')) ?>
                                                                </div>

                                                                <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-xs-12 payee_input_div">
                                                                    <input type="text" class="form-control" placeholder="Payee name" name="payee" value="<?= $item->payee ?>" required>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control source_approved_quantity" required name="quantity" value="<?= $item->requested_quantity ?>">
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                    <input type="text" class="form-control money" required name="rate" value="<?= round($item->requested_rate, 2) ?>">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                    <input type="text" class="form-control money" readonly name="amount" value="<?= round(($item->requested_rate * $item->requested_quantity), 2) ?>">
                                                                </div>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                <?php
                                }

                                foreach ($cash_items as $item) {
                                ?>
                                    <tr>
                                        <td style="width: 20% !important;">
                                            <?= wordwrap($item->description, 50, '<br/>') ?>
                                            <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                            <input type="hidden" name="item_type" value="cash">
                                        </td>
                                        <td><?= $item->measurement_unit()->symbol ?></td>
                                        <td width="75%">
                                            <table class="table table-bordered table-hover sources_table">
                                                <thead>
                                                    <tr>
                                                        <th width="40%">Payee</th>
                                                        <th>Quantity</th>
                                                        <th>Rate</th>
                                                        <th>Amount</th>
                                                        <th>
                                                            <!--<button title="Add Source" class="btn btn-default btn-xs pull-right cash_source_adder"><i class="fa fa-plus"></i></button>-->
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if ($last_approval) {
                                                        $approved_cash_items = $last_approval->cash_items($item->{$item::DB_TABLE_PK});
                                                        foreach ($approved_cash_items as $approved_cash_item) {
                                                            $approved_item = $item->approved_item($last_approval_id, $approved_cash_item->account_id);
                                                            $total_amount += $approved_item->approved_quantity * $approved_item->approved_rate;

                                                    ?>
                                                            <tr>
                                                                <td>
                                                                    <div class="form-group col-xs-12">
                                                                        <input type="text" class="form-control" name="payee" value="<?= $approved_cash_item->payee ?>">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control source_approved_quantity" required name="quantity" value="<?= $approved_item->approved_quantity ?>">
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                        <input type="text" class="form-control number_format" required name="rate" value="<?= round($approved_item->approved_rate, 2) ?>">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                        <input type="text" class="form-control number_format" readonly name="amount" value="<?= round(($approved_item->approved_rate * $approved_item->approved_quantity), 2) ?>">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-xs btn-default row_remover">
                                                                        <i class="fa fa-close"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                        }
                                                    } else {
                                                        $total_amount += $item->requested_rate * $item->requested_quantity;
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <div class="form-group col-xs-12">
                                                                    <input type="text" class="form-control" name="payee" value="<?= $item->payee ?>">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control source_approved_quantity" required name="quantity" value="<?= $item->requested_quantity ?>">
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                    <input type="text" class="form-control number_format" required name="rate" value="<?= round($item->requested_rate, 2) ?>">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-addon"><?= $currency->symbol ?></span>
                                                                    <input type="text" class="form-control number_format" readonly name="amount" value="<?= round(($item->requested_rate * $item->requested_quantity), 2) ?>">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <!--<button class="btn btn-xs btn-default row_remover">
                                                            <i class="fa fa-close"></i>
                                                        </button>-->
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                <?php
                                }
                                if ($last_approval) {
                                ?>
                                    <tr>
                                        <th colspan="2">Freight</th>
                                        <th style="text-align: right">
                                            <input type="text" style="width: 200px; text-align: right" name="freight" class="form-control pull-right number_format" value="<?= $last_approval->freight ?>">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Inspection &amp; Other Charges</th>
                                        <th style="text-align: right">
                                            <input type="text" style="width: 200px; text-align: right" name="inspection_and_other_charges" class="form-control  pull-right number_format" value="<?= $last_approval->inspection_and_other_charges  ?>">
                                        </th>
                                    </tr>
                                    <?php
                                    if ($last_approval->vat_inclusive == 'VAT COMPONENT') {
                                        $grand_total = ($total_amount + $last_approval->inspection_and_other_charges + $last_approval->freight) * 1.18;
                                        $vat_amount = number_format((($total_amount + $last_approval->inspection_and_other_charges + $last_approval->freight) * 0.18), 2);
                                    } else {
                                        $grand_total = ($total_amount + $last_approval->inspection_and_other_charges + $last_approval->freight);
                                        $vat_amount = 0;
                                    }
                                    ?>
                                    <tr>
                                        <th colspan="2">VAT</th>
                                        <th style="text-align: right">
                                            <input type="text" style="width: 200px; text-align: right" name="vat" class="form-control pull-right number_format" value="<?= $vat_amount ?>" readonly>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="grand_total_display" style="text-align: right"><?= number_format($grand_total, 2) ?></th>
                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <th colspan="2">Freight</th>
                                        <th style="text-align: right">
                                            <input type="text" style="width: 200px; text-align: right" name="freight" class="form-control pull-right number_format" value="<?= $requisition->freight ?>">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Inspection &amp; Other Charges</th>
                                        <th style="text-align: right">
                                            <input type="text" style="width: 200px; text-align: right" name="inspection_and_other_charges" class="form-control  pull-right number_format" value="<?= $requisition->inspection_and_other_charges  ?>">
                                        </th>
                                    </tr>
                                    <?php
                                    if ($requisition->vat_inclusive == 'VAT COMPONENT') {
                                        $grand_total = ($total_amount + $requisition->inspection_and_other_charges + $requisition->freight) * 1.18;
                                        $vat_amount = number_format((($total_amount + $requisition->inspection_and_other_charges + $requisition->freight) * 0.18), 2);
                                    } else {
                                        $grand_total = ($total_amount + $requisition->inspection_and_other_charges + $requisition->freight);
                                        $vat_amount = 0;
                                    }
                                    ?>
                                    <tr>
                                        <th colspan="2">VAT</th>
                                        <th style="text-align: right">
                                            <input type="text" style="width: 200px; text-align: right" name="vat" class="form-control pull-right number_format" value="<?= $vat_amount ?>" readonly>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="grand_total_display" style="text-align: right"><?= number_format($grand_total, 2) ?></th>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>

                <div class="col-md-12">
                    <?php if ($last_approval) { ?>
                        $object = $last_approval;
                    <?php } else { ?>
                        $object = $requisition;
                    <?php } ?>
                    <div class="form-group">
                        <input <?= $object->vat_inclusive == 'VAT PRICED' ? 'disabled' : '' ?> type="checkbox" name="vat_inclusive" <?= !is_null($object->vat_inclusive) ? 'checked' : '' ?>>
                        <input type="hidden" name="vat_priced_po" value="<?= $object->vat_inclusive == 'VAT PRICED' ? 'true' : 'false' ?>">
                        <label for="vat_inclusive" class="control-label text-center">VAT inclusive</label>
                    </div>
                    <div class="form-group col-md-6 " <?= $edit ? ($object->vat_inclusive == null ? 'style="display: none;"' : '') : 'style="display: none;"' ?>>
                        <?php $vat_options = array(0 => 'VAT@0%', 15 => 'VAT@15%', 18 => 'VAT@18%') ?>
                        <?= form_dropdown('vat_percentage', $vat_options, $edit ? $object->vat_percentage : '', ' class="form-control searchable"') ?>
                    </div>
                    <?php
                    $current_approval_level = $requisition->current_approval_level();
                    $next_level = $current_approval_level->next_level();
                    if ($can_override_prev && $next_level) {
                    ?>
                        <div class="form-group">
                            <input type="checkbox" name="set_final">
                            <label for="set_final" class="control-label text-center">Finalize Approval</label>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="hidden" name="vat_percentage" value="<?= $requisition->vat_percentage ?>">
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="form-group <?= $can_override_prev && $next_level ? 'col-md-8' : '' ?>">
                        <label for="comments" class="control-label">Approving Comments</label>
                        <textarea name="comments" class="form-control" style="height: 8%"><?= $last_approval ? $last_approval->approving_comments : $requisition->requesting_comments ?></textarea>
                    </div>
                    <?php
                    if ($can_override_prev && $next_level) {
                        $forward_to_dropdown = $forward_to_dropdown + $requisition->next_approval_employees_options($next_level->{$next_level::DB_TABLE_PK});
                    ?>
                        <div class="form-group col-md-4">
                            <label for="foward_to" class="control-label ">Forward To</label>
                            <?= form_dropdown('forward_to', $forward_to_dropdown, '', 'class="form-control searchable foward_to_options"') ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="row">

                    <div class="revert_form col-md-3" style="display: none;">

                        <div class="form-group col-md-12">

                            <label for="returned_chain_level_id" class="control-label">Approval Level Required</label>

                            <?= form_dropdown('returned_chain_level_id', $approval_chain_level_options, '', ' class="form-control searchable" ') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-sm reject_requisition">Reject</button>
            <button type="button" class="btn btn-default btn-sm approve_requisition">Submit Approval</button>
        </div>
    </div>
</div>