<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/28/2016
 * Time: 11:04 AM
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
                <table class="table table-bordered table-hover location_equipment_stock" location_id="<?= $location->{$location::DB_TABLE_PK} ?>">
                    <thead>
                    <tr>
                        <th>Thumbnail</th><th>Type</th><th>Make</th><th>Model</th><th>Identification No</th><th>Value</th>
                        <th>Vendor</th><th>Ownership</th><th>Description</th><th>Date Enrolled</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
