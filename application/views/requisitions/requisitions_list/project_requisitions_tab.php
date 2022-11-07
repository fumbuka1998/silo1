<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 8/14/2017
 * Time: 10:55 AM
 */

?>
<div class="box">
    <div class="box-header">
        <div class="col-xs-12">
            <div class="form-group col-md-2">
                <label for="" class="control-label">Status</label>
                <?= form_dropdown('status', [
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'incomplete' => 'Incomplete',
                    'rejected' => 'Rejected',
                    'all' => 'All'
                ],'',' class="form-control" ') ?>
            </div>
            <div class="box-tools pull-right">

                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
                        <i class="fa fa-plus"></i>Requisitions
                    </button>
                    <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a data-toggle="modal" data-target="#new_requisition" href="#">Requisition</a></li>
                        <li><a data-toggle="modal" data-target="#sub_contractor_payment_requisition_form" href="#">Sub Cont. Payment</a></li>
                    </ul>
                </div>
                <div id="new_requisition" class="modal fade requisition_form" role="dialog">
                    <?php $this->load->view('requisitions/requisitions_list/requisition_form'); ?>
                </div>
                <div id="sub_contractor_payment_requisition_form" class="modal fade sub_contract_payment_requisition_form out_table" role="dialog">
                    <?php $this->load->view('requisitions/requisitions_list/sub_contract_payment_requisition_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table project_id="<?= $project->{$project::DB_TABLE_PK} ?>" id="project_requisitions_table" class="table table-bordered requisitions_table table-hover">
                    <thead>
                    <tr>
                        <th>Request Date</th><th>Requisition No.</th><th>Required Date</th><th>Requested Amount</th><th>Status</th><th style="width: 150px"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
