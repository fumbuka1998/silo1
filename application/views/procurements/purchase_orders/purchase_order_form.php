<?php
$edit = isset($order);
if ($edit) {
    $project_junction = $order->project_purchase_order();
    $cost_center_junction = $order->cost_center_purchase_order();
    if($project_junction){
        $project = $project_junction->project();
        $project_options = [$project->{$project::DB_TABLE_PK} => $project->project_name];
    } else {
        $cost_center = $cost_center_junction->cost_center();
        $cost_center_options = [$cost_center->{$cost_center::DB_TABLE_PK} => $cost_center->cost_center_name];
    }

    $currency_options = [$currency->{$currency::DB_TABLE_PK} => $currency->currency_name];
    $vat_factor = $order->vat_percentage/100;
}

$material_options = isset($material_options) ? $material_options : material_item_dropdown_options('all');
$asset_options = isset($asset_options) ? $asset_options : asset_item_dropdown_options();
$stakeholders_options = isset($stakeholder) ? [$stakeholder->{$stakeholder::DB_TABLE_PK} => $stakeholder->stakeholder_name] : $stakeholders_options;
$purchase_order_type_options = $edit ?
    ($project_junction ? ['project_purchase_order' => 'Project Purchase Order'] : ['cost_center_purchase_order' => 'Cost Center Purchase Order']) :
    [
    'project_purchase_order' => 'Project Purchase Order',
    'cost_center_purchase_order' => 'Cost Center Purchase Order'
];

?>
<div class="modal-dialog" style="width: 80%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Purchase Order Form</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-3">
                            <label for="vendor_id" class="control-label">Vendor</label>
                            <input type="hidden" name="order_id" value="<?= $edit ? $order->{$order::DB_TABLE_PK} : '' ?>">
                            <?= form_dropdown('vendor_id', $stakeholders_options, $edit ? $order->stakeholder_id : '', ' class="form-control" ') ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="currency_id" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id', $currency_options, $edit ? $order->currency_id : '', ' readonly class="form-control" ') ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="location_id" class="control-label">Delivery Location</label>
                            <?= form_dropdown('location_id', $locations_options, $edit ? $order->location_id : '', ' class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="" class="control-label">Order Type</label>
                            <?= form_dropdown('order_type', $purchase_order_type_options, $edit && !$project_junction ? 'cost_center_purchase_order' : 'project_purchase_order', ' class="form-control searchable"') ?>
                        </div>
                        <?php if (!$edit || $project_junction) { ?>
                            <div class="form-group col-md-3 project_options_form_group">
                                <label for="" class="control-label">Project</label>
                                <?= form_dropdown('project_id', $project_options, $edit && $project_junction ? $project_junction->project_id : '', ' class="form-control"') ?>
                            </div>
                        <?php }

                        if (!$edit || $cost_center_junction) {
                        ?>
                            <div <?= !$edit ? 'style="display: none"' : '' ?> class="form-group col-md-3 cost_center_options_form_group">
                                <label for="" class="control-label">Cost Center</label>
                                <?= form_dropdown('cost_center_id', $cost_center_options, $edit ? $cost_center_junction->cost_center_id : '', ' class="form-control searchable"') ?>
                            </div>
                        <?php } ?>
                        <div class="form-group col-md-3">
                            <label for="issue_date" class="control-label">Reference</label>
                            <input type="text" class="form-control" name="reference" value="<?= $edit ? $order->reference : '' ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="issue_date" class="control-label">Issue Date</label>
                            <input type="text" class="form-control datepicker" name="issue_date" value="<?= $edit ? $order->issue_date : date('Y-m-d') ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="delivery_date" class="control-label">Delivery Date</label>
                            <input type="text" class="form-control datepicker" name="delivery_date" value="<?= $edit ? $order->delivery_date : '' ?>">
                        </div>
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Amount</th>
                                    <th>Remarks</th>
                                    <th></th>
                                </tr>
                                <tr style="display: none" class="material_row_template">
                                    <td style="width: 30%">
                                        <?= form_dropdown('material_id', $material_options, '', ' class="form-control "') ?>
                                        <input type="hidden" name="item_type" value="material">
                                    </td>
                                    <td class="unit_display"></td>
                                    <td><input name="quantity" class="form-control"></td>
                                    <td><input name="rate" class="form-control money"></td>
                                    <td><input name="amount" readonly class="form-control money"></td>
                                    <td><textarea name="remarks" rows="1" class="form-control"></textarea></td>
                                    <td>
                                        <button class="btn btn-danger btn-xs row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                <tr style="display: none" class="asset_row_template">
                                    <td style="width: 30%">
                                        <?= form_dropdown('asset_item_id', $asset_options, '', ' class="form-control "') ?>
                                        <input type="hidden" name="item_type" value="asset">
                                    </td>
                                    <td></td>
                                    <td><input name="quantity" class="form-control"></td>
                                    <td><input name="rate" class="form-control money"></td>
                                    <td><input name="amount" readonly class="form-control money"></td>
                                    <td><textarea name="remarks" rows="1" class="form-control"></textarea></td>
                                    <td>
                                        <button class="btn btn-danger btn-xs row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                <tr style="display: none" class="service_row_template">
                                    <td style="width: 30%">
                                        <input name="service_description" placeholder="Service Description" class="form-control">
                                        <input type="hidden" name="item_type" value="service">
                                    </td>
                                    <td style="width: 3%;"><?= form_dropdown('uom_id', $measurement_unit_options, '', ' class="form-control vendor_id"') ?></td>
                                    <td><input name="quantity" class="form-control"></td>
                                    <td><input name="rate" class="form-control money"></td>
                                    <td><input name="amount" readonly class="form-control money"></td>
                                    <td><textarea name="remarks" rows="1" class="form-control"></textarea></td>
                                    <td>
                                        <button class="btn btn-danger btn-xs row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                            </thead>
                            <tbody>
                                <?php
                                if (!$edit) {

                                ?>
                                    <tr>
                                        <td style="width: 30%">
                                            <?= form_dropdown('material_id', $material_options, '', ' class="form-control"') ?>
                                            <input type="hidden" name="item_type" value="material">
                                        </td>
                                        <td class="unit_display"></td>
                                        <td><input name="quantity" class="form-control"></td>
                                        <td><input name="rate" class="form-control money"></td>
                                        <td><input name="amount" readonly class="form-control money"></td>
                                        <td><textarea name="remarks" rows="1" class="form-control"></textarea></td>
                                        <td>
                                            <button class="btn btn-danger btn-xs row_remover"><i class="fa fa-close"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                } else {
                                    $total_items_amount = 0;
                                    $material_items = $order->material_items();
                                    foreach ($material_items as $item) {
                                        $material = $item->material_item();
                                        $unit = $material->unit()->symbol;
                                        $vat_exclusive_price = $order->vat_inclusive == 'VAT PRICED' ? ($item->price/(1+$vat_factor)) : $item->price;
                                        $total_items_amount += $amount = $item->quantity * $vat_exclusive_price;
                                    ?>
                                        <tr>
                                            <td style="width: 30%">
                                                <?= form_dropdown('material_id', [$item->material_item_id => $material->name_with_part_number()], $item->material_item_id, ' class="form-control searchable"') ?>
                                                <input type="hidden" name="item_type" value="material">
                                            </td>
                                            <td class="unit_display"><?= $unit ?></td>
                                            <td><input name="quantity" readonly class="form-control" value="<?= $item->quantity ?>"></td>
                                            <td><input name="rate" readonly class="form-control money" value="<?= number_format($vat_exclusive_price, 3) ?>"></td>
                                            <td><input name="amount" readonly class="form-control money" value="<?= number_format($amount, 3) ?>">
                                            </td>
                                            <td>
                                                <textarea name="remarks" rows="1" class="form-control">
                                                <?= $item->remarks ?>
                                            </textarea>
                                            </td>
                                            <td>
                                                <!--<button class="btn btn-danger btn-xs row_remover"><i class="fa fa-close"></i>
                                                </button>-->
                                            </td>
                                        </tr>
                                    <?php
                                    }

                                    $asset_items = $order->asset_items();
                                    foreach ($asset_items as $item) {
                                        $asset_item = $item->asset_item();
                                        $asset_options = [$item->asset_item_id => $asset_item->asset_name];
                                        $vat_exclusive_price = $order->vat_inclusive == 'VAT PRICED' ? ($item->price/(1+$vat_factor)) : $item->price;
                                        $total_items_amount += $amount = $item->quantity * $vat_exclusive_price;
                                    ?>
                                        <tr>
                                            <td style="width: 30%">
                                                <?= form_dropdown('asset_item_id', $asset_options, $item->asset_item_id, ' class="form-control searchable "') ?>
                                                <input type="hidden" name="item_type" value="asset">
                                            </td>
                                            <td></td>
                                            <td><input name="quantity" readonly class="form-control" value="<?= $item->quantity ?>"></td>
                                            <td><input name="rate" readonly class="form-control money" value="<?= number_format($vat_exclusive_price, 3) ?>"></td>
                                            <td><input name="amount" readonly class="form-control money" value="<?= number_format($amount, 3) ?>">
                                            </td>
                                            <td>
                                                <textarea name="remarks" rows="1" class="form-control"><?= $item->remarks ?></textarea>
                                            </td>
                                            <td>
                                                <!--<button class="btn btn-danger btn-xs row_remover">
                                                    <i class="fa fa-close"></i>
                                                </button>-->
                                            </td>
                                        </tr>
                                    <?php
                                    }

                                    $service_items = $order->service_items();
                                    foreach ($service_items as $item) {
                                        $vat_exclusive_price = $order->vat_inclusive == 'VAT PRICED' ? ($item->price/(1+$vat_factor)) : $item->price;
                                        $total_items_amount += $amount = $item->quantity * $vat_exclusive_price;
                                    ?>
                                        <tr>
                                            <td style="width: 30%">
                                                <input readonly name="service_description" class="form-control" value="<?= $item->description ?>">
                                                <input type="hidden" name="item_type" value="service">
                                            </td>
                                            <td style="width: 3%;"><?= form_dropdown('uom_id', $measurement_unit_options, $item->measurement_unit_id, ' class="form-control " readonly') ?></td>
                                            <td><input name="quantity" readonly class="form-control" value="<?= $item->quantity ?>"></td>
                                            <td><input name="rate" readonly class="form-control money" value="<?= number_format($vat_exclusive_price, 3) ?>"></td>
                                            <td><input name="amount" readonly class="form-control money" value="<?= number_format($amount, 3) ?>">
                                            </td>
                                            <td><textarea name="remarks" rows="1" class="form-control"><?= $item->remarks ?></textarea></td>
                                            </td>
                                            <td>
                                                <!--<button class="btn btn-danger btn-xs row_remover">
                                                    <i class="fa fa-close"></i>
                                                </button>-->
                                            </td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr class="text_styles">
                                    <th colspan="4">TOTAL</th>
                                    <th style="text-align: right" class="total_amount_display"><?= $edit ? number_format($total_items_amount, 3) : 0 ?></th>
                                    <th nowrap style="text-align: right" colspan="2">
                                        <?php if(!$edit){ ?>
                                        <button type="button" class="btn btn-xs btn-default material_row_adder">
                                            <i class="fa fa-plus"></i> Material
                                        </button>
                                        <button type="button" class="btn btn-xs btn-default asset_row_adder">
                                            <i class="fa fa-plus"></i> Asset
                                        </button>
                                        <button type="button" class="btn btn-xs btn-default service_row_adder">
                                            <i class="fa fa-plus"></i> Service
                                        </button>
                                        <?php } ?>
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="4" style="text-align: right">FREIGHT CHARGES</td>
                                    <td>
                                        <?php
                                            $freight = $order->vat_inclusive == "VAT PRICED" ? $order->freight/(1+$vat_factor) : $order->freight;
                                        ?>
                                        <input name="freight" class="form-control money text-right"<?= $edit ? 'readonly' : '' ?> value="<?= $edit ? $freight  : '' ?>">
                                    </td>
                                    <td colspan="2" rowspan="4">
                                        <div style="text-align: center" class="form-group">
                                            <?php if(1==1){ ?>
                                            <input <?= $edit ? 'disabled' : '' ?> type="checkbox" name="vat_inclusive"  <?= $edit && !is_null($order->vat_inclusive) ? 'checked' : '' ?>>

                                            <input type="hidden" name="vat_priced_po" value="<?= $edit && $order->vat_inclusive == 'VAT PRICED' ? 'true' : 'false' ?>">
                                            <label for="vat_inclusive" class="control-label text-center"> Include VAT </label>
                                            <?php } ?>
                                        </div>
                                        <div class="form-group" <?php if ($edit) {
                                                                    echo 'style="display: none;"';
                                                                } ?>>
                                            <?php $vat_options = array(0 => 'VAT@0%', 15 => 'VAT@15%', 18 => 'VAT@18%') ?>
                                            <?= form_dropdown('vat_percentage', $vat_options, $edit ? $order->vat_percentage : '', ($edit ? 'readonly' : '').' class="form-control searchable"') ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="text-align: right">INSPECTION AND OTHER CHARGES</td>
                                    <td>
                                        <?php
                                            $inspection_and_other_charges = $order->vat_inclusive == "VAT PRICED" ? $order->inspection_and_other_charges/(1+$vat_factor) : $order->inspection_and_other_charges;
                                        ?>
                                        <input name="inspection_and_other_charges" class="form-control money text-right" <?= $edit ? 'readonly' : '' ?> value="<?= $edit ? $inspection_and_other_charges : '' ?>">
                                    </td>
                                </tr>
                                <?php

                                if ($edit) {

                                    $vat_amount = 0;
                                    $total_amount = $total_items_amount + $freight+$inspection_and_other_charges;
                                    if(!is_null($order->vat_inclusive)){
                                        $vat_amount = $order->vat_inclusive == "VAT PRICED" ?  $total_amount*$vat_factor : ($total_amount-$inspection_and_other_charges)*$vat_factor;
                                        $grand_total = $total_amount+$vat_amount;
                                    } else {
                                        $grand_total = $total_amount;
                                    }
                                } else {
                                    $vat_amount = '';
                                }
                                ?>
                                <tr>
                                    <td colspan="4" style="text-align: right;">VAT</td>
                                    <td><input style="text-align: right; width: 150px;" name="vat" class="form-control money text-right" value="<?= $edit ? $vat_amount : '' ?>" readonly></td>
                                </tr>
                                <tr class="text_styles">
                                    <th colspan="4" style="text-align: right">GRAND TOTAL</th>
                                    <th class="grand_total_display" style="text-align: right"><?= $edit ? number_format($grand_total, 2) : 0 ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="form-group col-md-8">
                        <label for="comments" class="control-label">Terms &amp Conditions</label>
                        <textarea name="comments" class="form-control"><?= $edit ? $order->comments : '' ?></textarea>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="location_id" class="control-label">Assign Handler</label>
                        <?= form_dropdown('handler_id', $procurement_members_options, $edit ? $order->handler_id : '', ' class="form-control searchable"') ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_purchase_order">Save Order</button>
            </div>
        </form>
    </div>
</div>