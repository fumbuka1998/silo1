<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 4/27/2017
 * Time: 2:48 PM
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
            <button data-toggle="modal" data-target="#new_permanent_labour_cost" class="btn btn-default btn-xs">
                New Permanent Labour Cost
            </button>
            <div id="new_permanent_labour_cost" class="modal fade permanent_labour_cost_form" role="dialog">
                <?php $this->load->view('projects/costs/labour/permanent_labour_cost_form'); ?>
            </div>
        </div>
    </div>
</div>
<div class="box-body">
    <div class="row">
        <div class="col-xs-12">
            <table project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover permanent_labour_costs_items">
                <thead>
                <tr>
                    <th>Employee Name</th><th>Position</th><th>Working Mode</th><th>Dates</th><th>Duration</th><th>Amount</th><th>Description</th><th></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
</div>

