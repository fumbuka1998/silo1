<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/30/2018
 * Time: 11:55 AM
 */
?>
<div class="modal-dialog">

    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Edit Material Price Form</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-12">
                            <label for="material_price_name" class="control-label">Material Name</label>
                            <select name="material_id" class="form-control">
                                <option value="<?= $material_price->{$material_price::DB_TABLE_PK} ?>" selected><?= $material_price->material_item()->item_name ?></option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="material_price_name" class="control-label">Quantity</label>
                            <input type="text" class="form-control" required name="quantity" id = "quantity" value="<?= $material_price->quantity ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="material_price_name" class="control-label">Price</label>
                            <input type="text" class="form-control number_format" required name="price" id = "price" value="<?= $material_price->price ?>">
                            </div>
                        <div class="form-group col-md-12">
                            <label for="material_price_name" class="control-label">Description</label>
                            <textarea class="form-control" rows="1" name="remarks"><?= $material_price->description ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_edit_material_price">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

