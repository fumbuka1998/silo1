<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 26/10/2018
 * Time: 08:23
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
            <div class="col-xs-12 table-responsive">
                <table vendor_id="<?= $vendor->{$vendor::DB_TABLE_PK} ?>" class="table table-bordered table-hover vendor_invoices_list">
                    <thead>
                        <tr>
                            <th>Invoice Date</th><th>Invoice Correspondence No.</th><th>Reference</th><th style="width: 200px !important;">Amount</th><th style="width: 200px !important;">Paid Amount</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>