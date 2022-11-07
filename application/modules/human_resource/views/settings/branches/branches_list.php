<?php // branches list ?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_branch" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New branch
                </button>
                <div id="new_branch" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('branch_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="branches_list_table" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Branch Name</th><th>Created at</th><th>Created by</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>