<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 12/05/2018
 * Time: 09:28
 */
?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_project_payment" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> Payment
                </button>
                <div id="new_project_payment" class="modal fade expense_payment_form" role="dialog">
                    <?php $this->load->view('finance/payments/expense_payment_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="project_payments_table" project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover payments_table">
                    <thead>
                    <tr>
                        <th>Payment No.</th>
                        <th>Payment Date</th>
                        <th>Reference</th>
                        <th>Credit Account</th>
                        <th>Amount</th>
                        <th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
