<div style="width: 90%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Approved To Be Ordered</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-3">
                            <label for="vendor_id" class="control-label">Vendor</label>
                            <?= form_dropdown('vendor_id', $stakeholder_options, $stakeholder_id, ' class="form-control searchable" disabled') ?>
                            <input type="hidden" name="requisition_id" value="<?= $requisition_id ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="location_id" class="control-label">Location</label>
                            <?= form_dropdown('location_id', $location_options, '', ' class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="currency_id" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id', $currency_options, $currency_id, ' readonly class="form-control" disabled') ?>
                        </div>
                        <?php if ($project) { ?>
                            <div class="form-group col-md-3">
                                <label for="project_name" class="control-label">Project</label>
                                <input type="text" class="form-control" readonly name="project_name" value="<?= $project->project_name ?>">
                                <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>">
                            </div>
                        <?php } else {
                        ?>
                            <div class="form-group col-md-3">
                                <label for="project_name" class="control-label">Cost Center</label>
                                <input type="text" class="form-control" readonly name="cost_center_name" value="<?= $cost_center->cost_center_name ?>">
                                <input type="hidden" name="cost_center_id" value="<?= $cost_center->{$cost_center::DB_TABLE_PK} ?>">
                            </div>
                        <?php
                        } ?>

                        <div class="form-group col-md-3">
                            <label for="reference" class="control-label">Reference</label>
                            <input type="text" class="form-control" name="reference">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="issue_date" class="control-label">Issue Date</label>
                            <input type="text" class="form-control datepicker" name="issue_date" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="delivery_date" class="control-label">Delivery Date</label>
                            <input type="text" class="form-control datepicker" name="delivery_date">
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $total_amount = 0;
                                foreach ($approved_material_items as $item) {
                                    $requisition_item = $item->requisition_material_item();
                                    $material = $requisition_item->material_item();
                                    $total_amount += $row_amount = $item->approved_quantity * $item->approved_rate;

                                ?>
                                    <tr>
                                        <td>
                                            <?= wordwrap($material->item_name, 100, '<br/>') ?>
                                            <input type="hidden" name="item_type" value="material">
                                            <input type="hidden" name="item_id" value="<?= $requisition_item->material_item_id ?>">
                                        </td>
                                        <td><?= $material->unit()->symbol ?></td>
                                        <td style="text-align: right">
                                            <?= $item->approved_quantity ?>
                                            <input type="hidden" name="quantity" value="<?= $item->approved_quantity ?>">
                                        </td>
                                        <td style="text-align: right">
                                            <?= number_format($item->approved_rate, 2) ?>
                                            <input type="hidden" name="rate" value="<?= $item->approved_rate ?>">
                                        </td>
                                        <td style="text-align: right">
                                            <?= number_format($row_amount, 2) ?>
                                            <input type="hidden" name="amount" value="<?= number_format($row_amount, 2) ?>">
                                        </td>
                                    </tr>
                                <?php
                                }

                                foreach ($approved_asset_items as $item) {
                                    $requisition_item = $item->requisition_asset_item();
                                    $asset_item = $requisition_item->asset_item();
                                    $total_amount += $row_amount = $item->approved_quantity * $item->approved_rate;

                                ?>
                                    <tr>
                                        <td>
                                            <?= wordwrap($asset_item->asset_name, 100, '<br/>') ?>
                                            <input type="hidden" name="item_type" value="asset">
                                            <input type="hidden" name="item_id" value="<?= $requisition_item->asset_item_id ?>">
                                        </td>
                                        <td>No.</td>
                                        <td style="text-align: right">
                                            <?= $item->approved_quantity ?>
                                            <input type="hidden" name="quantity" value="<?= $item->approved_quantity ?>">
                                        </td>
                                        <td style="text-align: right">
                                            <?= number_format($item->approved_rate, 2) ?>
                                            <input type="hidden" name="rate" value="<?= $item->approved_rate ?>">
                                        </td>
                                        <td style="text-align: right">
                                            <?= number_format($row_amount, 2) ?>
                                            <input type="hidden" name="amount" value="<?= number_format($row_amount, 2) ?>">
                                        </td>
                                    </tr>
                                <?php
                                }


                                foreach ($approved_service_items as $item) {
                                    $requisition_item = $item->requisition_service_item();
                                    $total_amount += $row_amount = $item->approved_quantity * $item->approved_rate;

                                ?>
                                    <tr>
                                        <td>
                                            <?= wordwrap($requisition_item->description, 100, '<br/>') ?>
                                            <input type="hidden" name="item_type" value="service">
                                            <input type="hidden" name="item_id" value="<?= $requisition_item->description ?>">
                                        </td>
                                        <td>No.</td>
                                        <td style="text-align: right">
                                            <?= $item->approved_quantity ?>
                                            <input type="hidden" name="quantity" value="<?= $item->approved_quantity ?>">
                                            <input type="hidden" name="unit_id" value="<?= $requisition_item->measurement_unit_id ?>">
                                        </td>
                                        <td style="text-align: right">
                                            <?= number_format($item->approved_rate, 2) ?>
                                            <input type="hidden" name="rate" value="<?= $item->approved_rate ?>">
                                        </td>
                                        <td style="text-align: right">
                                            <?= number_format($row_amount, 2) ?>
                                            <input type="hidden" name="amount" value="<?= number_format($row_amount, 2) ?>">
                                        </td>
                                    </tr>
                                <?php
                                }

                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4">Total</th>
                                    <th style="text-align: right" class="total_amount_display"><?= number_format($total_amount, 2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <?php
                    $freight = $requisition_approval->freight;
                    $inspection = $requisition_approval->inspection_and_other_charges;
                    ?>

                    <div class="col-xs-3">
                        <div class="form-group">
                            <input type="hidden" name="freight" class="form-control number_format" value="<?= $freight ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="hidden" name="inspection_and_other_charges" class="form-control number_format" value="<?= $inspection  ?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <?php
                        if ($requisition_approval->vat_inclusive == 'VAT COMPONENT') {
                            $grand_total = ($total_amount + $freight + $inspection) * 1.18;
                            $vat_amount = number_format((($total_amount + $freight + $inspection) * 0.18), 2);
                        } else {
                            $grand_total = ($total_amount + $freight + $inspection);
                            $vat_amount = number_format(0, 2);
                        }
                        ?>
                        <table class="table table-responsive table-bordered">
                            <tr>
                                <td style="text-align: right">FREIGHT CHARGES</td>
                                <td style="text-align: right"><?= number_format($freight, 2) ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right">INSPECTION AND OTHER CHARGES</td>
                                <td style="text-align: right"><?= number_format($inspection, 2) ?></td>
                            </tr>
                            <tr>
                                <td style="text-align: right">VAT</td>
                                <td style="text-align: right" class="vat_amount_display"><?= $vat_amount ?></td>
                            </tr>
                            <tr style="background-color: #f0f0f0; font-weight: bold">
                                <td style="text-align: right">GRAND TOTAL</td>
                                <td style="text-align: right" class="grand_total_display"><?= number_format($grand_total, 2) ?></td>
                            </tr>
                        </table>

                    </div>
                    <?php
                        $no_vat = $requisition_approval->vat_percentage == 0 && is_null($requisition_approval->vat_inclusive);
                    ?>
                    <div class="row col-md-12">
                        <div class="form-group col-md-8"></div>
                        <div class="form-group col-md-2">
                            <input type="checkbox" name="vat_inclusive" disabled <?= !empty($requisition_approval) && $requisition_approval->vat_inclusive == 'VAT PRICED' ? 'disabled' : '' ?> <?= $requisition_approval->vat_inclusive == 'VAT COMPONENT' ? "checked" : '' ?>>
                            <input type="hidden" name="vat_priced_po" value="<?= !empty($requisition_approval) && $requisition_approval->vat_inclusive == 'VAT PRICED' ? 'true' : 'false' ?>">
                            <label for="vat_inclusive" class="control-label text-center"> Include VAT </label>
                        </div>
                        <div class="form-group col-md-2" <?php if($no_vat){ ?>style="display: none;" <?php } ?>>
                            <?php $vat_options = array($requisition_approval->vat_percentage => 'VAT@'.$requisition_approval->vat_percentage.'%') ?>
                            <?= form_dropdown('vat_percentage', $vat_options, $requisition_approval->vat_percentage, ' class="form-control searchable"') ?>
                        </div>
                    </div>
                    <div class="form-group col-md-8">
                        <label for="comments" class="control-label">Terms and Condition</label>
                        <textarea name="comments" class="form-control"></textarea>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="location_id" class="control-label">Assign Handler</label>
                        <?= form_dropdown('handler_id', $procurement_members_options, '', ' class="form-control searchable"') ?>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_pre_ordered_purchase_order">Save</button>
            </div>
        </form>
    </div>
</div>