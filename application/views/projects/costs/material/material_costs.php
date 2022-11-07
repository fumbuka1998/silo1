<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 9/29/2016
 * Time: 11:28 AM
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="col-xs-12">
                <form class="form-inline">
                    <div class="form-group">
                        <label for="cost_center_id">Cost Center:  </label>
                        <?= form_dropdown('cost_center_selector',$cost_center_options,'',' class="form-control" ') ?>
                    </div>&nbsp; &nbsp;&nbsp; &nbsp;
                </form>
            </div>
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_material_cost" class="btn btn-default btn-xs">
                    New Material Cost
                </button>
                <div id="new_material_cost" class="modal fade material_cost_form" role="dialog">
                    <?php $this->load->view('projects/costs/material/material_cost_form'); ?>
                </div>
                <button data-toggle="modal" data-target="#new_project_material_cost" class="btn btn-default btn-xs">
                    New Bulk Material Cost
                </button>
                <div id="new_project_material_cost" class="modal fade bulk_material_cost_form" role="dialog">
                    <?php $this->load->view('projects/costs/material/new_material_costs_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover material_costs_items">
                    <thead>
                    <tr>
                        <th>Date</th><th>Material</th><th>Quantity</th><th>Unit</th><th>Rate</th><th>Amount</th><th>Description</th><th></th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th colspan="5">Total</th><th id="total_cost_amount_display" style="text-align: right"></th><th colspan="2"></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
