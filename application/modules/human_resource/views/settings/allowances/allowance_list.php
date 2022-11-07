<?php // banks list ?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_allowance" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Allowance
                </button>
                <div id="new_allowance" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('settings/allowances/allowance_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="allowances_list_table" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                       <th>S/No</th><th>Allowance Name</th><th>Description</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>