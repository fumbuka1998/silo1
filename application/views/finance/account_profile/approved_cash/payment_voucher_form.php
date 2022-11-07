<?php
   
    $location_options = isset($location) ? [$location->{$location::DB_TABLE_PK} => $location->location_name] : locations_options();
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

                    <div class="form-group col-md-3">
                        <label for="payment_date" class="control-label">Payment Date</label>
                        <input type="text" class="form-control datepicker" required name="payment_date" value="">
                        <input type="hidden" name="credit_account_id" value="<?= $account_id ?>">
                        <input type="hidden" name="requisition_approval_id" value="<?= $requisition_approval_id ?>">
                        <input type="hidden" name="payment_voucher_id" value="">
                 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="payee" class="control-label">Payee</label>
                        <input type="text" class="form-control" required name="payee" value="">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="reference" class="control-label">Reference</label>
                        <input type="text" class="form-control" name="reference" value="">
                    </div>

                    <?php if($requisition_approval->requisition()->currency_id != 1){ ?>
                        <div class="form-group col-md-3">
                            <label for="exchange_rate" class="control-label">Exchange Rate</label>
                            <input type="text" class="form-control number_format" required name="exchange_rate" value="<?= currency_exchange_rate($requisition_approval->requisition()->currency_id) ?>">
                        </div>
                        <?php } else {
                            ?>
                            <input type="hidden" name="exchange_rate" class="number_format" value="1">
                        <?php
                        } ?>

                    <div class="form-group col-md-3">
                        <label for="location_id" class="control-label">Location</label>
                        <?= form_dropdown('location_id',$location_options,'',' class="form-control location_selector searchable"') ?>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="sub_location_id" class="control-label">Offloading Sub-location</label>
                        <?= form_dropdown('sub_location_id',[],'',' class="form-control sub_location_id searchable"') ?>
                    </div>

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
        $material_items = $requisition_approval->material_items(null,$account_id);

       foreach($material_items as $item){
            $sn++;
             $material = $item->requisition_material_item()->material_item();
            $total_amount += $amount = $item->approved_quantity*$item->approved_rate;
        ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= form_dropdown('debit_account_id',$expense_pv_debit_account_options,$item->requisition_material_item()->expense_account_junction($requisition_approval->{$requisition_approval::DB_TABLE_PK})->expense_account_id,' class="form-control searchable"') ?></td>
                <td><?= $material->item_name ?>
                     <input type="hidden" name="description" value="<?= $material->item_name ?>">
                     <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                     <input type="hidden" name="item_type" value="material">
                </td>
                
                <td style="text-align: right;width: 10%">
 
                        <input type="text" name="quantity" class="form-control" value="<?= $item->approved_quantity ?>">
                    

                </td>
                <td><?= $material->unit()->symbol ?></td>
                
                <td nowrap="nowrap" style="text-align: right;width: 20%">

                <div class="form-group col-xs-12">
                    
                    <div class="input-group">
                        <input type="text" name="rate" class="form-control money" value="<?=  number_format($item->approved_rate,2) ?>">
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
                <td><?= form_dropdown('debit_account_id',$expense_pv_debit_account_options,$item->requisition_cash_item()->expense_account_junction($requisition_approval->{$requisition_approval::DB_TABLE_PK})->expense_account_id,' class="form-control searchable"') ?></td>
                <td><?= $item->requisition_cash_item()->description ?>
                     <input type="hidden" name="description" value="<?= $item->requisition_cash_item()->description ?>">
                     <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                     <input type="hidden" name="item_type" value="cash">
                </td>
               
                <td style="text-align: right;width: 10%">

                  <input type="text" name="quantity" class="form-control" value="<?= $item->approved_quantity ?>">
                   
                </td>

                <td><?= $item->requisition_cash_item()->measurement_unit()->symbol ?></td>
             
                <td nowrap="nowrap" style="text-align: right;width: 20%">

                <div class="form-group col-xs-12">
                    
                    <div class="input-group">
                        <input type="text" name="rate" class="form-control money" value="<?=  number_format($item->approved_rate,2) ?>">
                        <span class="input-group-addon currency_display"> <?= $item->currency()->symbol ?></span>
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
                    <td></td>
                    <th colspan="4">TOTAL</th>
                    <th style="text-align: right" class="total_amount_display"><?= number_format($total_amount,2) ?></th>
                
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
            <button type="button" class="btn btn-sm btn-default save_payment_voucher">Submit</button>
        </div>
        </form>
    </div>
</div>