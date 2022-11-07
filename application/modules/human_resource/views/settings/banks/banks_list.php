<?php // banks list ?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_bank" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Bank
                </button>
                <div id="new_bank" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('bank_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="banks_list_table" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Bank Name</th><th>Description</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>