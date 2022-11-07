<?php // ssfs list ?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_ssf" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New SSF
                </button>
                <div id="new_ssf" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('ssf_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="ssfs_list_table" class="table table-bordered table-responsive table-hover">
                    <thead>
                    <tr>
                        <th>S.Security Fund Name</th>
                        <th>Employee %</th>
                        <th>Employer %</th>
                        <th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>