<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/26/2018
 * Time: 11:07 PM
 */
$edit = isset($tender_sub_component);
?>
<div class="modal-dialog">
    <form>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Tender Sub Component</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-xs-12">
                            <label for="sub_component_name" class="control-label">Name</label>
                            <input type="text" class="form-control" required name="sub_component_name" value="<?= $edit ? $tender_sub_component->sub_component_name : '' ?>">
                            <input type="hidden" name="tender_component_id" value="<?=!$edit ? $component_id :'' ?>">
                            <input type="hidden" name="sub_component_id" value="<?= $edit ? $tender_sub_component->{$tender_sub_component::DB_TABLE_PK} : ''?>">
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="lumpsum_price" class="control-label">Lumpsum Price</label>
                            <input type="text" class="form-control number_format" name="lumpsum_price" value="<?= $edit ? $tender_sub_component->lumpsum_price : '' ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_sub_component">Save</button>
            </div>
        </div>
    </form>
</div>





