<?php
$edit = isset($maintenance_service);

?>
<div class="modal-dialog modal-lg" style="width: 80%">
    <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">New Service</h4>
            </div>
            <form>
                <div class="modal-body" style="overflow:auto;">
                    <div class="form-group col-md-3">
                        <label for="service_date" class="control-label">Service Date</label>
                        <input type="text" class="form-control datepicker" name="service_date" value="<?= $edit ? $maintenance_service->service_date : date('Y-m-d') ?>">
                        <input type="hidden" class="form-control datepicker" name="service_id" value="<?= $edit ? $maintenance_service->{$maintenance_service::DB_TABLE_PK} : '' ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="client_id" class="control-label">Client</label>
                        <?= form_dropdown('client_id', $stakeholder_options, $edit ? $maintenance_service->client_id : '', " class = ' searchable form-control' "); ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="currency_id" class="control-label">Currency</label>
                        <?= form_dropdown('currency_id', $currency_options, $edit ? $maintenance_service->currency_id : '', " class = ' searchable form-control' "); ?>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="location" class="control-label">Location</label>
                        <input type="text" class="form-control" name="location" value="<?= $edit ? $maintenance_service->location : '' ?>">
                    </div>


                    <div class="col-xs-12 table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <td><strong>Description</strong></td><td><strong>Quantity</strong></td><td><strong>UOM</strong></td><td><strong>Rate</strong></td><td><strong>Amount</strong></td><td></td>
                            </tr>
                            <tr class="row_template" style="display: none">
                                <td>
                                    <textarea style="width: 340px;" class="form-control" name="description"></textarea>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="quantity" value=" ">
                                </td>
                                <td>
                                    <?= form_dropdown('unit_id',  $measurement_unit_options, '', " class = ' form-control' "); ?>
                                </td>
                                <td>
                                    <input style="width: 150px; text-align: right;" type="text" class="form-control number_format" name="rate" value=" ">
                                </td>
                                <td>
                                    <input style="width: 150px; text-align: right;" type="text" class="form-control number_format" name="amount" value=" " readonly>
                                </td>
                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            </thead>
                            <tbody>

                            <?php
                               if(!$edit){
                                   ?>
                                   <tr>
                                       <td>
                                           <textarea style="width: 340px;" class="form-control" name="description"></textarea>
                                       </td>
                                       <td>
                                           <input type="text" class="form-control" name="quantity" value=" ">
                                       </td>
                                       <td>
                                           <?= form_dropdown('unit_id', $measurement_unit_options, '', "  class = ' form-control' "); ?>
                                       </td>
                                       <td>
                                           <input style="width: 150px; text-align: right;" type="text" class="form-control number_format" name="rate" value=" ">
                                       </td>
                                       <td>
                                           <input style="width: 150px; text-align: right;" type="text" class="form-control number_format" name="amount" value=" " readonly>
                                       </td>
                                       <td>
                                           <button title="Remove Row" type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-close"></i></button>
                                       </td>
                                   </tr>
                                   <?php
                               } else {
                                   $returned_service_items =  $maintenance_service->maintenance_service_items();
                                   foreach ($returned_service_items as $service_item) {
                                       ?>
                                       <tr>
                                           <td>
                                               <textarea style="width: 340px;" class="form-control" name="description"><?= $service_item->description ?></textarea>
                                           </td>
                                           <td>
                                               <input type="text" class="form-control" name="quantity" value="<?= $service_item->quantity ?>">
                                           </td>
                                           <td>
                                               <?= form_dropdown('unit_id', $measurement_unit_options, $service_item->measurement_unit_id, "  class = ' form-control' "); ?>
                                           </td>
                                           <td>
                                               <input style="width: 150px; text-align: right;" type="text" class="form-control number_format" name="rate" value="<?= $service_item->rate ?>">
                                           </td>
                                           <td>
                                               <input style="width: 150px; text-align: right;" type="text" class="form-control number_format" name="amount" value="<?= $service_item->amount() ?>" readonly>
                                           </td>
                                           <td>
                                               <button title="Remove Row" type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-close"></i></button>
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
                                    <?= '' ?></th>
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan="6">
                                    <button type="button" class="btn btn-xs btn-default row_adder pull-right">Add Service</button>
                                </th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="form-group col-xs-12">
                        <label for="remarks" class="control-label">Remarks</label>
                        <textarea class="form-control" name="remarks"><?= $edit ? $maintenance_service->remarks : '' ?></textarea>
                    </div>
                </div>
            </form>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_service_items">
                    Save
                </button>
            </div>
    </div>
</div>
