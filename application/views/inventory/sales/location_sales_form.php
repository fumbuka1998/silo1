<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/9/2018
 * Time: 8:03 AM
 */

$edit = isset($sale)
?>

<div style="width: 80%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Material And Asset Sales </h4>
        </div>
        <form>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="sale_date control_label">Sale Date</label>
                            <input type="hidden" name="location_id" value="<?= $location->{$location::DB_TABLE_PK} ?>">
                            <input type="text" class="form-control datepicker" name="sale_date" value="<?= $edit ? $sale->sale_date : date('Y-m-d') ?>" >
                            <input name="stock_sale_id" type="hidden" value="<?= $edit ? $sale->{$sale::DB_TABLE_PK} : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="control_label">Client</label>
                            <?= form_dropdown('client_id',$client_options, $edit ? $sale->client_id : '', ' class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="control_label">Project</label>
                            <?= form_dropdown('project_id', $project_options, $edit ? $sale->project_id : '', ' class="form-control searchable"') ?>
                        </div>

                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Source</th><th>Item</th><th>Quantinty</th><th>Price</th><th>Remarks</th><th></th>
                                </tr>
                                <tr style="display: none" class="sales_material_row_template">
                                    <td style="width: 30%">
                                        <?= form_dropdown('source_sub_location_id', $sub_location_options, '', ' class="form-control" ') ?>
                                    </td>
                                    <td style="width: 30%">
                                        <?= form_dropdown('material_id', [], '', ' class="form-control" ') ?>
                                        <input type="hidden" name="item_type" value="material">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" previous_quantity="0" name="quantity" class="form-control">
                                            <span class="input-group-addon unit_display"></span>
                                        </div>
                                    </td>

                                    <td>
                                        <input type="text" class="form-control number_format" name="price" value="">
                                    </td>
                                    <td>
                                        <textarea rows="1" class="form-control" name="remarks"></textarea>
                                    </td>
                                    <td>
                                        <button class="btn btn-xs btn-danger sales_row_remover">
                                            <i class="fa fa-close"></i>
                                        </button>
                                    </td>
                                </tr>

                                <tr style="display: none" class="sales_asset_row_template">
                                    <td style="width: 30%">
                                        <?= form_dropdown('source_sub_location_id', $sub_location_options, '', ' class="form-control" ') ?>
                                    </td>
                                    <td style="width: 30%">
                                        <?= form_dropdown('asset_id', [], '', ' class="form-control" ') ?>
                                        <input type="hidden" name="item_type" value="asset">
                                    </td>
                                    <td></td>
                                    <td>
                                        <input type="text" class="form-control number_format" name="price" value="">
                                    </td>
                                    <td>
                                        <textarea  class="form-control" name="remarks"></textarea>
                                    </td>
                                    <td>
                                        <button class="btn btn-xs btn-danger sales_row_remover">
                                            <i class="fa fa-close"></i>
                                        </button>
                                    </td>
                                </tr>

                                </thead>
                                <tbody>
                                <?php if(!$edit){?>
                                <tr>
                                    <td style="width: 30%">
                                        <?= form_dropdown('source_sub_location_id', $sub_location_options, '', ' class="form-control searchable" ') ?>
                                    </td>
                                    <td style="width: 30%">
                                        <?= form_dropdown('material_id', [], '', ' class="form-control searchable" ') ?>
                                        <input type="hidden" name="item_type" value="material">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" previous_quantity="0" name="quantity" class="form-control">
                                            <span class="input-group-addon unit_display"><?= $edit ? $unit_symbol : '&nbsp;' ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control number_format" name="price" value="">
                                    </td>
                                    <td>
                                        <textarea  class="form-control" name="remarks"></textarea>
                                    </td>
                                    <td>
                                        <button class="btn btn-xs btn-danger sales_row_remover">
                                            <i class="fa fa-close"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php } else {
                                    $material_items = $sale->material_items();
                                    foreach ($material_items as $item){
                                        $material = $item->material_item();
                                        $unit_symbol = $material->unit()->symbol;
                                        ?>
                                        <tr>
                                            <td style="width: 30%">
                                                <?= form_dropdown('source_sub_location_id', $sub_location_options, $item->source_sub_location_id, ' class="form-control searchable" ') ?>
                                            </td>
                                            <td style="width: 30%">
                                                <?= form_dropdown('material_id', [
                                                        $material->{$material::DB_TABLE_PK} => $material->item_name
                                                ], $item->material_item_id, ' class="form-control searchable" ') ?>
                                                <input type="hidden" name="item_type" value="material">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" previous_quantity="<?= $item->quantity ?>" name="quantity" class="form-control" value="<?= $item->quantity ?>">
                                                    <span class="input-group-addon unit_display"><?= $unit_symbol ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control number_format" name="price" value="<?= $item->price ?>">
                                            </td>
                                            <td>
                                                <textarea  class="form-control" name="remarks"><?= $item->remarks ?></textarea>
                                            </td>
                                            <td>
                                                <button class="btn btn-xs btn-danger sales_row_remover">
                                                    <i class="fa fa-close"></i>
                                                </button>
                                            </td>
                                        </tr>
                                <?php
                                    }

                                    $asset_items = $sale->asset_items();
                                    foreach ($asset_items as $item){
                                        $asset = $item->asset();
                                        $latest_history = $asset->latest_sub_location_history();
                                        $history_id = $latest_history->{$latest_history::DB_TABLE_PK};
                                        ?>
                                        <tr>
                                            <td style="width: 30%">
                                                <?= form_dropdown('source_sub_location_id', $sub_location_options, $latest_history->sub_location_id, ' class="form-control searchable" ') ?>
                                            </td>
                                            <td style="width: 30%">
                                                <?= form_dropdown('asset_id', [
                                                        $history_id => $asset->asset_code()
                                                ], '', ' class="form-control searchable" ') ?>
                                                <input type="hidden" name="item_type" value="asset">
                                            </td>
                                            <td></td>
                                            <td>
                                                <input type="text" class="form-control number_format" name="price" value="<?= $item->price ?>">
                                            </td>
                                            <td>
                                                <textarea  class="form-control" name="remarks"><?= $item->remarks ?></textarea>
                                            </td>
                                            <td>
                                                <button class="btn btn-xs btn-danger sales_row_remover">
                                                    <i class="fa fa-close"></i>
                                                </button>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                }?>
                                <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <button type="button" class="btn btn-default btn-xs sales_asset_row_adder pull-right">Add Asset Row</button>
                                        <span class="pull-right">&nbsp;</span>
                                        <button type="button" class="btn btn-default btn-xs sales_material_row_adder pull-right">Add Material Row</button>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="comments" class="control-label">Comments</label>
                            <textarea class="form-control" name="comments"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-md pull-right save_location_sales">Submit</button>
            </div>
        </form>
    </div>
</div>