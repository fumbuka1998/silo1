<?php

/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/15/2016
 * Time: 1:05 AM
 */
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="box-tools pull-right">
                        <input id="sub_location_keyword" type="text" placeholder="Search..">

                        <?php if (check_privilege('Inventory Actions')) { ?>
                            <button data-toggle="modal" data-target="#edit_location" class="btn btn-default btn-xs">
                                <i class="fa fa-edit"></i> Edit Location
                            </button>
                            <div id="edit_location" class="modal fade" tabindex="-1" role="dialog">
                                <?php $this->load->view('inventory/locations/location_form'); ?>
                            </div>

                            <button data-toggle="modal" data-target="#new_sub_location" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> New Sub-Location
                            </button>
                            <div id="new_sub_location" class="modal fade sub_location_form" role="dialog">
                                <?php $this->load->view('inventory/locations/sub_location_form'); ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row sub_locations_container">
                    <div class="col-xs-12" id="sub_locations_container" location_id="<?= $location->{$location::DB_TABLE_PK} ?>">
                        <?php $this->load->view('inventory/locations/sub_locations_list', ['sub_locations' => $location->sub_locations()]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>