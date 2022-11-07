<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 9/5/2018
 * Time: 3:32 PM
 */

    $location_options = isset($location) ? [$location->{$location::DB_TABLE_PK} => $location->location_name] : locations_options();
    $currency = $retirement->imprest_voucher()->currency();
    $imprest_voucher = $retirement->imprest_voucher();
?>
<div style="width: 90%" class="modal-dialog modal-lg">
    <form>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Imprest Retirement Examination</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div style="font-size: 15px" class="col-xs-12">

                        <input type="hidden" class="datepicker" name="examination_date" value="<?= date('Y-m-d')?>">
                        <input type="hidden"  name="imprest_voucher_retirement_id" value="<?= $retirement->{$retirement::DB_TABLE_PK} ?>">
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header with-border bg-gray-light">
                                    <p style="text-align: center; font-weight: bold; font-size: 16px"><strong><?= $retirement->imprest_voucher()->debit_account()->account_name ?></strong></p>
                                </div>
                                <div class="box-body">
                                    <table style="font-size: 15px" width="100%">
                                        <tr>
                                            <td style=" width: 30% vertical-align: top">
                                                    <div class="form-group col-sm-12">
                                                        <label class="col-sm-4 control-label"><strong>Imprest Date : </strong></label>
                                                        <div class="form-control-static col-sm-8">
                                                            <?= custom_standard_date($imprest_voucher->imprest_date) ?>
                                                        </div>
                                                    </div>
                                            </td>
                                            <td style=" width: 30% vertical-align: top">
                                                    <div class="form-group col-sm-12">
                                                        <label class="col-sm-4 control-label"><strong>Retirement Date : </strong></label>
                                                        <div class="form-control-static col-sm-8">
                                                            <?= custom_standard_date($retirement->retirement_date) ?>
                                                        </div>
                                                    </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style=" width: 30% vertical-align: top">
                                                    <div class="form-group col-sm-12">
                                                        <label class="col-sm-4 control-label"><strong>Currency : </strong></label>
                                                        <div class="form-control-static col-sm-8">
                                                            <?= $currency->symbol ?>
                                                        </div>
                                                    </div>
                                            </td>
                                            <td style=" width: 30% vertical-align: top">
                                                    <div class="form-group col-sm-12">
                                                        <label class="col-sm-4 control-label"><strong>Exchange Rate : </strong></label>
                                                        <div class="form-control-static col-sm-8">
                                                            <?= currency_exchange_rate($currency->{$currency::DB_TABLE_PK}) ?>
                                                        </div>
                                                    </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style=" width: 30% vertical-align: top">
                                                    <div class="form-group col-sm-12">
                                                        <label class="col-sm-4 control-label"><strong>Location : </strong></label>
                                                        <div class="form-control-static col-sm-8">
                                                            <?= $retirement->location()->location_name ?>
                                                        </div>
                                                    </div>
                                            </td>
                                            <td style=" width: 30% vertical-align: top">
                                                    <div class="form-group col-sm-12">
                                                        <label class="col-sm-4 control-label"><strong>Sub Location : </strong></label>
                                                        <div class="form-control-static col-sm-8">
                                                            <?= $retirement->location()->location_name ?>
                                                        </div>
                                                    </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style=" width: 30% vertical-align: top">
                                                    <div class="form-group col-sm-12">
                                                        <label class="col-sm-4 control-label"><strong>Retirement No : </strong></label>
                                                        <div class="form-control-static col-sm-8">
                                                            <?= $retirement->imprest_voucher_retirement_number() ?>
                                                        </div>
                                                    </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <table  width="100%" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Description</th>
                                        <th>Unit</th>
                                        <th>Quantity</th>
                                        <th nowrap="true">Rate</th>
                                        <th nowrap="true">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php
                                     $sn = $total_amount = 0;
                                     $retired_material_items = $retirement->retired_material_items();
                                     if(!empty($retired_material_items)) {
                                         foreach ($retired_material_items as $retired_material_item) {
                                             $sn++;
                                             $total_amount += $amount = $retired_material_item->rate * $retired_material_item->quantity;
                                             ?>
                                             <tr>
                                                 <td class="bordered"><?= $sn ?></td>
                                                 <td class="bordered"><?= $retired_material_item->material_item()->item_name ?></td>
                                                 <td class="bordered"><?= $retired_material_item->material_item()->unit()->symbol ?></td>
                                                 <td class="bordered"
                                                     style="text-align: right"><?= $retired_material_item->quantity ?></td>
                                                 <td class="right_bordered"
                                                     style="text-align: right"><?= number_format($retired_material_item->rate, 2) ?></td>
                                                 <td class="right_bordered"
                                                     style="text-align: right"><?= $currency->symbol . ' ' . number_format($amount, 2) ?></td>
                                             </tr>
                                             <?php
                                         }
                                     }
                                     $retired_asset_items = $retirement->retired_asset_items();
                                     if(!empty($retired_asset_items)) {
                                         foreach ($retired_asset_items as $retired_asset_item) {
                                             $sn++;
                                             $total_amount += $amount = $retired_asset_item->book_value * $retired_asset_item->quantity;
                                             ?>
                                             <tr>
                                                 <td class="bordered"><?= $sn ?></td>
                                                 <td class="bordered"><?= $retired_asset_item->asset_item()->asset_name ?></td>
                                                 <td class="bordered">No.</td>
                                                 <td class="bordered"
                                                     style="text-align: right"><?= $retired_asset_item->quantity ?></td>
                                                 <td class="right_bordered"
                                                     style="text-align: right"><?= number_format($retired_asset_item->book_value, 2) ?></td>
                                                 <td class="right_bordered"
                                                     style="text-align: right"><?= $currency->symbol . ' ' . number_format($amount, 2) ?></td>
                                             </tr>
                                             <?php
                                         }
                                     }
                                     $retired_cash_items = $retirement->retired_cash();
                                     if(!empty($retired_cash_items)) {
                                         foreach ($retired_cash_items as $cash_item) {
                                             $sn++;
                                             $total_amount += $amount = $cash_item->rate * $cash_item->quantity;
                                             ?>
                                             <tr>
                                                 <td class="bordered"><?= $sn ?></td>
                                                 <td class="bordered"><?= $cash_item->description ?></td>
                                                 <td class="bordered">No.</td>
                                                 <td class="bordered" style="text-align: right"><?= $cash_item->quantity ?></td>
                                                 <td class="right_bordered" style="text-align: right"><?= number_format($cash_item->rate, 2) ?></td>
                                                 <td class="right_bordered" style="text-align: right"><?= $currency->symbol . ' ' . number_format($amount, 2) ?></td>
                                             </tr>
                                     <?php
                                         }
                                     }

                                     $retired_service_items = $retirement->retired_services();
                                     if(!empty($retired_service_items)){
                                         foreach ($retired_service_items as $service_item){
                                             $sn++;
                                             $total_amount += $amount = $service_item->rate * $service_item->quantity;
                                            ?>
                                             <tr>
                                                 <td class="bordered"><?= $sn ?></td>
                                                 <td class="bordered"><?= $service_item->description ?></td>
                                                 <td class="bordered">No.</td>
                                                 <td class="bordered" style="text-align: right"><?= $service_item->quantity ?></td>
                                                 <td class="right_bordered" style="text-align: right"><?= number_format($service_item->rate, 2) ?></td>
                                                 <td class="right_bordered" style="text-align: right"><?= $currency->symbol . ' ' . number_format($amount, 2) ?></td>
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
                                        <th style="text-align: right" class="total_amount_display"><?= $currency->symbol.' '.number_format($total_amount,2) ?></th>
                                    </tr>
                                    <?php
                                    if(!is_null($retirement->vat_inclusive)){
                                        if($retirement->vat_inclusive == 'VAT PRICED'){
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
                                        </tr>

                                        <?php
                                        $grand_total = $total_amount + $vat_amount;
                                        ?>
                                        <tr  style="background-color: #dfdfdf">
                                            <td></td>
                                            <th style="text-align: right" colspan="4">GRAND TOTAL</th>
                                            <th style="text-align: right"><?= number_format($grand_total,2) ?></th>
                                        </tr>
                                    <?php } ?>
                                </tfoot>
                        </table>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group <?= $imprest_voucher->has_project_items() ? 'col-md-12' : 'col-md-9'?> ">
                            <label for="remarks" class="control-label">Remarks</label>
                            <textarea name="remarks" class="form-control"></textarea>
                        </div>
                        <div style="<?= $imprest_voucher->has_project_items() ? 'display: none' : '' ?>" class="form-group col-md-3">
                            <label for="retirement_to" class="control-label">Retirement To</label>
                            <?= form_dropdown('retirement_to',$expense_account_options,'','class="form-control searchable"') ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger disapprove_examination">Reject</button>
                <button type="button" class="btn btn-sm btn-default approve_examination">Accept</button>
            </div>
        </div>
    </form>
</div>