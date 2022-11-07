
<?php
$edit = isset($order);
if($edit){
    $project_junction = $order->project_purchase_order();
}
$material_options = isset($material_options) ? $material_options : material_item_dropdown_options();
$vendors_options = isset($vendor) ? [$vendor->{$vendor::DB_TABLE_PK} => $vendor->vendor_name] : $vendors_options;
$location_options = isset($location) ? [$location->{$location::DB_TABLE_PK} => $location->location_name] : locations_options();
$measurement_unit_options = isset($measurement_unit_options) ? $measurement_unit_options : measurement_unit_dropdown_options();
$purchase_order_type_options = $edit ? ($project_junction ? ['project_purchase_order' => 'Project Purchase Order'] : ['cost_center_purchase_order' => 'Cost Center Purchase Order']) : [
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
                            <?= form_dropdown('vendor_id',$vendors_options,$edit ? $order->vendor_id : '',' class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="currency_id" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id',$currency_options,$edit ? $order->currency_id : '',' class="form-control" ') ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="location_id" class="control-label">Delivery Location</label>
                            <?= form_dropdown('location_id',$location_options,$edit ? $order->location_id : '',' class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="" class="control-label">Order Type</label>
                            <?= form_dropdown('order_type',$purchase_order_type_options,$edit && !$project_junction ? 'cost_center_purchase_order' : 'project_purchase_order',' class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-3 project_options_form_group">
                            <label for="" class="control-label">Project</label>
                            <?= form_dropdown('project_id',$projects_options,$edit && $project_junction ? $project_junction->project_id : '',' class="form-control searchable"') ?>
                        </div>
                        <div style="display: none" class="form-group col-md-3 cost_center_options_form_group">
                            <label for="" class="control-label">Cost Center</label>
                            <?= form_dropdown('cost_center_id',$cost_center_options,$edit && $project_junction ? $project_junction->project_id : '',' class="form-control searchable"') ?>
                        </div>
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
                                <th>Item</th><th>Unit</th><th>Quantity</th><th>Price</th><th>Amount</th><th>Remarks</th><th></th>
                            </tr>
                            <tr style="display: none" class="material_row_template">
                                <td style="width: 30%">
                                    <?= form_dropdown('material_id',$material_options,'',' class="form-control "') ?>
                                    <input type="hidden" name="item_type" value="material">
                                </td>
                                <td style="text-align: center" class="unit_display"></td>
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
                                <td style="width: 3%;"><?= form_dropdown('uom_id',$measurement_unit_options, '',' class="form-control vendor_id"') ?></td>
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
                            if(!$edit) {

                                    ?>
                                    <tr>
                                        <td style="width: 30%">
                                            <?= form_dropdown('material_id', $material_options, '', ' class="form-control"') ?>
                                            <input type="hidden" name="item_type" value="material">
                                        </td>
                                        <td style="text-align: center" class="unit_display"></td>
                                        <td><input name="quantity" class="form-control"></td>
                                        <td><input name="rate" class="form-control money"></td>
                                        <td><input name="amount" readonly class="form-control money"></td>
                                        <td><textarea name="remarks" rows="1" class="form-control"></textarea></td>
                                        <td>
                                            <button class="btn btn-danger btn-xs row_remover"><i
                                                        class="fa fa-close"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                            } else {
                                $total_amount = 0;
                                $material_items = $order->material_items();
                                foreach ($material_items as $item) {
                                    $unit = $item->material_item()->unit()->symbol;
                                    $total_amount += $amount = $item->quantity * $item->price;
                                    ?>
                                    <tr>
                                        <td style="width: 30%">
                                            <?= form_dropdown('material_id', $material_options, $item->material_item_id, ' class="form-control searchable"') ?>
                                            <input type="hidden" name="item_type" value="material">
                                        </td>
                                        <td style="text-align: center" class="unit_display"><?= $unit ?></td>
                                        <td><input name="quantity" class="form-control"
                                                   value="<?= $item->quantity ?>"></td>
                                        <td><input name="rate" class="form-control money"
                                                   value="<?= number_format($item->price,3) ?>"></td>
                                        <td><input name="amount" readonly class="form-control money"
                                                   value="<?= number_format($amount,3) ?>">
                                        </td>
                                        <td><textarea name="remarks" rows="1"
                                                      class="form-control"><?= $item->remarks ?></textarea></td>
                                        <td>
                                            <button class="btn btn-danger btn-xs row_remover"><i
                                                        class="fa fa-close"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                $service_items = $order->service_items();
                                foreach ($service_items as $item){
                                    $total_amount += $amount = $item->quantity * $item->price;
                                    ?>
                                    <tr>
                                        <td style="width: 30%">
                                            <input name="service_description" class="form-control" value="<?= $item->description ?>">
                                            <input type="hidden" name="item_type" value="service">
                                        </td>
                                        <td style="width: 3%;"><?= form_dropdown('uom_id',$measurement_unit_options, $item->measurement_unit_id,' class="form-control vendor_id"') ?></td>
                                        <td><input name="quantity" class="form-control"
                                                   value="<?= $item->quantity ?>"></td>
                                        <td><input name="rate" class="form-control money"
                                                   value="<?= number_format($item->price,3) ?>"></td>
                                        <td><input name="amount" readonly class="form-control money"
                                                   value="<?= number_format($amount,3) ?>">
                                        </td>
                                        <td><textarea name="remarks" rows="1"
                                                      class="form-control"><?= $item->remarks ?></textarea></td>
                                        <td>
                                        <td>
                                            <button class="btn btn-danger btn-xs row_remover">
                                                <i class="fa fa-close"></i>
                                            </button>
                                        </td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                            </tbody>
                                <tfoot>
                                <tr class="text_styles">
                                    <th colspan="4">TOTAL</th><th style="text-align: right" class="total_amount_display"><?= $edit ? number_format($total_amount,3) : 0 ?></th>
                                    <th nowrap style="text-align: right" colspan="2">
                                        <button type="button" class="btn btn-xs btn-default material_row_adder">
                                            <i class="fa fa-plus"></i> Material
                                        </button>
                                        <button type="button" class="btn btn-xs btn-default service_row_adder">
                                            <i class="fa fa-plus"></i> Service
                                        </button>
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="4"  style="text-align: right">FREIGHT CHARGES</td>
                                    <td><input name="freight" class="form-control money text-right" value="<?=$edit ? $order->freight :''?>"></td>
                                    <td colspan="2" rowspan="3">
                                        <div style="text-align: center" class="form-group">
                                            <input type="checkbox" name="vat_inclusive" <?= $edit && $order->vat_inclusive == 1 ? 'checked' : '' ?> >
                                            <input type="hidden" name="vat_percentage" class="form-control" value="<?= $edit ? $order->vat_percentage : 18 ?>">
                                            &nbsp;&nbsp;
                                            <label for="vat_inclusive" class="control-label text-center">VAT inclusive</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr >
                                    <td colspan="4"   style="text-align: right">INSPECTION AND OTHER CHARGES</td>
                                    <td><input name="inspection_and_other_charges" class="form-control money text-right" value="<?=$edit ? $order->inspection_and_other_charges:''?>"></td>

                                </tr>
                                <tr class="text_styles">
                                    <th colspan="4" style="text-align: right" >GRAND TOTAL</th><th class="grand_total_display" style="text-align: right"><?= $edit ? number_format(($total_amount + $order->inspection_and_other_charges + $order->freight),2) : 0 ?></th>
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
                        <?= form_dropdown('handler_id',$procurement_members_options,$edit ? $order->handler_id : '',' class="form-control searchable"') ?>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_purchase_order">Save Order</button>
            </div>
        </form>
    </div>
</div>