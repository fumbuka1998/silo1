<?php
 $edit = isset($asset);
?>
<?php
    $edit = isset($asset);
    $modal_heading = $edit ? 'Edit Asset' : 'New Asset';
?>

<div class="modal-dialog">

    <div class="modal-content">
    <form>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $modal_heading ?></h4>
        </div>
        <div class="modal-body">
            <div class='row'>

                <div class="col-xs-12">

                     <div class="form-group col-md-6">
                        <label for="asset_name" class="control-label">Asset Name</label>
                        <input type="text" class="form-control" required name="asset_name" value="<?= $edit ? $asset->asset_name : '' ?>">
                        <input type="hidden" name="asset_id"  value="<?= $edit ? $asset->{$asset::DB_TABLE_PK} : '' ?>">
                        
                     </div>

                      <div class="form-group col-md-6">
                        <label for="asset_group_id" class="control-label">Asset Group</label>
                         <?= form_dropdown('asset_group_id',$asset_group_options,$edit ? $asset->asset_group_id: '',' class="form-control searchable"') ?>
                      </div>

                    </div>

                       <div class="col-xs-12">
                            <?php if($edit){?>
                        
                              
                            <input type="hidden" min="1" name="quantity" class="form-control number_format" value="<?= 1 ?>">
                            <input type="hidden" min="1" name="initial_code" class="form-control number_format" value="<?= 1 ?>">
                                <div class="form-group col-xs-6">
                                    <label for="quantity" class="control-label">Asset Code</label>
                                   <input type="text" name="asset_code" class="form-control" value="<?= $edit ? $asset->asset_code : '' ?>">
                                </div>

                            <?php }else{?>
                           
                            <input type="hidden" name="asset_code" class="form-control" value="<?= $edit ? $asset->asset_code : '' ?>">
                                <div class="form-group col-xs-3">
                                    <label for="quantity" class="control-label">Quantity</label>
                                    <input type="number" min="1" name="quantity" class="form-control number_format" value="<?= 1 ?>">
                                </div>
                                <div class="form-group col-xs-3">
                                    <label for="quantity" class="control-label">Initial Code</label>
                                    <input type="number" min="1" name="initial_code" class="form-control number_format" value="<?= 1 ?>">
                                </div>

                            <?php } ?>

                  
                            <div class="form-group col-md-6">
                                <label for="sub_location_id" class="control-label">Current Location</label>
                                <?= form_dropdown('sub_location_id', $sub_location_options, $edit ? $asset->sub_location_id : '', ' class="form-control searchable" ') ?>
                            </div>

                      </div>

                      <div class="col-xs-12">

                        <div class="form-group col-xs-6">
                            <label for="book_value" class="control-label">Book Value</label>
                            <input type="text" name="book_value" class="form-control number_format" value="<?= $edit ? $asset->book_value : '' ?>">
                        </div>

                        <div class="form-group col-xs-6">
                                <label for="registration_date" class="control-label">Registration Date</label>
                                <input type="text" name="registration_date" class="form-control datepicker" value="<?= $edit ? $asset->registration_date : '' ?>">
                        </div>
                     </div>

                    

                     <div class="col-xs-12">

                          <div class="form-group col-md-12">
                            <label for="description" class="control-label">Description</label>
                            <textarea name="description" class="form-control"><?= $edit ? $asset->description : '' ?></textarea>
                        </div>
                    </div>

                 
                </div>

            </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm save_asset_button">
                Save
            </button>
        </div>
    </form>
    </div>
</div>