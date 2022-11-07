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

                        <button data-toggle="modal"
                                data-target="#material_disposal_<?= $location->{$location::DB_TABLE_PK} ?>"
                                class="btn btn-default btn-xs">
                            <i class="fa fa-trash"></i> Material Disposal
                        </button>
                        <div id="material_disposal_<?= $location->{$location::DB_TABLE_PK} ?>"
                             class="modal fade material_disposal_form" role="dialog">
                            <?php $this->load->view('inventory/locations/disposals/material_disposal_form');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="location_material_disposal" location_id="<?= $location->{$location::DB_TABLE_PK} ?>">
                            <thead>
                            <tr>
                                <th>Disposal Date</th><th> Location</th><th>Disposed by</th><th></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
