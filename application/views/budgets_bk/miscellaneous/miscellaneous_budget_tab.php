<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 10/26/2016
 * Time: 9:04 AM
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
                <button data-toggle="modal" data-target="#new_miscellaneous_budget" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Miscellaneous Budget
                </button>
                <div id="new_miscellaneous_budget" class="modal fade miscellaneous_budget_form" role="dialog">
                    <?php $this->load->view('budgets/miscellaneous/miscellaneous_budget_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover miscellaneous_budget_items">
                    <thead>
                        <tr>
                            <th>Budget Type</th><th>Amount</th><th>Description</th><th></th>
                        </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Total</th><th id="total_budget_amount_display" style="text-align: right"></th><th colspan="2"></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
