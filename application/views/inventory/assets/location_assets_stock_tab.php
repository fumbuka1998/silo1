<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 29/03/2018
 * Time: 16:50
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
                <table location_id="<?= $location->{$location::DB_TABLE_PK} ?>" id="location_assets_stock_table" class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Asset Name</th><th>Asset Code</th><th>Received Date</th><th>Status</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

