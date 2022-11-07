<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/24/2016
 * Time: 10:08 AM
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">

                <?php  if(isset($project)){?>

                    <button data-toggle="modal"
                            data-target="#new_material_installation_<?= $sub_location->{$sub_location::DB_TABLE_PK} ?>"
                            class="btn btn-default btn-xs">New Material Cost</button>

                    <div id="new_material_installation_<?= $sub_location->{$sub_location::DB_TABLE_PK} ?>" class="modal fade material_cost_form" role="dialog">
                        <?php

                        $sub_location_id =$sub_location->{$sub_location::DB_TABLE_PK};

                        $this->load->model('material_item');

                        $data['material_items'] = $this->material_item->location_material_items($location->location_id, $sub_location_id,true,null);
                        ?>

                        <?php $this->load->view('projects/costs/material/bulk_material_cost_form',$data);?>


                    </div>

                <?php   }  ?>

                <button data-toggle="modal"
                        data-target="#new_material_opening_balance_<?= $sub_location->{$sub_location::DB_TABLE_PK} ?>"
                        class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Opening Stock
                </button>
                <div id="new_material_opening_balance_<?= $sub_location->{$sub_location::DB_TABLE_PK} ?>"
                     class="modal fade opening_stock_form" role="dialog">
                    <?php $this->load->view('inventory/material/material_opening_stock_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-bordered table-hover table-striped sub_location_material_stock" sub_location_id="<?= $sub_location->{$sub_location::DB_TABLE_PK} ?>">
                    <thead>
                        <tr>
                            <th>Thumbnail</th><th>Material Item</th><th>Category</th><th>Unit</th><th>Part Number</th><th>Description</th><th>Available</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
