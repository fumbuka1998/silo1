<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 3/2/2017
 * Time: 9:08 AM
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <form id="client_statement_filter_form" target="_blank" method="post" action="<?= base_url('finance/account_statement/'.$account->{$account::DB_TABLE_PK}) ?>">
                    <input type="hidden" name="account_id" value="<?= $account->{$account::DB_TABLE_PK} ?>">
                    <input type="hidden" name="print" value="true">
                    <input class="datepicker" name="from" placeholder="From" value="<?= date('Y-m-d') ?>">
                    <input class="datepicker" name="to" placeholder="To" value="<?= date('Y-m-d') ?>">
                    <button class="btn btn-xs btn-primary">
                        <i  class="fa fa-file-pdf-o"></i> PDF
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div id="account_statement_table_container" class="col-xs-12 table-responsive">
            
            </div>
        </div>
    </div>
</div>
