
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="box-tools pull-right">
                        <button data-toggle="modal" data-target="#new_group_form" class="btn btn-default btn-xs">
                            <i class="fa fa-plus"></i> New
                        </button>
                        <div id="new_group_form" class="modal fade" role="dialog">
                            <?php $this->load->view('assets/settings/asset_group/asset_group_form');?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <table id="asset_group_lists" class="table table-bordered table-hover asset_group_list">
                            <thead>
                                <tr>
                                    <th>Group Name</th><th>Description</th><th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
