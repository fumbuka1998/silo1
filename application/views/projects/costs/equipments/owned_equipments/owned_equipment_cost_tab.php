
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
                        <button data-toggle="modal" data-target="#new_owned_equipment_cost_item"
                                class="btn btn-xs btn-default">
                            <i class="fa fa-plus-circle"></i>&nbsp; New Owned Equipment Cost
                        </button>
                        <div id="new_owned_equipment_cost_item" class="modal fade owned_equipment_cost_form" role="dialog">
                            <?php $this->load->view('projects/costs/equipments/owned_equipments/owned_equipment_cost_form'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="col-xs-12 table-responsive">
                    <table project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover owned_equipment_cost_tab">
                        <thead>
                        <tr>
                            <th>Equipment Name</th>
                            <th>Task</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Rate mode</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Added on</th>
                            <th>Added by</th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th colspan="6">Total</th>
                            <th id="total_cost_amount_display" style="text-align: right"></th>
                            <th ></th>
                            <th ></th>
                            <th></th>
                            <th></th>

                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
