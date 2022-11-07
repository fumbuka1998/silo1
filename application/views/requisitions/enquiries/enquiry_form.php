<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 12/17/2018
 * Time: 5:16 PM
 */

?>
<?php
$edit = isset($enquiry);
$measurement_unit_options = measurement_unit_dropdown_options();
?>
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Enquiry Form</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-3">
                            <label for="enquiry_date" class="control-label">Enquiry Date</label>
                            <input type="hidden" name="enquiry_id" value="<?= $edit ? $enquiry->{$enquiry::DB_TABLE_PK} : '' ?>">
                            <input type="text" class="form-control datepicker" required name="enquiry_date" value="<?= $edit ? $enquiry->enquiry_date : date('Y-m-d') ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="project_id" class="control-label">Enquiry To</label>
                            <?= form_dropdown('vendor_id', $stakeholder_options,$edit ? $enquiry->enquiry_to : '', ' class="form-control searchable" ') ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="required_date" class="control-label">Required Date</label>
                            <input type="text" class="form-control datepicker" required name="required_date" value="<?= $edit ? $enquiry->required_date : '' ?>">
                        </div>

                        <div class="col-xs-12 table-responsive">
                            <table class="table table-hover"">
                                <thead>
                                <tr>
                                    <th>Item</th><th>Unit</th><th>Quantity</th><th>Remarks</th><th></th>
                                </tr>
                                <tr class="material_row_template" style="display: none">
                                    <td>
                                        <?= form_dropdown('material_id',$material_options,'',' class=" form-control"') ?>
                                        <input type="hidden" name="item_type" value="material">
                                    </td>
                                    <td class="unit_display"></td>
                                    <td>
                                        <input type="text" class="form-control"  name="enquired_quantity" value="">
                                    </td>
                                    <td>
                                        <textarea type="text" class="form-control" name="remarks"></textarea>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                <tr class="asset_row_template" style="display: none">
                                    <td>
                                        <?= form_dropdown('asset_item_id',$asset_options,'',' class=" form-control"') ?>
                                        <input type="hidden" name="item_type" value="asset">
                                    </td>
                                    <td></td>
                                    <td>
                                        <input type="text" class="form-control"  name="enquired_quantity" value="">
                                    </td>
                                    <td>
                                        <textarea type="text" class="form-control" name="remarks"></textarea>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>

                                <tr class="service_row_template" style="display: none">
                                    <td>
                                        <input type="text" class="form-control"  name="description" placeholder="Service Descrition" value="" style="width: 350px">
                                        <input type="hidden" name="item_type" value="service">
                                    </td>
                                    <td><?= form_dropdown('measurement_unit_id',$measurement_unit_options,'','class="form-control"')?></td>
                                    <td>
                                        <input type="text" class="form-control"  name="enquired_quantity" value="" >
                                    </td>
                                    <td>
                                        <textarea type="text" class="form-control" name="remarks"></textarea>
                                    </td>
                                    <td>
                                        <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php if(!$edit){ ?>
                                    <tr>
                                        <td>
                                            <?= form_dropdown('material_id',$material_options,'',' class=" form-control" style="width: 350px"') ?>
                                            <input type="hidden" name="item_type" value="material">
                                        </td>
                                        <td class="unit_display"></td>
                                        <td>
                                            <input type="text" class="form-control"  name="enquired_quantity" value="">
                                        </td>
                                        <td>
                                            <textarea type="text" class="form-control" name="remarks"></textarea>
                                        </td>
                                        <td>
                                            <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                    <?php } else {
                                        $material_items = $enquiry->material_items();
                                        foreach($material_items as $material_item){
                                        ?>
                                            <tr>
                                                <td>
                                                    <?= form_dropdown('material_id',$material_options,$material_item->material_item_id,' class=" form-control"') ?>
                                                    <input type="hidden" name="item_type" value="material">
                                                </td>
                                                <td class="unit_display"><?= $material_item->material_item()->unit()->symbol ?></td>
                                                <td>
                                                    <input type="text" class="form-control"  name="enquired_quantity" value="<?= $material_item->quantity ?>" style="width: 100px">
                                                </td>
                                                <td>
                                                    <textarea type="text" class="form-control" name="remarks"><?= $material_item->remarks ?></textarea>
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
                                                </td>
                                                <td></td>
                                                <td>
                                                    <input type="text" class="form-control"  name="enquired_quantity" value="<?= $asset_item->quantity ?>" style="width: 100px">
                                                </td>
                                                <td>
                                                    <textarea type="text" class="form-control" name="remarks"><?= $asset_item->remarks ?></textarea>
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
                                                    <input type="text" class="form-control"  name="description" placeholder="Service Descrition" value="<?= $service_item->description ?>" style="width: 350px">
                                                    <input type="hidden" name="item_type" value="service">
                                                </td>
                                                <td><?= form_dropdown('measurement_unit_id',$measurement_unit_options,$service_item->measurement_unit_id,'class="form-control"')?></td>
                                                <td>
                                                    <input type="text" class="form-control"  name="enquired_quantity" value="<?= $service_item->quantity?>" style="width: 100px">
                                                </td>
                                                <td>
                                                    <textarea type="text" class="form-control" name="remarks"><?= $service_item->remarks ?></textarea>
                                                </td>
                                                <td>
                                                    <button title="Remove Row" type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } ?>
                                </tbody>
                                <tfoot>
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
                            <div class="form-group">
                                <label for="comments" class="control-label">Comments</label>
                                <textarea type="text" class="form-control" name="comments"><?= $edit ? $enquiry->comments : '' ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_enquiry">Submit</button>
            </div>
        </form>
    </div>
</div>
