
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#sub_contract" class="btn btn-default btn-xs">
                                <i class="fa fa-plus-circle"></i> New Sub-Contract
                            </button>
                            <div id="sub_contract" class="modal fade" role="dialog">
                                <?php $this->load->view('projects/sub_contracts/sub_contract_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="sub_contracts_list_table"  project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover table-striped" >
                                <thead>
                                <tr>
                                    <th>Contract Name</th><th>Contract Date</th><th>Sub-contractor</th><th></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
