<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/13/2016
 * Time: 2:25 PM
 */


if(!empty($activities)){
    foreach($activities as $activity){
        $activity_id = $activity->{$activity::DB_TABLE_PK};
        $data['activity'] = $activity;
        ?>
        <div class="box collapsed-box">
            <div class="box-header with-border bg-gray-light">
                <h3 class="box-title collapse-title"  data-widget="collapse"><?= $activity->activity_name ?></h3>
                <div class="box-tools pull-right">
                    <?php if(($project->manager_access() || check_privilege('Project Actions')) && $project_status != 'closed'){ ?>
                    <button data-toggle="modal" data-target="#edit_activity_<?= $activity_id ?>"
                            class="btn btn-xs btn-default">
                        <i class="fa fa-edit"></i> Edit
                    </button>
                    <div id="edit_activity_<?= $activity_id ?>" class="modal fade" tabindex="-1"
                         role="dialog">
                        <?php $this->load->view('projects/activities/activity_form',['activity' => $activity]); ?>
                    </div>
                    <button class="btn btn-danger btn-xs delete_activity" activity_id="<?= $activity_id ?>">
                        <i class="fa fa-trash-o"></i> Delete
                    </button>
                    <?php } ?>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a class="activity_summary_activator" href="#activity_summary_<?= $activity_id ?>" data-toggle="tab">Summary</a></li>
                                <li><a class="activity_tasks_activator" href="#activity_tasks_<?= $activity_id ?>" data-toggle="tab">Tasks</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="active tab-pane budget_activity_summary_container" activity_id="<?= $activity_id ?>"
                                     id="activity_summary_<?= $activity_id ?>">
                                    <?php
                                        $this->load->view('projects/activities/activity_summary_tab',$data);
                                    ?>
                                </div>
                                <div class="tab-pane" id="activity_tasks_<?= $activity_id ?>">
                                    <?php
                                        $this->load->view('projects/activities/activity_tasks_tab', $data);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.box-body -->
        </div>
        <?php
    }
} else {
    ?>
    <div class="alert alert-info">No activities found for this project</div>
    <?php
}
?>

