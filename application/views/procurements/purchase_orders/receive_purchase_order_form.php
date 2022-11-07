<?php
$edit = isset($grn);
$sub_location_options = isset($sub_location_options) ? $sub_location_options : $order->location()->sub_location_options();
$vat_factor = $order->vat_percentage /100;
?>
<div style="width: 80%" class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Receive Purchase Order</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-3">
                            <label for="receive_date" class="control-label">Receive Date</label>
                            <input type="hidden" name="order_id" value="<?= $order->{$order::DB_TABLE_PK} ?>">
                            <input type="hidden" name="grn_id" value="<?= $edit ? $grn->grn_id : '' ?>">
                            <input type="text" class="form-control datetime_picker" required name="receive_date" value="<?= $edit ? $grn->receive_date : date('Y-m-d') ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="offloading_sub_location_id" class="control-label">Offloading Sub-location</label>
                            <?= form_dropdown('receiving_sub_location_id', $sub_location_options, '', ' class="form-control searchable"') ?>
                        </div>
                        <?php if ($order->currency_id != 1) { ?>
                            <div class="form-group col-md-3">
                                <label for="exchange_rate" class="control-label">Exchange Rate</label>
                                <input type="text" class="form-control number_format" required name="exchange_rate" value="<?= $edit ? $grn->purchase_order_grn()->exchange_rate : currency_exchange_rate($order->currency_id) ?>">
                            </div>
                        <?php } else {
                            ?>
                            <input type="hidden" name="exchange_rate" class="number_format" value="1">
                            <?php
                        } ?>
                    </div>
                </div>
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a class="item_details" href="#items_details_<?= $order->{$order::DB_TABLE_PK} ?>" data-toggle="tab">Items Details</a></li>
                        <li><a class="import_duties" href="#import_duties_<?= $order->{$order::DB_TABLE_PK} ?>" data-toggle="tab">Duties</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="items_details_<?= $order->{$order::DB_TABLE_PK} ?>">
                            <div class='row'>
                                <div class="col-xs-12 table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>Item Description</th>
                                            <th>Received Quantity </th>
                                            <th> Rejected Quantity</th>
                                            <th>Unit</th>
                                            <th>Price</th>
                                            <th>Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if (!$edit) {
                                            $material_items = $order->material_items();
                                            foreach ($material_items as $item) {
                                                $material = $item->material_item();
                                                $quantity = $item->unreceived_quantity();
                                                $price = $order->vat_inclusive == 'VAT PRICED' ? $item->price/(1+$vat_factor) : $item->price;
                                                if ($quantity > 0) { ?>
                                                    <tr>
                                                        <td style="width: 35%">
                                                            <input type="hidden" name="item_type" value="material">

                                                            <?= wordwrap($material->item_name, 50, '<br/>') ?>
                                                            <input type="hidden" name="item_id" value="<?= $item->material_item_id ?>">
                                                            <input type="hidden" name="order_item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control" quantity_balance="<?= $quantity ?>" name="quantity" value="<?= $quantity; ?>"></td>
                                                        <td><input type="text" class="form-control" name="rejected_quantity" value=""></td>
                                                        <td><?= $material->unit()->symbol ?></td>
                                                        <td><input readonly type="text" name="rate" class="form-control money" value="<?= $price ?>"></td>
                                                        <td><input type="text" name="amount" readonly class="form-control money" value="<?= $item->price * $quantity ?>"></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }

                                            $asset_items = $order->asset_items();
                                            foreach ($asset_items as $item) {
                                                $asset_item = $item->asset_item();
                                                $quantity = $item->unreceived_quantity();
                                                $price = $order->vat_inclusive == 'VAT PRICED' ? $item->price/(1+$vat_factor) : $item->price;
                                                if ($quantity > 0) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?= $asset_item->asset_name ?>
                                                            <input type="hidden" name="item_id" value="<?= $item->asset_item_id ?>">
                                                            <input type="hidden" name="item_type" value="asset">
                                                            <input type="hidden" name="order_item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"  quantity_balance="<?= $quantity ?>" name="quantity" value="<?= $quantity; ?>"></td>
                                                        <td><input type="text" class="form-control" name="rejected_quantity" value=""></td>
                                                        <td>No.</td>
                                                        <td><input readonly type="text" name="rate" class="form-control money" value="<?= $price ?>"></td>
                                                        <td><input type="text" name="amount" readonly class="form-control money" value="<?= $item->price * $quantity ?>"></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }

                                            $service_items = $order->service_items();
                                            foreach ($service_items as $item) {
                                                $quantity = $item->unreceived_quantity();
                                                $price = $order->vat_inclusive == 'VAT PRICED' ? $item->price/(1+$vat_factor) : $item->price;
                                                if ($quantity > 0) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?= $item->description ?>
                                                            <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                                            <input type="hidden" name="item_type" value="service">
                                                            <input type="hidden" name="order_item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control" quantity_balance="<?= $quantity ?>" name="quantity" value="<?= $quantity; ?>"></td>
                                                        <td><input type="text" class="form-control" name="rejected_quantity" value=""></td>
                                                        <td>No.</td>
                                                        <td><input readonly type="text" name="rate" class="form-control money" value="<?= $price ?>"></td>
                                                        <td><input type="text" name="amount" readonly class="form-control money" value="<?= $item->price * $quantity ?>"></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        } else {
                                            $items = $grn->material_items();
                                            $total_amount = $foreign_total_amount = 0;
                                            foreach ($items as $item) {
                                                $stock = $item->stock_item();
                                                $material = $stock->material_item();
                                                $total_amount += $amount = $stock->price * $stock->quantity; ?>
                                                <tr>
                                                    <td style="width: 35%">
                                                        <input type="hidden" name="item_type" value="material">

                                                        <?= wordwrap($material->item_name, 50, '<br/>') ?>
                                                        <input type="hidden" name="item_id" value="<?= $item->stock_item()->item_id ?>">
                                                        <input type="hidden" name="order_item_id" value="<?= $item->purchase_order_material_item()->purchase_order_material_item_id ?>">
                                                    </td>
                                                    <td><input type="text" class="form-control" name="quantity" value="<?= $stock->quantity ?>"></td>
                                                    <td><input type="text" class="form-control" name="rejected_quantity" value="<?= $item->rejected_quantity ?>"></td>
                                                    <td><?= $material->unit()->symbol ?></td>
                                                    <td><input type="text" readonly name="rate" class="form-control money" value="<?= $stock->price ?>"></td>
                                                    <td><input type="text" name="amount" readonly class="form-control money" value="<?= $amount ?>"></td>
                                                </tr>
                                                <?php
                                            }

                                            $asset_items = $grn->asset_items();
                                            foreach ($asset_items as $item) {
                                                $total_amount += $amount = $item->price * $item->quantity; ?>
                                                <tr>
                                                    <td>
                                                        <?= $item->asset_name ?>
                                                        <input type="hidden" name="item_id" value="<?= $item->asset_item_id ?>">
                                                        <input type="hidden" name="item_type" value="asset">
                                                        <input type="hidden" name="order_item_id" value="<?= '' ?>">
                                                    </td>
                                                    <td><input type="text" class="form-control" name="quantity" value="<?= $item->quantity ?>"></td>
                                                    <td><input type="text" class="form-control" name="rejected_quantity" value="<?= $item->rejected_quantity ?>"></td>
                                                    <td>No.</td>
                                                    <td><input type="text" name="rate" readonly class="form-control money" value="<?= $item->price ?>"></td>
                                                    <td><input type="text" name="amount" readonly class="form-control money" value="<?= $amount ?>"></td>
                                                </tr>
                                                <?php
                                            }

                                            $service_items = $grn->service_items();
                                            foreach ($service_items as $item) {
                                                $total_amount += $amount = $item->rate * $item->received_quantity; ?>
                                                <tr>
                                                    <td>
                                                        <?= $item->description ?>
                                                        <input type="hidden" name="item_id" value="<?= $item->purchase_order_service_item_id ?>">
                                                        <input type="hidden" name="item_type" value="service">
                                                        <input type="hidden" name="order_item_id" value="<?= $item->purchase_order_service_item_id ?>">
                                                    </td>
                                                    <td><input type="text" class="form-control" name="quantity" value="<?= $item->received_quantity ?>"></td>
                                                    <td><input type="text" class="form-control" name="rejected_quantity" value="<?= $item->rejected_quantity ?>"></td>
                                                    <td>No.</td>
                                                    <td><input type="text" name="rate" readonly class="form-control money" value="<?= $item->rate ?>"></td>
                                                    <td><input type="text" name="amount" readonly class="form-control money" value="<?= $amount ?>"></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="5">Total</th>
                                            <th style="text-align: right" class="total_amount_display"></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="form-group col-xs-12">
                                    <label for="comments" class="control-label">Comments</label>
                                    <textarea name="comments" class="form-control"><?= $edit ? $grn->comments : '' ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="import_duties_<?= $order->{$order::DB_TABLE_PK} ?>">
                            <div class="row">
                                <div class="col-xs-12">
                                    <?php $this->load->view('procurements/purchase_orders/import_duties_form', ['edit' => $edit]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" style="display: none" class="btn btn-default btn-sm previous_to_item_details">
                    <i class="fa fa-arrow-circle-o-left"></i> Previous
                </button>
                <button type="button" class="btn btn-default btn-sm next_to_duties">
                    Next <i class="fa fa-arrow-circle-o-right"></i>
                </button>
                <!--<button style="display: none" type="button" class="btn btn-default btn-sm preview_purchase_order_grn">Preview GRN</button>-->
                <button style="display: none" type="button" class="btn btn-default btn-sm receive_purchase_order">Submit</button>
            </div>
        </form>
    </div>
</div>