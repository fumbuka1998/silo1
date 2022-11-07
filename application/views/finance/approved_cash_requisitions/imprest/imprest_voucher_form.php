<?php
   
    $location_options = isset($location) ? [$location->{$location::DB_TABLE_PK} => $location->location_name] : locations_options();
?>
<div style="width: 90%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Imprest Voucher</h4>
        </div>
        <form>
        <div class="modal-body">


            <div class='row'>

                <div class="col-xs-12">

                    <div class="form-group col-md-2">
                        <label for="imprest_date" class="control-label">Imprest Date</label>
                        <input type="text" class="form-control datepicker" required name="imprest_date" value="<?= date('Y-m-d') ?>">
                        <input type="hidden" name="requisition_approval_id" value="<?= $requisition_approval->{$requisition_approval::DB_TABLE_PK} ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="location_id" class="control-label">Credit Account</label>
                        <?= form_dropdown('credit_account_id',$credit_account_options,'',' class="form-control location_selector searchable"') ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="location_id" class="control-label">Debit Account</label>
                        <?= form_dropdown('debit_account_id',$credit_account_options,'',' class="form-control location_selector searchable"') ?>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="reference" class="control-label">Currency</label>
                        <?= form_dropdown('currency_id', [$currency->{$currency::DB_TABLE_PK} => $currency->name_and_symbol()],$currency->{$currency::DB_TABLE_PK},' class="form-control" ') ?>
                    </div>

                    <?php if($currency->{$currency::DB_TABLE_PK} != 1){ ?>
                        <div class="form-group col-md-2">
                            <label for="exchange_rate" class="control-label">Exchange Rate</label>
                            <input type="text" class="form-control number_format" required name="exchange_rate" value="<?= currency_exchange_rate($currency->{$currency::DB_TABLE_PK}) ?>">
                        </div>
                    <?php } else {
                        ?>
                        <input type="hidden" name="exchange_rate" class="number_format" value="1">
                        <?php
                    }
                    ?>

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
            $total_amount += $amount = $item->approved_quantity*$item->approved_rate;
        ?>
            <tr>
                <td><?= $sn ?></td>
               
                <td><?= $material->item_name ?>
                     <input type="hidden" name="description" value="<?= $material->item_name ?>">
                     <input type="hidden" name="item_id" value="<?= $material->{$material::DB_TABLE_PK} ?>">
                     <input type="hidden" name="requisition_approval_material_item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                     <input type="hidden" name="item_type" value="material">
                </td>
                
                <td style="text-align: right;width: 10%">
                    <input type="text" name="quantity" class="form-control" readonly value="<?= $item->approved_quantity ?>">
                </td>
                <td><?= $material->unit()->symbol ?></td>
                
                <td nowrap="nowrap" style="text-align: right;width: 20%">
                    <div class="form-group col-xs-12">
                        <div class="input-group">
                            <input type="text" name="rate" class="form-control money" readonly value="<?=  number_format($item->approved_rate,2) ?>">
                            <span class="input-group-addon currency_display"> <?= $item->currency()->symbol ?></span>
                        </div>
                    </div>
                </td>

                <td style="text-align: right"><input name="amount" readonly class="form-control money" value="<?= number_format($amount,2) ?>"></td>
            
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

             <td><?= $asset_item->asset_name ?>
                 <input type="hidden" name="description" value="<?= $asset_item->asset_name  ?>">
                 <input type="hidden" name="asset_item_id" value="<?= $asset_item->{$asset_item::DB_TABLE_PK} ?>">
                 <input type="hidden" name="requisition_approval_asset_item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                 <input type="hidden" name="item_type" value="asset">
             </td>

             <td style="text-align: right;width: 10%">
                 <input type="text" name="quantity" class="form-control" readonly value="<?= $item->approved_quantity ?>">
             </td>
             <td></td>

             <td nowrap="nowrap" style="text-align: right;width: 20%">
                 <div class="form-group col-xs-12">
                     <div class="input-group">
                         <input type="text" name="rate" class="form-control money" readonly value="<?=  number_format($item->approved_rate,2) ?>">
                         <span class="input-group-addon currency_display"> <?= $item->currency()->symbol ?></span>
                     </div>
                 </div>
             </td>

             <td style="text-align: right"><input name="amount" readonly class="form-control money" value="<?= number_format($amount,2) ?>"></td>

         </tr>
     <?php
     }

     $cash_items = $requisition_approval->cash_items(null,$account_id);

        foreach ($cash_items as $item){
            $sn++;
            $total_amount += $amount = $item->approved_quantity*$item->approved_rate;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td>
                     <input type="text" name="description" readonly value="<?= $item->requisition_cash_item()->description ?>" class="form-control">
                     <input type="hidden" name="item_id" value="">
                     <input type="hidden" name="requisition_approval_cash_item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                     <input type="hidden" name="item_type" value="cash">
                </td>
               
                <td style="text-align: right;width: 10%">
                  <input type="text" name="quantity" readonly class="form-control" value="<?= $item->approved_quantity ?>">
                </td>

                <td><?= $item->requisition_cash_item()->measurement_unit()->symbol ?></td>
             
                <td nowrap="nowrap" style="text-align: right;width: 20%">

                <div class="form-group col-xs-12">
                    
                    <div class="input-group">
                        <input type="text" name="rate" class="form-control money" readonly value="<?=  number_format($item->approved_rate,2) ?>">
                        <span class="input-group-addon currency_display"> <?= $item->currency()->symbol ?></span>
                    </div>
                </div>
        
                </td>
               
               <td style="text-align: right"><input name="amount" readonly class="form-control money" value="<?= number_format($amount,2) ?>"></td>
              
            </tr>
    <?php
        }

     $service_items = $requisition_approval->service_items('cash');

        foreach($service_items as $item){
            $sn++;
            $total_amount += $amount = $item->approved_quantity*$item->approved_rate;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td>
                    <input type="text" name="description" readonly value="<?= $item->requisition_service_item()->description ?>" class="form-control">
                    <input type="hidden" name="item_id" value="">
                    <input type="hidden" name="requisition_approval_service_item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                    <input type="hidden" name="item_type" value="service">
                </td>

                <td style="text-align: right;width: 10%">
                    <input type="text" name="quantity" readonly class="form-control" value="<?= $item->approved_quantity ?>">
                </td>

                <td><?= $item->requisition_service_item()->measurement_unit()->symbol ?></td>

                <td nowrap="nowrap" style="text-align: right;width: 20%">

                    <div class="form-group col-xs-12">

                        <div class="input-group">
                            <input type="text" name="rate" class="form-control money" readonly value="<?=  number_format($item->approved_rate,2) ?>">
                            <span class="input-group-addon currency_display"> <?= $item->requisition_service_item()->currency_symbol() ?></span>
                        </div>
                    </div>

                </td>

                <td style="text-align: right"><input name="amount" readonly class="form-control money" value="<?= number_format($amount,2) ?>"></td>

            </tr>
            <?php
        }
    ?>
        </tbody>

        <tfoot>
                <tr>
                    <td></td>
                    <th  style="text-align: right" colspan="4">TOTAL</th>
                    <th style="text-align: right" class="total_amount_display"><?= number_format($total_amount,2) ?></th>
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
                        <td></td>
                        <th style="text-align: right" colspan="4">VAT </th>
                        <th style="text-align: right"><?= number_format($vat_amount,2) ?></th>
                    </tr>

                    <?php
                    $grand_total = $total_amount + $vat_amount;
                    ?>
                    <tr  style="background-color: #dfdfdf">
                        <td></td>
                        <th  style="text-align: right" colspan="4">GRAND TOTAL</th>
                        <th style="text-align: right"><?= number_format($grand_total,2) ?></th>
                    </tr>
                <?php } ?>
        </tfoot>

        </table>
    </div>

            <div class="form-group col-xs-12">
                <div class="form-group col-md-8">
                    <label for="remarks" class="control-label">Remarks</label>
                    <textarea name="remarks" class="form-control"></textarea>
                </div>
                <div class="form-group col-md-4">
                    <label for="location_id" class="control-label">Assign Handler</label>
                    <?= form_dropdown('handler_id',$employee_options,'',' class="form-control searchable"') ?>
                </div>
            </div>
        </div>

        </div>

         <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-danger cancel_approved_payment">Revoke</button>
            <button type="button" class="btn btn-sm btn-default save_imprest_voucher">Submit</button>
        </div>
        </form>
    </div>
</div>