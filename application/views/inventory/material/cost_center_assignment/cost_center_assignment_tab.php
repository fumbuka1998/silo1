<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 10/5/2017
 * Time: 9:27 AM
 */


?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="box-tools pull-right">
                        <button data-toggle="modal" data-target="#cost_center_assignment" class="btn btn-default btn-xs">
                            <i class="fa fa-plus"></i> Cost Center Assignment
                        </button>
                        <div id="cost_center_assignment" class="modal fade cost_center_assignment_form " role="dialog">
                            <?php $this->load->view('inventory/material/cost_center_assignment/cost_center_assignment_form'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <table id="material_cost_center_assignment_tab" class="table table-bordered table-hover table-striped" location_id="<?= $location->{$location::DB_TABLE_PK}?>">
                            <thead>
                            <tr>
                                <th>Assignment Date</th><th>MCA No</th><th>Source Project </th><th>Destination Project </th><th>Assigned By</th><th></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>