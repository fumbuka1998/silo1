<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 8/14/2018
 * Time: 9:03 AM
 */

$credit_account = $imprest_voucher->credit_account();
$debit_account = $imprest_voucher->debit_account();
$currency = $imprest_voucher->currency();
$sn = 0;
?>
<div style="width: 90%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title"> Retirement Form</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="retirement_date" class="control-label">Retirement Date</label>
                            <input type="text" class="form-control datepicker" name="retirement_date" value="<?= date('Y-m-d') ?>">
                            <input type="hidden" name="imprest_voucher_id" value="<?= $imprest_voucher->{$imprest_voucher::DB_TABLE_PK} ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="credit_account_name" class="control-label">Credit Account</label>
                            <input type="text" class="form-control" name="credit_account_name" value="<?= $credit_account->account_name ?>" readonly>
                            <input type="hidden" name="credit_account_id" value="<?= $credit_account->{$credit_account::DB_TABLE_PK} ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="debit_account_name" class="control-label">Debit Account</label>
                            <input type="text" class="form-control" name="debit_account_name" value="<?= $debit_account->account_name ?>" readonly>
                            <input type="hidden" name="debit_account_id" value="<?= $debit_account->{$debit_account::DB_TABLE_PK} ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="location_id" class="control-label">Location</label>
                            <?= form_dropdown('location_id',$location_options,'','class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="sub_location_id" class="control-label">Sub Location</label>
                            <?= form_dropdown('sub_location_id', [], '', 'class="form-control searchable sub_locations_display"') ?>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <table  width="100%" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th nowrap="true">Rate</th>
                                    <th nowrap="true">Amount</th>
                                    <th></th>
                                </tr>

                                <tr style="display: none" class="material_row_template">
                                    <td><?= '' ?></td>
                                    <td>
                                        <?= form_dropdown('item_id',$material_options,'','class="form-control"')?>
                                        <input type="hidden" name="item_type" value="material">
                                        <input type="hidden" name="added_item" value="material">
                                    </td>
                                    <td>
                                        <input type="text" name="quantity" value="" class="form-control">
                                    </td>
                                    <td class="unit_display"></td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" name="rate" value="" class="form-control">
                                            <span class="input-group-addon"><?= $currency->symbol ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <input style="text-align: right" type="text" name="amount" value="" class="form-control" readonly>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                <tr style="display: none" class="asset_row_template">
                                    <td></td>
                                    <td>
                                        <?= form_dropdown('asset_item_id',$asset_options,'','class="form-control"')?>
                                        <input type="hidden" name="item_type" value="asset">
                                        <input type="hidden" name="added_item" value="asset">
                                    </td>
                                    <td>
                                        <input type="text" name="quantity" value="" class="form-control">
                                    </td>
                                    <td>No.</td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" name="rate" value="" class="form-control">
                                            <span class="input-group-addon"><?= $currency->symbol ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <input style="text-align: right" type="text" name="amount" value="" class="form-control" readonly>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                <tr style="display: none" class="cash_row_template">
                                    <td></td>
                                    <td>
                                        <input type="text" name="cash_description" placeholder="Cash Item" value="" class="form-control"></td>
                                        <input type="hidden" name="item_type" value="cash">
                                    <td>
                                        <input type="text" name="quantity" value="" class="form-control">
                                    </td>
                                    <td></td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" name="rate" value="" class="form-control">
                                            <span class="input-group-addon"><?= $currency->symbol ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <input style="text-align: right" type="text" name="amount" value="" class="form-control" readonly>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                <tr style="display: none" class="service_row_template">
                                    <td></td>
                                    <td>
                                        <input type="text" name="service_description" placeholder="Service Item" value="<?= ''?>" class="form-control">
                                        <input type="hidden" name="item_type" value="service">
                                    </td>
                                    <td>
                                        <input type="text" name="quantity" value="" class="form-control">
                                    </td>
                                    <td></td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" name="rate" value="" class="form-control">
                                            <span class="input-group-addon"><?= $currency->symbol ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <input style="text-align: right" type="text" name="amount" value="" class="form-control" readonly>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_amount = 0;

                            $imprest_voucher_material_items = $imprest_voucher->material_items();
                            foreach ($imprest_voucher_material_items as $imprest_material_item) {
                                $sn++;
                                $approved_material_item = $imprest_material_item->requisition_approval_material_item();
                                $material = $approved_material_item->material_item();
                                if ($balance == 0) {
                                    $material_quantity = $approved_material_item->approved_quantity;
                                    $total_amount += $amount = $approved_material_item->approved_quantity * $approved_material_item->approved_rate;
                                } else {
                                    $material_quantity = $approved_material_item->approved_quantity - $imprest_material_item->retired_material($imprest_voucher->{$imprest_voucher::DB_TABLE_PK},$material->{$material::DB_TABLE_PK});
                                    $total_amount += $amount = $material_quantity * $approved_material_item->approved_rate;
                                }

                                if($material_quantity != 0) {
                                    ?>
                                    <tr>
                                        <td><?= $sn ?></td>

                                        <td><?= $material->item_name ?>
                                            <input type="hidden" name="description" value="<?= $material->item_name ?>">
                                            <input type="hidden" name="item_id" value="<?= $material->{$material::DB_TABLE_PK} ?>">
                                            <input type="hidden" name="item_type" value="material">
                                            <input type="hidden" name="added_item" value="">
                                        </td>
                                        <td style="text-align: right;width: 10%">
                                            <input type="text" name="quantity" class="form-control"
                                                   value="<?= $material_quantity ?>">
                                        </td>
                                        <?php

                                        ?>
                                        <td><?= $material->unit()->symbol ?></td>

                                        <td nowrap="nowrap" style="text-align: right;width: 20%">
                                            <div class="input-group">
                                                <input type="text" name="rate" class="form-control money"
                                                       value="<?= number_format($approved_material_item->approved_rate, 2) ?>">
                                                <span class="input-group-addon currency_display"> <?= $approved_material_item->currency()->symbol ?></span>
                                            </div>
                                        </td>
                                        <td><input style="text-align: right" name="amount" readonly class="form-control number_format" value="<?= $amount ?>">
                                        </td>
                                        <td>
                                            <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }

                            $imprest_voucher_asset_items = $imprest_voucher->asset_items();
                            foreach ($imprest_voucher_asset_items as $imprest_asset_item) {
                                $sn++;
                                $approved_asset_item = $imprest_asset_item->requisition_approval_asset_item();
                                $asset_item = $approved_asset_item->requisition_asset_item()->asset_item();
                                if ($balance == 0) {
                                    $asset_quantity = $approved_asset_item->approved_quantity;
                                    $total_amount += $amount = $approved_asset_item->approved_quantity * $approved_asset_item->approved_rate;
                                } else {
                                    $asset_quantity = $approved_asset_item->approved_quantity - $imprest_asset_item->retired_asset($imprest_voucher->{$imprest_voucher::DB_TABLE_PK},$asset_item->{$asset_item::DB_TABLE_PK});
                                    $total_amount += $amount = $asset_quantity * $approved_asset_item->approved_rate;
                                }

                                if($asset_quantity != 0) {
                                    ?>
                                    <tr>
                                        <td><?= $sn ?></td>
                                        <td><?= $asset_item->asset_name ?>
                                            <input type="hidden" name="description" value="<?= $asset_item->asset_name ?>">
                                            <input type="hidden" name="asset_item_id" value="<?= $asset_item->{$asset_item::DB_TABLE_PK} ?>">
                                            <input type="hidden" name="item_type" value="asset">
                                            <input type="hidden" name="added_item" value="">
                                        </td>

                                        <td style="text-align: right;width: 10%">
                                            <input type="text" name="quantity" class="form-control"
                                                   value="<?= $asset_quantity ?>">
                                        </td>
                                        <td></td>
                                        <td nowrap="nowrap" style="text-align: right;width: 20%">
                                            <div class="input-group">
                                                <input type="text" name="rate" class="form-control money"
                                                       value="<?= number_format($approved_asset_item->approved_rate, 2) ?>">
                                                <span class="input-group-addon currency_display"> <?= $approved_asset_item->currency()->symbol ?></span>
                                            </div>
                                        </td>
                                        <td><input style="text-align: right" name="amount" readonly
                                                   class="form-control number_format" value="<?= $amount  ?>">
                                        </td>
                                        <td>
                                            <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }

                            $imprest_voucher_cash_items = $imprest_voucher->cash_items();
                            foreach ($imprest_voucher_cash_items as $imprest_cash_item) {
                                $sn++;
                                $approved_cash_item = $imprest_cash_item->requisition_approval_cash_item();
                                if ($balance == 0) {
                                    $cash_quantity = $approved_cash_item->approved_quantity;
                                    $total_amount += $amount = $approved_cash_item->approved_quantity * $approved_cash_item->approved_rate;
                                } else {
                                    $cash_quantity = $approved_cash_item->approved_quantity - $imprest_cash_item->retired_cash_item();
                                    $total_amount += $amount = $cash_quantity * $approved_cash_item->approved_rate;
                                }

                                if($cash_quantity != 0) {
                                    ?>
                                    <tr>
                                        <td><?= $sn ?></td>
                                        <td>
                                            <input type="text" name="cash_description" readonly
                                                   value="<?= $approved_cash_item->requisition_cash_item()->description ?>"
                                                   class="form-control">
                                            <input type="hidden" name="imprest_voucher_cash_item_id" value="<?= $imprest_cash_item->{$imprest_cash_item::DB_TABLE_PK} ?>">
                                            <input type="hidden" name="item_type" value="cash">
                                        </td>
                                        <td style="text-align: right;width: 10%">
                                            <input type="text" name="quantity" class="form-control"
                                                   value="<?= $cash_quantity ?>">
                                        </td>

                                        <td><?= $approved_cash_item->requisition_cash_item()->measurement_unit()->symbol ?></td>

                                        <td nowrap="nowrap" style="text-align: right;width: 20%">
                                            <div class="input-group">
                                                <input type="text" name="rate" class="form-control money"
                                                       value="<?= number_format($approved_cash_item->approved_rate, 2) ?>">
                                                <span class="input-group-addon currency_display"> <?= $approved_cash_item->currency()->symbol ?></span>
                                            </div>
                                        </td>
                                        <td><input style="text-align: right" name="amount" readonly
                                                   class="form-control number_format" value="<?= $amount ?>">
                                        </td>
                                        <td>
                                            <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }

                            $imprest_voucher_service_items = $imprest_voucher->service_items();
                            foreach ($imprest_voucher_service_items as $imprest_service_item) {
                            $sn++;
                            $approved_service_item = $imprest_service_item->requisition_approval_service_item();
                            if($balance == 0){
                                $service_qunatity = $approved_service_item->approved_quantity;
                                $total_amount += $amount = $approved_service_item->approved_quantity * $approved_service_item->approved_rate;
                            } else {
                                $service_qunatity = $approved_service_item->approved_quantity - $imprest_service_item->retired_service();
                                $total_amount += $amount = $service_qunatity * $approved_service_item->approved_rate;
                            }

                            if($service_qunatity != 0) {
                                ?>
                                <tr>
                                    <td><?= $sn ?></td>
                                    <td>
                                        <input type="text" name="service_description" readonly value="<?= $approved_service_item->requisition_service_item()->description ?>" class="form-control">
                                        <input type="hidden" name="imprest_voucher_service_item_id" value="<?= $imprest_service_item->{$imprest_service_item::DB_TABLE_PK} ?>">
                                        <input type="hidden" name="item_type" value="service">
                                    </td>
                                    <td style="text-align: right;width: 10%">
                                        <input type="text" name="quantity" class="form-control"
                                               value="<?= $service_qunatity ?>">
                                    </td>
                                    <td><?= $approved_service_item->requisition_service_item()->measurement_unit()->symbol ?></td>
                                    <td nowrap="nowrap" style="text-align: right;width: 20%">
                                        <div class="input-group">
                                            <input type="text" name="rate" class="form-control money"
                                                   value="<?= number_format($approved_service_item->approved_rate, 2) ?>">
                                            <span class="input-group-addon currency_display"> <?= $approved_service_item->requisition_service_item()->currency_symbol() ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <input style="text-align: right" name="amount" readonly class="form-control number_format" value="<?= $amount ?>">
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
                            <tr>
                                <td></td>
                                <th style="text-align: right" colspan="4">TOTAL</th>
                                <th class="number_format total_amount_display" style="text-align: right"><?= $total_amount ?></th>
                                <th></th>
                            </tr>

                            <?php
                            if(!is_null($imprest_voucher->vat_inclusive)){
                                if($imprest_voucher->vat_inclusive == 'VAT PRICED'){
                                    $total_amount_vat_exclusive = $total_amount/1.18;
                                    $vat_amount = $total_amount - $total_amount_vat_exclusive;
                                } else {
                                    $vat_amount = $total_amount*0.18;
                                }
                                ?>
                                <tr>
                                    <td></td>
                                    <th style="text-align: right" colspan="4">VAT </th>
                                    <th style="text-align: right"><?= number_format($vat_amount,2) ?></th>
                                    <th></th>
                                </tr>

                                <?php
                                $grand_total = $total_amount + $vat_amount;
                                ?>
                                <tr  style="background-color: #dfdfdf">
                                    <td></td>
                                    <th style="text-align: right" colspan="4">GRAND TOTAL</th>
                                    <th style="text-align: right"><?= number_format($grand_total,2) ?></th>
                                    <th></th>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="7">
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-xs btn-default material_row_adder">
                                            <i class="fa fa-plus"></i> Material Item
                                        </button>
                                        <button type="button" class="btn btn-xs btn-default asset_row_adder">
                                            <i class="fa fa-plus"></i> Asset Item
                                        </button>
                                        <button type="button" class="btn btn-xs btn-default cash_row_adder">
                                            <i class="fa fa-plus"></i> Cash Item
                                        </button>
                                        <button type="button" class="btn btn-xs btn-default service_row_adder">
                                            <i class="fa fa-plus"></i> Service Item
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="form-group col-xs-12">
                        <label for="remarks" class="control-label">Remarks</label>
                        <textarea name="remarks" class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_imprest_voucher_retirement">Submit</button>
            </div>
        </form>
    </div>
</div>