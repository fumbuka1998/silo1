<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/22/2016
 * Time: 7:32 PM
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_material_item_category" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Category
                </button>
                <div id="new_material_item_category" class="modal fade" role="dialog">
                    <?php $this->load->view('inventory/settings/material_item_category_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="material_item_categories_list" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Category Name</th><th>Under</th><th>Description</th><th>Number of Items</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
