<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/13/2016
 * Time: 12:16 PM
 */
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="box-tools">
                        <?php
                        if($project_status != 'closed' && ($project->manager_access() || check_permission('Administrative Actions'))){
                            ?>
                            <?php if (check_privilege('Project Actions')) {
                                  ?>
                                <div class="col-md-8">
                                    <form class="form-inline">
                                        <a target="_blank" href="<?= base_url('projects/download_activities_excel/'.$project->{$project::DB_TABLE_PK}) ?>" class="btn btn-default btn-sm"><i class="fa fa-file-excel-o"></i> Download Excel Template</a>&nbsp;
                                        <div class="form-group">
                                            <input type="file" name="activities_excel" project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="form-control">
                                        </div>
                                        <button type="button" excel_type="activities" class="btn btn-default btn-sm upload_project_excel">
                                            <i class="fa fa-upload"></i> Upload Excel
                                        </button>
                                    </form>
                                </div>
                                <?php
                            } ?>

                            <div class="search_container form-inline col-md-3 pull-right">
                                <input class="form-control" type="text" id="activity_keyword" placeholder="Search.." project="true">
                                <?php if (check_privilege('Project Actions')) {
                                    ?>
                                    <button data-toggle="modal" data-target="#new_activity"
                                            class="btn btn-default btn-sm">
                                        <i class="fa fa-plus"></i> New Activity
                                    </button>
                                    <?php
                                } ?>
                            </div>


                            <div id="new_activity" class="modal fade" tabindex="-1" role="dialog">
                                <?php $this->load->view('projects/activities/activity_form'); ?>
                            </div>
                                <?php
                            ?>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12" id="activities_container" project_id="<?= $project->{$project::DB_TABLE_PK} ?>">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
