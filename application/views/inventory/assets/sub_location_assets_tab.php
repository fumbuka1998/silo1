<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 08/02/2018
 * Time: 17:22
 */
?>

<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_asset_registration_<?= $sub_location->{$sub_location::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Registration
                </button>
                <div id="new_asset_registration_<?= $sub_location->{$sub_location::DB_TABLE_PK} ?>" class="modal fade new_asset_registration" role="dialog">
                    <?php $this->load->view('inventory/assets/asset_registration_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table sub_location_id="<?= $sub_location->{$sub_location::DB_TABLE_PK} ?>" class="table table-bordered table-striped table-hover sub_location_assets" >
                    <thead>
                    <tr>
                        <th>Asset Name</th>
                        <th>Asset Code</th>
                        <th>Received Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
