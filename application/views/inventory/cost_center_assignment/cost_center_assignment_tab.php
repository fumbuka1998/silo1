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
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
                                <i class="fa fa-plus"></i> Assignment
                            </button>
                            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a data-toggle="modal" data-target="#material_cost_center_assignment_<?= $location->{$location::DB_TABLE_PK} ?>" href="#">
                                        <i class="fa fa-cubes"></i> Material
                                    </a>
                                </li>
                                <li>
                                    <a data-toggle="modal" data-target="#asset_cost_center_assignment_<?= $location->{$location::DB_TABLE_PK} ?>" href="#">
                                        <i class="fa fa-wrench"></i> Asset
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div id="material_cost_center_assignment_<?= $location->{$location::DB_TABLE_PK} ?>" class="modal fade material_cost_center_assignment_form " role="dialog">
                            <?php $this->load->view('inventory/cost_center_assignment/material/material_cost_center_assignment_form'); ?>
                        </div>

                        <div id="asset_cost_center_assignment_<?= $location->{$location::DB_TABLE_PK} ?>" class="modal fade asset_cost_center_assignment_form " role="dialog">
                            <?php $this->load->view('inventory/cost_center_assignment/assets/asset_cost_center_assignment_form'); ?>
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
                                <th>Assignment Date</th><th>MCA No</th><th>Source Project </th><th>Destination Project</th><th>Assigned By</th><th></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>