<?php
/**
 * Created by PhpStorm.
 * User: Munyaki
 * Date: 27-Sep-17
 * Time: 3:43 PM

 */

//inspect_object($material_disposal);

?>

<div class="modal-dialog modal-lg" style="width: 90%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Material Disposal </h4>
        </div>
        <form>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">

                        <div class="box-body">
                            <div class="form-horizontal">

                                <div class="form-group col-md-4 col-sm-6">
                                    <label  class="col-sm-4 control-label">Disposal Date:</label>
                                    <div class="form-control-static col-sm-8">
                                        <?= custom_standard_date($material_disposal->disposal_date) ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-6">
                                    <label  class="col-sm-4 control-label">Location:</label>
                                    <div class="form-control-static col-sm-8">
                                        <?=$material_disposal->location()->location_name ?>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-6">
                                    <label  class="col-sm-4 control-label">Disposed by:</label>
                                    <div class="form-control-static col-sm-8">
                                        <?= $material_disposal->employee()->full_name() ?>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <hr/>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 table-responsive">

                            <table class="table table-bordered table-hover table-striped " disposal_id="<?= $material_disposal->{$material_disposal::DB_TABLE_PK} ?>">
                                <thead>
                                <tr>
                                    <th>Material Item</th><th>Sub_location</th><th>Project_id</th><th>Quantity</th><th>Rate</th><th>Remarks</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php

                                $disposed_items=$material_disposal->material_disposal_items();
                                    if (!empty($disposed_items )){
                                foreach ($disposed_items as $disposed_item){
                                ?>
                                <tr>
                                    <td><?= $disposed_item->item_name()->item_name ?></td>
                                    <td><?= $disposed_item->sub_location()->sub_location_name ?></td>
                                    <td><?= $disposed_item->project()->project_name ?></td>
                                    <td><?= $disposed_item->quantity ?></td>
                                    <td><?= $disposed_item->rate ?></td>
                                    <td><?= $disposed_item->remarks ?></td>

                                </tr>
                                    <?php
                                   }
                                    }else{ ?>
                                        <tr><td colspan="7">

                                            <div class="alert alert-info">No Items found for this Disposal</div>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                 ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
