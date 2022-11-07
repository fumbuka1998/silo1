<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/22/2016
 * Time: 2:17 AM
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <button data-toggle="modal" data-target="#new_job_position" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Position
                </button>
                <div id="new_job_position" class="modal fade" tabindex="-1" role="dialog">
                    <?php $this->load->view('human_resources/settings/job_position_form'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="job_positions_list" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Position Name</th><th>Description</th><th>No. of Employees</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
