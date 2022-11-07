
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
                <button data-toggle="modal" data-target="#new_casual_labour_budget" class="btn btn-xs btn-default">
                    <i class="fa fa-plus"></i> Add Labour
                </button>
               <div id="new_casual_labour_budget" class="modal fade casual_labour_budget_form" role="dialog">
                    <?php
                        $data['cost_center_level'] = 'project';
                       $data['cost_center_id'] = $project->{$project::DB_TABLE_PK};
                       $this->load->view('budgets/labour/casual_labour/casual_labour_budget_form',$data);
                    ?>

                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-bordered table-hover casual_labour_budget_items" project_id="<?= $project->{$project::DB_TABLE_PK} ?>">
                    <thead>
                        <tr>
                              <th>Labour Type</th>
                              <th>Mode</th>
                              <th>Duration</th>
                              <th>Rate</th>
                              <th>No of Workers</th>
                              <th>Amount</th>
                              <th>Description</th>
                              <th></th>
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
