<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_cost_center" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New
                </button>
                <div id="new_cost_center" class="modal fade" role="dialog">
                    <?php $this->load->view('finance/settings/cost_center_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="cost_centers_list" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Cost Center Name</th><th>Description</th><th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
