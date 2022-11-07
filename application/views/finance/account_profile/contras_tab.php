<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/17/2016
 * Time: 8:44 AM
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_contra" class="btn btn-default btn-xs">
                    New Contra
                </button>
                <div id="new_contra" class="modal fade" role="dialog">
                    <?php $this->load->view('finance/account_profile/contra_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table account_id="<?= $account->{$account::DB_TABLE_PK} ?>" id="account_contras_list" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Contra Date</th><th>Contra No</th><th>Reference</th><th>Action</th><th>Supplementary Account(s)</th><th>Amount</th><th>Remarks</th><th>Datetime Posted</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
