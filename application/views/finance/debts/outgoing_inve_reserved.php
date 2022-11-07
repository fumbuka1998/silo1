<?php
/**
 * Created by PhpStorm.
 * User: kihunakasobo
 * Date: 2019-07-23
 * Time: 16:28
 */

$edit = $outgoing_invoice;
if($edit){
    $grand_total = 0;
}
?>
<div class="modal-dialog modal-lg" style="width: 80%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= 'Tax Invoice' ?></h4>
        </div>
        <form>
            <div class="modal-body" style="overflow:auto;">
            <div class="form-group col-md-4">
                <label for="invoice_date" class="control-label">Invoice Date</label>
                <input type="text" class="form-control datepicker" name="invoice_date" value="<?= $edit ? $outgoing_invoice->invoice_date : '' ?>">
                <input type="hidden" name="outgoing_invoice_id" value="<?= $edit ? $outgoing_invoice->{$outgoing_invoice::DB_TABLE_PK} : '' ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="client_id" class="control-label">Client</label>
                <?php if ($debt_nature == "stock_sale") {
                    $client = $sale->client();
                    ?>
                    <?= form_dropdown('client_id', [$client->{$client::DB_TABLE_PK}=>$client->client_name],$sale->client_id, ' class="form-control searchable"'); ?>

                <?php } else if ($debt_nature == "maintenance_service") {
                    $client = $maintenance_service->client();
                    ?>
                    <?= form_dropdown('client_id', [$client->{$client::DB_TABLE_PK}=>$client->client_name],$maintenance_service->client_id, ' class="form-control searchable"'); ?>
                <?php } else if($debt_nature == "certificate") {
                    $client = $project_certificate->project()->client();
                    if ($client) {
                        ?>
                        <?= form_dropdown('client_id', [$client->{$client::DB_TABLE_PK} => $client->client_name], $client->{$client::DB_TABLE_PK}, ' class="form-control searchable"'); ?>
                    <?php } else { ?>
                        <?= form_dropdown('client_id', [], '', ' class="form-control searchable"'); ?>
                    <?php }
                    }
                ?>
            </div>
            <div class="form-group col-md-4">
                <label for="currency_id" class="control-label">Currency</label>
                <?= form_dropdown('currency_id', $currency_options, $edit ? $outgoing_invoice->currency_id : '', ' class="form-control searchable"'); ?>
            </div>
            <div class="form-group col-md-4">
                <label for="reference" class="control-label">Reference</label>
                <input type="text" class="form-control" name="reference" value="<?= $edit ? $outgoing_invoice->reference : '' ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="invoice_no" class="control-label">Invoice No</label>
                <input type="text" class="form-control" name="invoice_no" value="<?= $edit ? $outgoing_invoice->invoice_no : '' ?>">
            </div>
            <div class="col-xs-12 table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <td style="width: 350px;"><strong>Description</strong></td><td><strong>Quantity</strong></td><td><strong>UOM</strong></td><td><strong>Rate</strong></td><td><strong>Amount</strong></td>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                       if(!$edit){
                           if ($debt_nature == "stock_sale") {
                               $stock_sale_material_items = $sale->material_items();
                               foreach($stock_sale_material_items as $sale_item) {
                                   $unit = $sale_item->material_item()->unit();
                                   $item_balance = $sale_item->amount() - $sale_item->invoiced_amount();
                                   if($item_balance > 0) {
                                       ?>
                                       <tr>
                                           <td>
                                               <?= form_dropdown('debted_item_id', [$sale_item->{$sale_item::DB_TABLE_PK} => $sale_item->material_item()->item_name], $sale_item->{$sale_item::DB_TABLE_PK}, " class = ' form-control' "); ?>
                                               <input type="hidden" name="item_type" value="material">
                                               <input type="hidden" name="debt_nature_id"
                                                      value="<?= $sale->{$sale::DB_TABLE_PK} ?>">
                                               <input type="hidden" name="debt_nature" value="<?= $debt_nature ?>">
                                           </td>
                                           <td>
                                               <input style="text-align: right" type="text" class="form-control"
                                                      name="quantity" value="<?= $sale_item->quantity ?>">
                                           </td>
                                           <td>
                                               <span class="unit_display"><?= $unit->symbol ?></span>
                                               <input type="hidden" name="unit_id"
                                                      value="<?= $unit->{$unit::DB_TABLE_PK} ?>">
                                           </td>
                                           <td>
                                               <input style="text-align: right; width: 150px;" type="text"
                                                      class="form-control number_format" name="rate"
                                                      value="<?= $sale_item->price ?>">
                                           </td>
                                           <td>
                                               <input style="text-align: right; width: 150px;" type="text"
                                                      class="form-control number_format" name="amount"
                                                      value="<?= $sale_item->amount() ?>" readonly>
                                           </td>
                                       </tr>
                                       <?php
                                   }
                               }

                               $stock_sale_asset_items = $sale->asset_items();
                               foreach($stock_sale_asset_items as $sale_item) {
                                   $item_balance = $sale_item->price - $sale_item->invoiced_amount();
                                   if($item_balance > 0) {
                                       ?>
                                       <tr>
                                           <td>
                                               <?= form_dropdown('debted_item_id', [$sale_item->{$sale_item::DB_TABLE_PK} => $sale_item->asset_item()->asset_code()], $sale_item->{$sale_item::DB_TABLE_PK}, " class = ' form-control' "); ?>
                                               <input type="hidden" name="item_type" value="asset">
                                               <input type="hidden" name="debt_nature_id"
                                                      value="<?= $sale->{$sale::DB_TABLE_PK} ?>">
                                               <input type="hidden" name="debt_nature" value="<?= $debt_nature ?>">
                                           </td>
                                           <td>
                                               <span>1</span>
                                               <input type="hidden" name="quantity" value="1">
                                           </td>
                                           <td>
                                               <span>Item</span>
                                               <input type="hidden" name="unit_id" value="">
                                           </td>
                                           <td>
                                               <input style="text-align: right; width: 150px;" type="text"
                                                      class="form-control number_format" name="rate"
                                                      value="<?= $sale_item->price ?>">
                                           </td>
                                           <td>
                                               <input style="text-align: right; width: 150px;" type="text"
                                                      class="form-control number_format" name="amount"
                                                      value="<?= $sale_item->price ?>" readonly>
                                           </td>
                                       </tr>
                                       <?php
                                   }
                               }

                           } else if ($debt_nature == "maintenance_service") {
                               $maintenance_services_items = $maintenance_service->maintenance_service_items();
                               foreach($maintenance_services_items as $maintenance_services_item) {
                                   $item_balance = $maintenance_services_item->amount() - $maintenance_services_item->invoiced_amount();
                                   if($item_balance > 0){
                                       ?>
                                        <tr>
                                           <td>
                                               <?= form_dropdown('debted_item_id', [$maintenance_services_item->{$maintenance_services_item::DB_TABLE_PK} => $maintenance_services_item->description], $maintenance_services_item->{$maintenance_services_item::DB_TABLE_PK}, " class = ' form-control' "); ?>
                                               <input type="hidden" name="item_type" value="">
                                               <input type="hidden" name="debt_nature_id" value="<?= $maintenance_service->{$maintenance_service::DB_TABLE_PK} ?>">
                                               <input type="hidden" name="debt_nature" value="<?= $debt_nature ?>">
                                           </td>
                                           <td>
                                               <input style="text-align: right" type="text" class="form-control" name="quantity" value="<?= $maintenance_services_item->quantity ?>">
                                           </td>
                                           <td>
                                               <span class="unit_display"><?= $maintenance_services_item->measurement_unit()->symbol ?></span>
                                               <input type="hidden" name="unit_id" value="<?= $maintenance_services_item->measurement_unit_id ?>">
                                           </td>
                                           <td>
                                               <input style="text-align: right; width: 150px;" type="text" class="form-control number_format" name="rate" value="<?= $maintenance_services_item->rate ?>">
                                           </td>
                                           <td>
                                               <input style="text-align: right; width: 150px;" type="text" class="form-control number_format" name="amount" value="<?= $maintenance_services_item->amount() ?>" readonly>
                                           </td>
                                       </tr>
                                <?php
                                   }
                               }
                           } else if($debt_nature == "certificate") {
                               $item_balance = $project_certificate->certified_amount - $project_certificate->invoiced_amount();
                               if($item_balance > 0) {
                                   ?>
                                   <tr>
                                       <td>
                                           <span style="width: 340px"><?= wordwrap($project_certificate->certificate_number . ' - ' . $project_certificate->project()->project_name, 60, '<br/>') ?></span>
                                           <input type="hidden" name="debted_item_id"
                                                  value="<?= $project_certificate->{$project_certificate::DB_TABLE_PK} ?>">
                                           <input type="hidden" name="item_type" value="">
                                           <input type="hidden" name="debt_nature_id"
                                                  value="<?= $project_certificate->{$project_certificate::DB_TABLE_PK} ?>">
                                           <input type="hidden" name="debt_nature" value="<?= $debt_nature ?>">
                                       </td>
                                       <td>
                                           <span>1</span>
                                           <input type="hidden" name="quantity" value="1">
                                       </td>
                                       <td>
                                           <span>Item</span>
                                           <input type="hidden" name="unit_id" value="">
                                       </td>
                                       <td>
                                           <input style="text-align: right; width: 150px;" type="text"
                                                  class="form-control number_format" name="rate"
                                                  value="<?= $project_certificate->certified_amount; ?>">
                                       </td>
                                       <td>
                                           <input style="text-align: right; width: 150px;" type="text"
                                                  class="form-control number_format" name="amount"
                                                  value="<?= $project_certificate->certified_amount; ?>" readonly>
                                       </td>
                                   </tr>
                                   <?php
                               }
                           }

                       } else {

                           $outgoing_invoice_items = $outgoing_invoice->outgoing_invoice_items();
                           foreach ($outgoing_invoice_items as $item) {
                               if ($debt_nature == "stock_sale") {
                                   $item_nature = $item->stock_sale_item_nature();
                                   if($item_nature == "asset"){
                                       $stock_sale_asset_item = $item->stock_sale_asset_item();
                                       $item_balance = $stock_sale_asset_item->price - $stock_sale_asset_item->invoiced_amount();
                                   } else {
                                       $stock_sale_material_item = $item->stock_sale_material_item();
                                       $item_balance = $stock_sale_material_item->amount() - $stock_sale_material_item->invoiced_amount();
                                   }
                               } else if ($debt_nature == "maintenance_service") {
                                    $service_item = $item->maintenance_service_item();
                                    $item_balance = $service_item->amount() - $service_item->invoiced_amount();
                               } else {
                                    $certificate = $item->project_certificate();
                                    $item_balance = $certificate->certified_amount - $certificate->invoiced_amount();
                               }

                               if($item_balance > 0) {
                                   ?>
                                   <tr>
                                       <td>
                                           <?php if ($debt_nature == "stock_sale") {
                                               if ($item_nature == "asset") { ?>
                                                   <?= form_dropdown('debted_item_id', [$stock_sale_asset_item->{$stock_sale_asset_item::DB_TABLE_PK} => $item->stock_sale_asset_item()->asset_sub_location_history()->asset()->asset_code()], '', " class = ' form-control' "); ?>
                                                   <input type="hidden" name="item_type" value="asset">
                                               <?php } else {
                                                   $stock_sale_material_item = $item->stock_sale_material_item();
                                                   ?>
                                                   <?= form_dropdown('debted_item_id', [$stock_sale_material_item->{$stock_sale_material_item::DB_TABLE_PK} => $item->stock_sale_material_item()->material_item()->item_name], '', " class = ' form-control' "); ?>
                                                   <input type="hidden" name="item_type" value="material">
                                               <?php } ?>
                                               <input type="hidden" name="debt_nature_id"
                                                      value="<?= $sale->{$sale::DB_TABLE_PK} ?>">
                                               <input type="hidden" name="debt_nature" value="<?= $debt_nature ?>">
                                               <?php
                                           }

                                           if ($debt_nature == "maintenance_service") { ?>
                                               <?= form_dropdown('debted_item_id', [$service_item->{$service_item::DB_TABLE_PK} => $service_item->description], $service_item->{$service_item::DB_TABLE_PK}, " class = ' form-control' "); ?>
                                               <input type="hidden" name="item_type" value="">
                                               <input type="hidden" name="debt_nature_id"
                                                      value="<?= $maintenance_service->{$maintenance_service::DB_TABLE_PK} ?>">
                                               <input type="hidden" name="debt_nature" value="<?= $debt_nature ?>">
                                           <?php }

                                           if ($debt_nature == "certificate") {
                                               ?>
                                               <span style="width: 340px"><?= wordwrap($certificate->certificate_number . ' - ' . $certificate->project()->project_name, 60, '<br/>') ?></span>
                                               <input type="hidden" name="debted_item_id"
                                                      value="<?= $certificate->{$certificate::DB_TABLE_PK} ?>">
                                               <input type="hidden" name="item_type" value="">
                                               <input type="hidden" name="debt_nature_id"
                                                      value="<?= $project_certificate->{$project_certificate::DB_TABLE_PK} ?>">
                                               <input type="hidden" name="debt_nature" value="<?= $debt_nature ?>">
                                           <?php } ?></td>
                                       <td>
                                           <?php if ($debt_nature != "certificate") { ?>
                                               <input style="text-align: right" type="text" class="form-control"
                                                      name="quantity" value="<?= $item->quantity ?>">
                                           <?php } else { ?>
                                               <span>1</span>
                                               <input type="hidden" name="quantity" value="1">
                                           <?php } ?>
                                       </td>
                                       <td>
                                           <?php if ($debt_nature != "certificate") { ?>
                                               <span class="unit_display"><?= $item->measurement_unit()->symbol ?></span>
                                               <input type="hidden" name="unit_id"
                                                      value="<?= $item->measurement_unit_id ?>">
                                           <?php } else { ?>
                                               <span>Item</span>
                                               <input type="hidden" name="unit_id" value="">
                                           <?php } ?>
                                       </td>
                                       <td>
                                           <input style="text-align: right; width: 150px;" type="text"
                                                  class="form-control number_format" name="rate"
                                                  value="<?= number_format($item->rate, 2) ?>">
                                       </td>
                                       <td>
                                           <input style="text-align: right; width: 150px;" type="text"
                                                  class="form-control number_format" name="amount"
                                                  value="<?= $item->amount() ?>" readonly>
                                       </td>
                                   </tr>
                                   <?php
                               }
                           }
                       }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr class="text_styles">
                            <th>TOTAL</th>
                            <th colspan="4" class="number_format total_amount_display" style="text-align: right; width: 150px;"><?= '' ?></th>
                        </tr>

                        <tr>
                            <td colspan="4"  style="text-align: right; font-weight: bold">
                                <div class="col-xs-12">
                                    <div style="text-align: left" class="form-group col-xs-6">
                                        <input type="checkbox" name="vat_inclusive" <?= $edit && $outgoing_invoice->vat_inclusive == 1 ? 'checked' : '' ?> >
                                        <input type="hidden" name="vat_percentage" class="form-control" value="<?= $edit ? $outgoing_invoice->vat_percentage : 18 ?>">
                                        &nbsp;&nbsp;
                                        <label for="vat_inclusive" class="control-label text-center">VAT inclusive</label>
                                    </div>
                                    <div style="text-align: right" class="form-group col-xs-6">
                                        VAT(18%)
                                    </div>
                                </div>
                            </td>
                            <td><input style="text-align: right; width: 150px;" name="vat" class="form-control money text-right" value="" readonly></td>
                        </tr>
                        <tr class="text_styles">
                            <th colspan="4" style="text-align: right" >GRAND TOTAL</th>
                            <th class="grand_total_display"  style="text-align: right; width: 150px;"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class=" form-group col-xs-12">
                <label for="account_id" class="control-label">Accounts</label>
                <?= form_dropdown('account_id', $accounts, '', " class='searchable' ") ?>
            </div>
            <div class="form-group col-xs-12">
                <label for="bank_details" class="control-label">Bank Details</label>
                <textarea id="bank_details_text_area" readonly class="form-control" name="bank_details" rows="7"><?= $edit ? $outgoing_invoice->bank_details : ''  ?></textarea>
            </div>
            <div class="form-group col-xs-12">
                <label for="remarks" class="control-label">Payment Terms</label>
                <textarea class="form-control" name="payment_terms" rows="5"><?= $edit ? $outgoing_invoice->payment_terms : ''  ?></textarea>
            </div>
        </div>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm submit_outgoing_invoice">
                Submit
            </button>
        </div>
    </div>
</div>
