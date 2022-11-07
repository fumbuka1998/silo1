<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/13/2016
 * Time: 12:49 PM
 */
?>
<div class="box">
    <div class="box-header">
        <div class="col-xs-12">
            <div class="box-tools pull-right">

            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table orders_for="location" id="location_purchase_orders_table" class="table table-bordered table-hover" location_id="<?= $location->{$location::DB_TABLE_PK} ?>">
                    <thead>
                        <tr>
                            <th>Date</th><th>Order No.</th><th>Vendor</th><th style="width: 200px !important;">Project</th><th>P.O Value</th><th>Received Value</th><th>Balance</th><th>Status</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
