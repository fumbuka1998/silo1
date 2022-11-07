<?php
    $edit = isset($item)
    
    //inspect_object($material_items);
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close specific_modal_hide" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Material Cost</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-xs-4">
                        <label for="cost_center_id">Cost Center </label>
                        <?= form_dropdown('cost_center_id',$project->cost_center_options(),
                            $edit ? isset($item->task_id) ? $item->task_id : '' : '',
                            ' class="form-control searchable" '
                        ) ?>
                        <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>"/>
                       
                        <input type="hidden" name="source_sub_location_id" value="<?= $sub_location->{$sub_location::DB_TABLE_PK} ?>"/>


                    </div>
                    <div class="form-group col-md-4">
                        <label for="date" class="control-label">Date</label>
                        <input type="text" class="form-control datepicker" required name="date" value="<?= $edit ? $item->cost_date : date('Y-m-d') ?>">
                    </div>

                <div class="col-xs-12 table-responsive">

               
                    <table class="table table-bordered table-hover table-striped" sub_location_id="<?= $sub_location->{$sub_location::DB_TABLE_PK} ?>">
                        <thead>
                            <tr>
                               
                                <th>Material Item</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Available</th>
                                <th style="width:15%">Quantity</th>
                                <th style="width:15%">Rate</th>
                                <th style="width:25%">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($material_items as $material_item){

                                $qty=$material_item->sub_location_balance($sub_location->{$sub_location::DB_TABLE_PK},$project->{$project::DB_TABLE_PK});
                                $rate=$material_item->sub_location_average_price($sub_location->{$sub_location::DB_TABLE_PK},$project->{$project::DB_TABLE_PK});

                                ?>

                            <tr>
                                <td><?= $material_item->item_name ?></td>
                                 <td><?= $material_item->material_item_category()->category_name ?></td>
                                <td><?= $material_item->unit()->symbol ?></td>
                                <td><?= $qty ?></td>
                                <td>

                                 <input type="text" name="quantity" class="form-control" previous_quantity="<?= $edit ? $item->quantity : 0 ?>" value="<?= $edit ? $item->quantity : 0 ?>"> 

                                 <input type="hidden" class="form-control"  name="material_id" value="<?= $material_item->item_id ?>">

                                 <input type="hidden" name="item_id" value="<?= $edit ? $item->{$item::DB_TABLE_PK} : '' ?>">

                                </td>
                                <td>
                                <input class="form-control number_format" readonly name="rate" value="<?= $edit ? $item->rate : $rate ?>">
                                </td>
                               
                                <td>
                                   <div class="form-group col-xs-12">
                                    
                                    <textarea name="description" class="form-control"><?= $edit ? $item->description : ''  ?></textarea>
                                   </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>


                    </table>
                </div>

                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm save_bulk_material_cost">
                Save
            </button>
        </div>
        </form>
    </div>
</div>