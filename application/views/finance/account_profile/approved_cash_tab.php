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
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table account_id="<?= $account->{$account::DB_TABLE_PK} ?>" id="approved_cash_list" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Date Approved</th><th>Requisition No.</th><th>Amount</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
