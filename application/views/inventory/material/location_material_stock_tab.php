<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/29/2016
 * Time: 12:31 PM
 */
?>
<div class="box">
    <div class="box-body">

        <div class="row">
            <div class="col-xs-12 table-responsive">

                <table id="location_material_stock_table" class="table table-bordered table-hover table-striped" location_id="<?= $location->{$location::DB_TABLE_PK} ?>">
                    <thead>
                    <tr>
                        <th>Thumbnail</th><th>Material Item</th><th>Category</th><th>Unit</th><th>Part Number</th><th>Description</th><th>Available</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
