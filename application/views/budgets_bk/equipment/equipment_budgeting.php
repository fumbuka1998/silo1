
<div class="row">
   <div class="col-xs-12">
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
                        <button data-toggle="modal" data-target="#new_equipment_budget_item"
                                class="btn btn-xs btn-default">
                            <i class="fa fa-plus"></i> Add Equipment
                        </button>
                        <div id="new_equipment_budget_item" class="modal fade equipment_budget_form" role="dialog">
                            <?php $this->load->view('budgets/equipment/equipment_budget_form'); ?>
                        </div>
                    </div>
                </div>
            </div>
          <div class="box-body">
                <div class="col-xs-12 table-responsive">
                    <table project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover equipment_budget_items" cost_center_level="project" cost_center_id="<?= $project->{$project::DB_TABLE_PK} ?>">
                        <thead>
                        <tr>
                            <th>Equipment Type</th><th>Ownership</th><th>Days</th><th>Quantity</th><th>Rate</th><th>Amount</th><th>Description</th><th></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th colspan="5">Total</th><th id="total_budget_amount_display" style="text-align: right"></th><th colspan="2"></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
   </div>
</div>
