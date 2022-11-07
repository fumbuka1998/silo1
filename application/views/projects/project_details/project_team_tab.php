<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/11/2016
 * Time: 6:42 PM
 */
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="box-tools pull-right">
                        <?php
                        if($project->created_by == $this->session->userdata('employee_id') || $project->manager_access() ||  check_permission('Administrative Actions')) {
                            ?>
                            <button data-toggle="modal" data-target="#new_project_team_member" class="btn btn-xs btn-default">
                                New Team Member
                            </button>
                            <div id="new_project_team_member" class="modal fade" role="dialog">
                                <?php $this->load->view('projects/project_details/project_team_member_form'); ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class=" col-xs-12 table-responsive"></div>
                <table id="project_team_members_table" project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Employee Name</th><th>Title</th><th>Manager</th><th>Assignor</th><th>Date Assigned</th><th>Remarks</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
