

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_hif" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New hif
                </button>
                <div id="new_hif" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('hif_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="hifs_list_table" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>HIF Name</th><th>Created at</th><th>Created by</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>