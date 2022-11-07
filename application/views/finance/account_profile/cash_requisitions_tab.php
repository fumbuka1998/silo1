<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/12/2017
 * Time: 8:20 AM
 */

?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_cash_requisition" class="btn btn-default btn-xs">
                    New Cash Requisition
                </button>
                <div id="new_cash_requisition" class="modal fade cash_requisition_form" role="dialog">
                    <?php $this->load->view('finance/account_profile/cash_requisition_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table account_id="<?= $account->{$account::DB_TABLE_PK} ?>" id="account_cash_requisitions_list" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Date Requested</th><th>Date Approved</th><th>Requisition No.</th><th>Amount</th><th>Status</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
