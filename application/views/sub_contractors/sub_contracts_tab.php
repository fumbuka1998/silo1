<?php

?>
<div class="box-header with-border" style="display: none">
    <div class="col-xs-12">
        <div class="box-tools pull-right">
            <button data-toggle="modal" data-target="#sub_contract" class="btn btn-default btn-xs">
                <i class="fa fa-plus-circle"></i> New Sub-Contract
            </button>
            <div id="sub_contract" class="modal fade" tabindex="-1" role="dialog">
                <?php //$this->load->view('sub_contractors/sub_contract_form'); ?>
            </div>
        </div>
    </div>
</div>
<div class="box-body">
    <div class="row">
        <div class="col-xs-12 table-responsive">
            <table class="table table-bordered table-hover table-striped" sub_contractor_id="<?= $sub_contractor->id ?>" id="sub_contracts_list">
                <thead>
                <tr>
                    <th>Project Name</th><th>Contract Name</th><th>Contract Date</th><th>Added On</th><th>Created by</th><th>Descriptions</th>
<!--                    <th>Actions</th>-->

                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>