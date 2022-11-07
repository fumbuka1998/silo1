<?php
$source_types_options = [
    '' => '',
    'vendor' => 'Vendor',
    'cash' => 'Cash',
    'store' => 'Store',
];

$last_approval_id =  $last_approval ? $last_approval->{$last_approval::DB_TABLE_PK} : 0;
$current_approval_level = $requisition->current_approval_level();
$approval_chain_level_id = $current_approval_level->{$current_approval_level::DB_TABLE_PK};

$vat_factor = $last_approval_id > 0 ? $last_approval->vat_percentage/100 : $requisition->vat_percentage/100;

?>
<div class='row'>
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="form-group col-md-2" style="padding: 2rem">
                <label for="approve_date" class="control-label">Approve Date</label>
                <input type="hidden" name="requisition_id" value="<?= $requisition->{$requisition::DB_TABLE_PK} ?>">
                <input type="hidden" name="has_sources" value="<?= $current_approval_level->change_source ? 'true' : 'false' ?>">
                <input type="hidden" name="approval_chain_level_id" value="<?= $approval_chain_level_id ?>">
                <input type="text" class="form-control datepicker" required name="approve_date" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group col-md-2" style="padding: 2rem">
                <label for="currency_id" class="control-label">Currency</label>
                <?php
                $currency = $requisition->currency();
                echo form_dropdown(
                    'currency_id',
                    [
                        $currency->{$currency::DB_TABLE_PK} => $currency->name_and_symbol()
                    ],
                    $currency->{$currency::DB_TABLE_PK},
                    ' class=" form-control searchable" readonly'
                )
                ?>
            </div>
            <div class="form-group col-md-2" style="padding: 2rem">
                <label class="control-label" for="email">Requisition No:</label>
                <div>
                    <span class="form-control-static"><?= $requisition->requisition_number() ?></span>
                </div>
            </div>
            <div class="form-group col-md-3" style="padding: 2rem">
                <label class="control-label" for="email">Requested For:</label>
                <div>
                    <span class="form-control-static"><?= wordwrap($requisition->cost_center_name(), 45, '<br/>') ?></span>
                </div>
            </div>
            <div class="form-group col-md-3" style="padding: 2rem">
                <label class="control-label" for="email">Requested By:</label>
                <div>
                    <span class="form-control-static"><?= $requisition->requester()->full_name() ?></span>
                </div>
            </div>
        </div>

        <style>
            .fixed-input {
                width: 181px;
                text-align: right;
            }
        </style>
        <div class="col-md-12 table-responsive">
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
                                                            <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-md-12 source_selector_div">
                                                                <?= form_dropdown('source', $source_options, $source_id, ' class="form-control searchable" ' . ($approved_item->source_type == 'cash' ? 'disabled' : '')) ?>
                                                            </div>

                                                            <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-md-12 payee_input_div">
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
                                                        <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-md-12 source_selector_div">
                                                            <?= form_dropdown('source', $source_options, $source_id, ' class="form-control searchable" ' . ($item->source_type == 'cash' ? 'disabled' : '')) ?>
                                                        </div>

                                                        <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-md-12 payee_input_div">
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
                                                            <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-md-12 source_selector_div">
                                                                <?= form_dropdown('source', $source_options, $source_id, ' class="form-control searchable" ' . ($approved_item->source_type == 'cash' ? 'disabled' : '')) ?>
                                                            </div>

                                                            <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-md-12 payee_input_div">
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
                                                        <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-md-12 source_selector_div">
                                                            <?= form_dropdown('source', $source_options, $source_id, ' class="form-control searchable" ' . ($item->source_type == 'cash' ? 'disabled' : '')) ?>
                                                        </div>

                                                        <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-md-12 payee_input_div">
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
                                                            <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-md-12 source_selector_div">
                                                                <?= form_dropdown('source', $source_options, $source_id, ' class="form-control searchable" ' . ($approved_item->source_type == 'cash' ? 'disabled' : '')) ?>
                                                            </div>

                                                            <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-md-12 payee_input_div">
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
                                                        <div style=" <?= $item->source_type == 'cash' ? 'display: none' : ''   ?>" class="form-group col-md-12 source_selector_div">
                                                            <?= form_dropdown('source', $source_options, $source_id, ' class="form-control searchable" ' . ($item->source_type == 'cash' ? 'disabled' : '')) ?>
                                                        </div>

                                                        <div style=" <?= $item->source_type == 'cash' ? '' : 'display: none'   ?>" class="form-group col-md-12 payee_input_div">
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
                                                            <div class="form-group col-md-12">
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
                                                        <div class="form-group col-md-12">
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
                                <th>
                                    <input type="text" name="freight" class="form-control pull-right number_format fixed-input" value="<?= $last_approval->freight ?>">
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2">Inspection &amp; Other Charges</th>
                                <th>
                                    <input type="text" name="inspection_and_other_charges" class="form-control  pull-right number_format fixed-input" value="<?= $last_approval->inspection_and_other_charges  ?>">
                                </th>
                            </tr>
                            <?php

                            $grand_total = $total_amount + $last_approval->freight + $last_approval->inspection_and_other_charges;
                            if ($last_approval->vat_inclusive == 'VAT COMPONENT') {
                                $vat_amount = ($grand_total-$last_approval->inspection_and_other_charges)*$vat_factor;
                                $grand_total = $grand_total+$vat_amount;
                            } else {
                                $grand_total = ($total_amount + $last_approval->inspection_and_other_charges + $last_approval->freight);
                                $vat_amount = 0;
                            }
                            ?>
                            <tr>
                                <th colspan="2">VAT(18%)</th>
                                <th>
                                    <input type="text" name="vat" class="form-control pull-right number_format fixed-input" value="<?= number_format($vat_amount,2) ?>" readonly>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2">Total</th>
                                <th class="grand_total_display" style="text-align: right"><?= number_format($grand_total, 2) ?></th>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <th colspan="2">Freight</th>
                                <th>
                                    <input type="text" name="freight" class="form-control pull-right number_format fixed-input" value="<?= $requisition->freight ?>">
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2">Inspection &amp; Other Charges</th>
                                <th>
                                    <input type="text" name="inspection_and_other_charges" class="form-control  pull-right number_format fixed-input" value="<?= $requisition->inspection_and_other_charges  ?>">
                                </th>
                            </tr>
                            <?php

                            $grand_total = $total_amount + $requisition->freight + $requisition->inspection_and_other_charges;
                            if ($requisition->vat_inclusive == 'VAT COMPONENT') {
                                $vat_amount = ($grand_total-$requisition->inspection_and_other_charges)*$vat_factor;
                                $grand_total = $grand_total+$vat_amount;
                            } else {
                                $grand_total = ($total_amount + $requisition->inspection_and_other_charges + $requisition->freight);
                                $vat_amount = 0;
                            }

                            ?>
                            <tr>
                                <th colspan="2">VAT</th>
                                <th>
                                    <input type="text" name="vat" class="form-control pull-right number_format fixed-input" value="<?= $vat_amount ?>" readonly>
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
            <?php if ($last_approval) {
                $object = $last_approval;
            } else {
                $object = $requisition;
            } ?>
            <div class="row col-md-12">
                <div class="form-group col-md-2">
                    <input <?= $object->vat_inclusive == 'VAT PRICED' ? 'disabled' : '' ?> type="checkbox" name="vat_inclusive" <?= !is_null($object->vat_inclusive) ? 'checked' : '' ?>>
                    <input type="hidden" name="vat_priced_po" value="<?= $object->vat_inclusive == 'VAT PRICED' ? 'true' : 'false' ?>">
                    <label for="vat_inclusive" class="control-label text-center">&nbsp;&nbsp;Include VAT</label>
                </div>
                <div class="form-group col-md-2" <?= $object->vat_inclusive == null ? 'style="display: none;"' : '' ?>>
                    <?php $vat_options = array(0 => 'VAT@0%', 15 => 'VAT@15%', 18 => 'VAT@18%') ?>
                    <?= form_dropdown('vat_percentage', $vat_options, $object->vat_percentage, ' class="form-control searchable"') ?>
                </div>
            </div>
            <?php
            $current_approval_level = $requisition->current_approval_level();
            $next_level = $current_approval_level->next_level();
            if ($can_override_prev && $next_level) {
            ?>
                <div class="row col-md-12">
                    <div class="form-group col-md-4">
                        <input type="checkbox" name="set_final">
                        <label for="set_final" class="control-label text-center">Finalize Approval</label>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="col-md-12">
            <div class="form-group <?= $next_level ? 'col-md-8' : '' ?>" style="padding: 2rem">
                <label for="comments" class="control-label">Approving Comments</label>
                <textarea name="comments" class="form-control" style="height: 8%"><?= $last_approval ? $last_approval->approving_comments : $requisition->requesting_comments ?></textarea>
            </div>
            <?php
            if ($next_level) { ?>
                <div class="form-group col-md-4" style="padding: 2rem">
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

        <div class="col-xs-12">
            <button type="button" value="reject" class="btn btn-danger btn-sm approve_button">Reject</button>
            <button type="button" value="approve" class="btn btn-default btn-sm approve_button">Submit Approval</button>
        </div>
    </div>
</div>

<script>
    function approve_requisition(button) {
        var modal = button.closest('.modal');
        var status = button.attr('value') == 'reject' ? 'REJECTED' : '';
        var requisition_id = modal.find('input[name="requisition_id"]').val();
        var has_sources = modal.find('input[name="has_sources"]').val();
        var approval_chain_level_id = modal.find('input[name="approval_chain_level_id"]').val();
        var returned_chain_level_id = modal.find('select[name="returned_chain_level_id"]').val();
        var currency_id = modal.find('select[name="currency_id"]').val();
        var approve_date = modal.find('input[name="approve_date"]').val(),
            i = 0;
        var item_ids = new Array(),
            expense_account_ids = [],
            item_types = Array(),
            quantities = new Array(),
            source_types = new Array(),
            sources = new Array(),
            rates = new Array(),
            remarks = new Array();
        var tbody = modal.find(' .major_table_tbody');

        var error = 0;
        tbody.find('input[name="item_id"]').each(function() {
            var row = $(this).closest('tr');
            var row_index = 0;
            item_ids[i] = $(this).val();
            item_types[i] = row.find('input[name="item_type"]').val();
            expense_account_ids[i] = row.find('select[name="expense_account_id"]').val();
            if (has_sources == 'true') {
                var row_quantities = new Array(),
                    row_rates = new Array(),
                    row_source_types = new Array(),
                    row_currencies = new Array(),
                    row_sources = new Array();
                row.find('.source_approved_quantity').each(function() {
                    var source_row = $(this).closest('tr');
                    row_quantities[row_index] = $(this).val();
                    row_rates[row_index] = source_row.find('input[name="rate"]').unmask();
                    row_currencies[row_index] = source_row.find('select[name="currency_id"]').val();
                    row_source_types[row_index] = source_row.find('select[name="source_type"]').val();
                    if (item_types[i] == 'cash') {
                        row_sources[row_index] = source_row.find('input[name="payee"]').val();
                    } else {
                        row_sources[row_index] = row_source_types[row_index] == 'cash' ? source_row.find('input[name="payee"]').val() : source_row.find('select[name="source"]').val();
                    }
                    if ((row_source_types[row_index] != 'cash' && item_types[i] != 'cash') && (row_sources[row_index] == '' || row_source_types[row_index] == '')) {
                        error++;
                    }
                    row_index++;
                });

                quantities[i] = row_quantities;
                rates[i] = row_rates;
                sources[i] = row_sources;
                source_types[i] = row_source_types;
            } else {
                item_types[i] = row.find('input[name="item_type"]').val();
                quantities[i] = row.find('input[name="quantity"]').val();
                rates[i] = row.find('input[name="rate"]').unmask();
            }
            i++;
        });

        if (approve_date != '' && error == 0) {
            var freight = modal.find('input[name="freight"]').unmask();
            var inspection_and_other_charges = modal.find('input[name="inspection_and_other_charges"]').unmask();
            var vat_priced_po = modal.find('input[name="vat_priced_po"]').val();
            var vat_inclusive;
            if (vat_priced_po == 'true') {
                vat_inclusive = modal.find('input[name="vat_inclusive"]').is(':checked') ? 'VAT PRICED' : 'NULL';
            } else {
                vat_inclusive = modal.find('input[name="vat_inclusive"]').is(':checked') ? 'VAT COMPONENT' : 'NULL';
            }
            var vat_percentage = modal.find('select[name="vat_percentage"]').val();
            var freight_charges = parseFloat(freight);
            var inspection_charges = parseFloat(inspection_and_other_charges);
            var comments = modal.find('textarea[name="comments"]').val();
            var set_final = modal.find('input[name="set_final"]').is(':checked') ? 1 : null;
            var forward_to = modal.find('select[name="forward_to"]').val();

            var send_data = function() {
                modal.modal('hide');
                start_spinner();
                $.post(
                    base_url + "requisitions/approve_requisition/", {
                        requisition_id: requisition_id,
                        has_sources: has_sources,
                        approval_chain_level_id: approval_chain_level_id,
                        returned_chain_level_id: returned_chain_level_id,
                        quantities: quantities,
                        rates: rates,
                        currency_id: currency_id,
                        expense_account_ids: expense_account_ids,
                        approve_date: approve_date,
                        item_ids: item_ids,
                        sources: sources,
                        source_types: source_types,
                        item_types: item_types,
                        remarks: remarks,
                        status: status,
                        set_final: set_final,
                        forward_to: forward_to,
                        freight: freight_charges,
                        inspection_and_other_charges: inspection_charges,
                        vat_inclusive: vat_inclusive,
                        vat_percentage: vat_percentage,
                        comments: comments
                    },
                    function(data) {
                        toast('success', 'Your Approval has been submitted');
                        modal.closest('.box').find('.requisitions_table').DataTable().draw('page');
                        initialize_common_js();
                    }
                ).complete(function() {
                    stop_spinner();
                });
            };

            if (status == 'REJECTED') {
                $.confirm({
                    title: 'Reject Requisition No. ' + requisition_id,
                    content: 'This action is irreversible! Are you sure?',
                    buttons: {
                        confirm: {
                            text: 'Confirm Reject',
                            btnClass: 'btn btn-danger btn-xs',
                            action: function() {
                                send_data();
                            }
                        },
                        cancel: {
                            text: "Cancel",
                            btnClass: 'btn btn-default btn-xs'
                        }
                    }
                });
            } else {
                send_data();
            }
        } else {
            display_form_fields_error();
        }
    }

    $('.approve_button').off('click').on('click', function(e) {
        if (e.handled !== true) {
            approve_requisition($(this));
            e.handled = true;
        }
    });
</script>