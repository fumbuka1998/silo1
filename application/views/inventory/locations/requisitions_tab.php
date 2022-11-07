<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/7/2016
 * Time: 11:15 AM
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">
            <div class="box-tools pull-right">
                <?php if(!isset($project) || (isset($project) && ($project->manager_access() || $project->team_member_access()))){ ?>
                <button data-toggle="modal" data-target="#requisition_form" class="btn btn-default btn-xs">
                    <i class="fa fa-plus"></i> New Requisition
                </button>
                <div id="requisition_form" class="modal fade  requisition_form" role="dialog">
                    <?php
                        $this->load->view('inventory/requisitions/requisition_form');
                    ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table id="location_requisitions_table" class="table table-bordered table-hover" location_id="<?= $location->{$location::DB_TABLE_PK} ?>">
                    <thead>
                        <tr>
                            <th>Request Date</th><th>Requisition No.</th><th>Location Name</th><th>Required Date</th><th>Status</th><th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
