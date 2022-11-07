<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 12/17/2018
 * Time: 5:16 PM
 */

$currency_options = currency_dropdown_options();
$approval_module_options = [
    '&nbsp;'=>'&nbsp;',
    '1' => 'General Requisition',
    '2' => 'Project Requisition'
];
$measurement_unit_options = measurement_unit_dropdown_options();
$vendor = $enquiry->enquiry_to();

?>
<div class="modal-dialog" style="width: 80%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Requisition Form</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-2">
                            <label for="enquiry_date" class="control-label">Request Date</label>
                            <input type="hidden" name="enquiry_id" value="<?= $enquiry->{$enquiry::DB_TABLE_PK}?>">
                            <input type="text" class="form-control datepicker" required name="request_date" value="<?=  date('Y-m-d') ?>">
                        </div>

                        <div class="form-group col-md-2">
                            <label for="required_date" class="control-label">Required Date</label>
                            <input type="text" class="form-control datepicker" name="required_date" value="">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="requisition_type" class="control-label">Requisition Type</label>
                            <?= form_dropdown('approval_module_id',$approval_module_options, '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-3 ">
                            <label for="cost_center_id" class="control-label">Requesting For</label>
                            <?= form_dropdown('requisition_cost_center_id', [],'', ' class="form-control searchable" ') ?>
                        </div>

                        <div class="form-group col-md-2 ">
                            <label for="rate" class="control-label">Currency</label>
                            <?= form_dropdown('currency_id',$currency_options, '',' class="form-control searchable"') ?>
                        </div>

                        <div class="col-xs-12 table-responsive">
                            <table class="table table-hover" width="100%">
                                <thead>
                                <tr>
                                    <th>Item</th><th>Quantity</th><th>Unit</th><th>Rate</th><th>Amount</th><th></th>
                                </tr>
                                <tr class="material_row_template" style="display: none">
                                    <td>
                                        <?= form_dropdown('material_id',$material_options,'',' class=" form-control"') ?>
                                        <input type="hidden" name="item_type" value="material">
                                        <input type="hidden" name="source_type" value="vendor">
                                        <input type="hidden" name="source_id" value="<?= $vendor->{$vendor::DB_TABLE_PK} ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"  name="quantity" value="">
                                    </td>
                                    <td class="unit_display"></td>
                                    <td>
                                        <input type="text" class="form-control"  name="rate" value="" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"  name="amount" value="" readonly>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                <tr class="asset_row_template" style="display: none">
                                    <td>
                                        <?= form_dropdown('asset_item_id',$asset_options,'',' class=" form-control"') ?>
                                        <input type="hidden" name="item_type" value="asset">
                                        <input type="hidden" name="source_type" value="vendor">
                                        <input type="hidden" name="source_id" value="<?= $vendor->{$vendor::DB_TABLE_PK} ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"  name="quantity" value="">
                                    </td>
                                    <td class="unit_display"></td>
                                    <td>
                                        <input type="text" class="form-control"  name="rate" value="" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"  name="amount" value="" readonly>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                <tr class="service_row_template" style="display: none">
                                    <td>
                                        <input type="text" class="form-control"  name="service_description" placeholder="Service Descrition" value="" style="width: 350px">
                                        <input type="hidden" name="item_type" value="service">
                                        <input type="hidden" name="source_type" value="vendor">
                                        <input type="hidden" name="source_id" value="<?= $vendor->{$vendor::DB_TABLE_PK} ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"  name="quantity" value="" >
                                    </td>
                                    <td><?= form_dropdown('uom_id',$measurement_unit_options,'','  class=" form-control" ') ?></td>
                                    <td>
                                        <input type="text" class="form-control"  name="rate" value="" >
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"  name="amount" value="" readonly>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $material_items = $enquiry->material_items();
                                    foreach($material_items as $material_item){
                                        ?>
                                        <tr>
                                            <td>
                                                <?= form_dropdown('material_id',$material_options,$material_item->material_item_id,' class=" form-control"') ?>
                                                <input type="hidden" name="item_type" value="material">
                                                <input type="hidden" name="source_type" value="vendor">
                                                <input type="hidden" name="source_id" value="<?= $vendor->{$vendor::DB_TABLE_PK} ?>">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"  name="quantity" value="<?= $material_item->quantity ?>">
                                            </td>
                                            <td class="unit_display"><?= $material_item->material_item()->unit()->symbol ?></td>
                                            <td>
                                                <input type="text" class="form-control"  name="rate" value="" >
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"  name="amount" value="" readonly>
                                            </td>
                                            <td>
                                                <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                            </td>
                                        </tr>
                                        <?php
                                    }

                                    $asset_items = $enquiry->asset_items();
                                    foreach($asset_items as $asset_item){
                                        ?>
                                        <tr>
                                            <td>
                                                <?= form_dropdown('asset_item_id',$asset_options,$asset_item->asset_item_id,' class=" form-control"') ?>
                                                <input type="hidden" name="item_type" value="asset">
                                                <input type="hidden" name="source_type" value="vendor">
                                                <input type="hidden" name="source_id" value="<?= $vendor->{$vendor::DB_TABLE_PK} ?>">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"  name="quantity" value="<?= $asset_item->quantity ?>">
                                            </td>
                                            <td class="unit_display"></td>
                                            <td>
                                                <input type="text" class="form-control"  name="rate" value="" >
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"  name="amount" value="" readonly>
                                            </td>
                                            <td>
                                                <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                            </td>
                                        </tr>
                                        <?php
                                    }

                                    $service_items = $enquiry->service_items();
                                    foreach($service_items as $service_item){
                                        ?>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control"  name="service_description" placeholder="Service Descrition" value="<?= $service_item->description ?>" style="width: 350px">
                                                <input type="hidden" name="item_type" value="service">
                                                <input type="hidden" name="source_type" value="vendor">
                                                <input type="hidden" name="source_id" value="<?= $vendor->{$vendor::DB_TABLE_PK} ?>">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"  name="quantity" value="<?= $service_item->quantity ?>" >
                                            </td>
                                            <td><?= form_dropdown('uom_id',$measurement_unit_options,$service_item->measurement_unit_id,'  class=" form-control" ') ?></td>
                                            <td>
                                                <input type="text" class="form-control"  name="rate" value="" >
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"  name="amount" value="" readonly>
                                            </td>
                                            <td>
                                                <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                            </td>
                                        </tr>
                                        <?php
                                    } ?>
                                </tbody>
                                <tfoot>
                                    <tr class="text_styles">
                                        <th colspan="4"  style="text-align: right">TOTAL</th>
                                        <th class="number_format total_amount_display" style="text-align: right"></th>
                                        <th></th>
                                    </tr>

                                    <tr>
                                        <td colspan="4"  style="text-align: right">FREIGHT CHARGES</td>
                                        <td><input name="freight" class="form-control money text-right" value=""></td>
                                        <td colspan="4" rowspan="3">
                                            <div style="text-align: center" class="form-group">
                                                <input type="checkbox" name="vat_inclusive">
                                                <input type="hidden" name="vat_percentage" class="form-control" value="<?= 18 ?>">
                                                &nbsp;&nbsp;
                                                <label for="vat_inclusive" class="control-label text-center">VAT inclusive</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr >
                                        <td colspan="4"  style="text-align: right">INSPECTION AND OTHER CHARGES</td>
                                        <td><input name="inspection_and_other_charges" class="form-control money text-right" value=""></td>

                                    </tr>
                                    <tr class="text_styles">
                                        <th colspan="4" style="text-align: right" >GRAND TOTAL</th><th class="grand_total_display" style="text-align: right"></th>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right" colspan="8">
                                            <button type="button" class="btn btn-default btn-xs material_row_adder">
                                                <i class="fa fa-plus"></i> Material
                                            </button>
                                            <button type="button" class="btn btn-default btn-xs asset_row_adder">
                                                <i class="fa fa-plus"></i> Asset
                                            </button>
                                            <button type="button" class="btn btn-default btn-xs service_row_adder">
                                                <i class="fa fa-plus"></i> Service
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group col-md-8">
                                <label for="comments" class="control-label">Requesting Comments</label>
                                <textarea type="text" class="form-control" name="comments"></textarea>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="foward_to" class="control-label ">Forward To</label>
                                <?= form_dropdown('foward_to',[], '','class="form-control searchable foward_to_options"')?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_enquiry_requisition">Submit</button>
            </div>
        </form>
    </div>
</div>

