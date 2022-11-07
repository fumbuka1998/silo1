<?php
    $credit_account_options = isset($account) ? [$account->{$account::DB_TABLE_PK} => $account->account_name] : $credit_account_options;
    $requisition = $requisition_approval->requisition();
    $currency = $requisition->currency();
    $requested_for = $requisition->requested_for();
    if($requested_for == 'project'){
        $junction_id = $requisition->project_requisition()->project_id;
    } else {
        $junction_id = $requisition->cost_center_requisition()->cost_center_id;
    }
?>
<div style="width: 90%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Payment Voucher</h4>
        </div>
        <form>
        <div class="modal-body">

            <div class='row'>

                <div class="col-xs-12">

                    <div class="form-group col-md-4">
                        <label for="payment_date" class="control-label">Payment Date</label>
                        <input type="text" class="form-control datepicker" required name="payment_date" value="<?= date('Y-m-d') ?>">
                        <input type="hidden" name="requisition_approval_id" value="<?= $requisition_approval_id ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="reference" class="control-label">Reference</label>
                        <input type="text" class="form-control" name="reference" value="">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="reference" class="control-label">Currency</label>
                        <?= form_dropdown('currency_id', [$currency->{$currency::DB_TABLE_PK} => $currency->name_and_symbol()],$requisition->currency_id,' class="form-control" ') ?>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="credit_account_id" class="control-label">Credit Account</label>
                        <?= form_dropdown('credit_account_id',$credit_account_options,'',' class="form-control searchable" ') ?>
                        <input type="hidden" name="junction_type" value="<?= $requested_for ?>">
                        <input type="hidden" name="junction_id" value="<?= $junction_id ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="cheque_number" class="control-label">Cheque Number</label>
                        <input type="text" class="form-control" placeholder="Optional" name="cheque_number" value="">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="payee" class="control-label">Payee</label>
                        <input type="text" class="form-control" required name="payee" value="">
                    </div>

                    <?php if($requisition->currency_id != 1){ ?>
                        <div class="form-group col-md-4">
                            <label for="exchange_rate" class="control-label">Exchange Rate</label>
                            <input type="text" class="form-control number_format" required name="exchange_rate" value="<?= currency_exchange_rate($requisition_approval->requisition()->currency_id) ?>">
                        </div>
                        <?php } else {
                            ?>
                            <input type="hidden" name="exchange_rate" class="number_format" value="1.00">
                        <?php
                        }
                    ?>

                </div>

         <div class="col-xs-12">

            <table  width="100%" class="table table-bordered">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Debit Account</th>
                        <th>Description</th>
                        <th>Quantity</th>
                         <th>Unit</th>
                        <th nowrap="true">Rate</th>
                        <th nowrap="true">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    
                 <?php


        $sn = 0;
        $total_amount=0;
        $material_items = $requisition_approval->material_items('cash');

       foreach($material_items as $item){
            $sn++;
            $material = $item->requisition_material_item()->material_item();
            $unit_symbol = $material->unit()->symbol;
            $total_amount += $amount = $item->approved_quantity*$item->approved_rate;
            $material_name = htmlentities($material->item_name);
        ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= form_dropdown('debit_account_id',$expense_pv_debit_account_options,'',' class="form-control "') ?></td>
                <td><?= $material_name ?>
                     <input type="hidden" name="description" value="<?= $material_name.' ('.$item->approved_quantity.' '.$unit_symbol.')'?>">
                     <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                     <input type="hidden" name="item_type" value="material">
                </td>
                <td style="text-align: right;width: 10%">
                     <input type="text" name="quantity" class="form-control" value="<?= $item->approved_quantity ?>">
                </td>
                <td><?= $unit_symbol ?></td>
                <td nowrap="nowrap" style="width: 13%">
                    <div class="input-group">
                        <span class="input-group-addon currency_display"> <?= $item->currency()->symbol ?></span>
                        <input style="text-align: right" type="text" name="rate" class="form-control money" value="<?=  $item->approved_rate ?>">
                    </div>
                </td>
                <td>
                    <input style="text-align: right" name="amount" readonly class="form-control money" value="<?= $amount ?>">
                </td>
            </tr>
    <?php
        }

        $asset_items = $requisition_approval->asset_items('cash');

       foreach($asset_items as $item){
            $sn++;
             $asset_item = $item->requisition_asset_item()->asset_item();
            $total_amount += $amount = $item->approved_quantity*$item->approved_rate;
        ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= form_dropdown('debit_account_id',$expense_pv_debit_account_options,'',' class="form-control "') ?></td>
                <td><?= $asset_item->asset_name ?>
                     <input type="hidden" name="description" value="<?= $asset_item->asset_name.' ('.$item->approved_quantity.' Nos)'?>">
                     <input type="hidden" name="item_id" value="<?= $asset_item->{$asset_item::DB_TABLE_PK} ?>">
                     <input type="hidden" name="item_type" value="asset">
                </td>

                <td style="text-align: right;width: 10%">
                        <input type="text" name="quantity" class="form-control" value="<?= $item->approved_quantity ?>">
                </td>
                <td></td>
                <td nowrap="nowrap" style="width: 13%">
                    <div class="input-group">
                        <span class="input-group-addon currency_display"> <?= $currency->symbol ?></span>
                        <input style="text-align: right" type="text" name="rate" class="form-control money" value="<?=  $item->approved_rate ?>">
                    </div>
                </td>

                <td>
                    <input style="text-align: right" name="amount" readonly class="form-control money" value="<?= $amount ?>">
                </td>
            </tr>
    <?php
        }

        $service_items = $requisition_approval->service_items('cash');

        foreach ($service_items as $item){
            $sn++;
            $total_amount += $amount = $item->approved_quantity*$item->approved_rate;
            $requisition_item = $item->requisition_service_item();
            $description = htmlentities($requisition_item->description);
            $unit_symbol = $requisition_item->measurement_unit()->symbol;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= form_dropdown('debit_account_id',$expense_pv_debit_account_options,'',' class="form-control "') ?></td>
                <td><?= $description ?>
                     <input type="hidden" name="description" value="<?= $description.' ('.$item->approved_quantity.' '.$unit_symbol.')' ?>">
                     <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                     <input type="hidden" name="item_type" value="service">
                </td>
                <td style="text-align: right;width: 10%">
                  <input type="text" name="quantity" class="form-control" value="<?= $item->approved_quantity ?>">
                </td>

                <td>
                    <?= $unit_symbol ?>
                </td>
                <td nowrap="nowrap">
                    <div class="input-group">
                        <span class="input-group-addon currency_display"> <?= $currency->symbol ?></span>
                        <input style="text-align: right" type="text" name="rate" class="form-control money" value="<?=  $item->approved_rate ?>">
                    </div>
                </td>
               <td>
                   <input style="text-align: right" name="amount" readonly class="form-control money" value="<?= $amount ?>">
               </td>
            </tr>
    <?php
        }

        $cash_items = $requisition_approval->cash_items();

        foreach ($cash_items as $item){
            $sn++;
            $total_amount += $amount = $item->approved_quantity*$item->approved_rate;
            $requisition_item = $item->requisition_cash_item();
            $description = htmlentities($requisition_item->description);
            $unit_symbol = $requisition_item->measurement_unit()->symbol;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= form_dropdown('debit_account_id',$expense_pv_debit_account_options,'',' class="form-control "') ?></td>
                <td><?= $description ?>
                     <input type="hidden" name="description" value="<?= $description.' ('.$item->approved_quantity.' '.$unit_symbol.')' ?>">
                     <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                     <input type="hidden" name="item_type" value="cash">
                </td>
                <td style="text-align: right;width: 10%">
                  <input type="text" name="quantity" class="form-control" value="<?= $item->approved_quantity ?>">
                </td>

                <td>
                    <?= $unit_symbol ?>
                </td>
                <td nowrap="nowrap">
                    <div class="input-group">
                        <span class ="input-group-addon currency_display"> <?= $currency->symbol ?></span>
                        <input style ="text-align: right" type="text" name="rate" class="form-control money" value="<?=  $item->approved_rate ?>">
                    </div>
                </td>
               <td>
                   <input style="text-align: right" name="amount" readonly class="form-control money" value="<?= $amount ?>">
               </td>
            </tr>
    <?php
        }
        
    ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" style="text-align: right">TOTAL</th>
                <th style="text-align: right" class="total_amount_display"><?= number_format($total_amount) ?></th>
            </tr>
            <?php
            if(!is_null($requisition_approval->vat_inclusive)){
                if($requisition_approval->vat_inclusive == 'VAT PRICED'){
                    $total_amount_vat_exclusive = $total_amount/1.18;
                    $vat_amount = $total_amount - $total_amount_vat_exclusive;
                } else {
                    $vat_amount = $total_amount*0.18;
                }
                ?>
                <tr>
                    <th colspan="6" style="text-align: right">VAT</th>
                    <th style="text-align: right" class="vat_amount"><?= number_format($vat_amount) ?></th>
                    <input type="hidden" name="vat_percentage" value="<?= $requisition_approval->vat_percentage ?>">
                </tr>

                <?php
                $grand_total = $total_amount + $vat_amount;
                ?>
                <tr  style="background-color: #dfdfdf">
                    <th colspan="6" style="text-align: right">GRAND TOTAL</th>
                    <th style="text-align: right" class="grand_total"><?= number_format($grand_total) ?></th>
                </tr>
            <?php } ?>
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
            <button type="button" class="btn btn-sm btn-danger cancel_approved_payment">Revoke</button>
            <button type="button" class="btn btn-sm btn-default save_approved_cash_payment_voucher">Submit</button>
        </div>
        </form>
    </div>
</div>