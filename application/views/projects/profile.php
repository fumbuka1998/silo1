<?php

$this->load->view('includes/header');
$project_id = $project->{$project::DB_TABLE_PK};
$project_name = substr($project->project_name,0,30);

?>
<!-- Content Header (Page header) -->
<section class="content-header">

    <h1>
        <?= $project->project_name ?>
<!--<small>Preview page</small>-->
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#project_details" project_id="<?= $project_id ?>" data-toggle="tab">Project Details</a></li>
                    <li><a href="#project_activities" data-toggle="tab">Activities</a></li>
                    <?php if($project->manager_access() || check_privilege('Budgets')){ ?>
                        <li><a href="#project_budgets" data-toggle="tab">Budget</a></li>
                    <?php }
                    if($project->manager_access() || check_privilege('Planning')){
                        ?>
                        <li><a href="#project_planning" data-toggle="tab">Planning</a></li>
                        <?php
                    } ?>
                    <li><a href="#project_executions" data-toggle="tab">Execution</a></li>
                    <li><a href="#project_requisitions" data-toggle="tab"> Requisitions</a></li>
                    <li><a href="#project_costs" data-toggle="tab">Costs</a></li>
                    <?php if($project->manager_access() || check_privilege('Finance')){ ?>
                        <li><a href="#project_finance" project_id="<?= $project_id ?>" data-toggle="tab">Finance</a></li>
                    <?php } if($project->manager_access() || check_privilege('Project Team')){ ?>
                        <li><a href="#project_team" data-toggle="tab">Project Team</a></li>
                    <?php } ?>
                    <li><a href="#project_store" project_id="<?= $project_id ?>" data-toggle="tab">Store</a></li>
                    <?php  if($project->manager_access() || check_privilege('Sub Contracts')){ ?>
                        <li><a href="#project_sub_contracts" data-toggle="tab">Sub-Contracts</a></li>
                    <?php }  if($project->manager_access() || check_privilege('Contract Reviews')){ ?>
                        <li><a href="#project_contract_reviews" project_id="<?= $project_id ?>" data-toggle="tab">Contract Reviews</a></li>
                    <?php }  if($project->manager_access() ||  check_permission('Projects')){ ?>
                        <li><a href="#project_wall_posts" project_id="<?= $project_id ?>" data-toggle="tab">Project Wallpost</a></li>
                    <?php }  if($project->manager_access() || check_privilege('Certificates')){ ?>
                        <li><a href="#project_certificates" project_id="<?= $project_id ?>" data-toggle="tab">Certificates</a></li>
                    <?php } ?>
                   <!--<li><a href="#project_gantt_chart" project_id="<?/*= $project_id */?>" data-toggle="tab">Gantt Chart</a></li>-->
                    <li><a href="#project_attachments" project_id="<?= $project_id ?>" data-toggle="tab">Attachments</a></li>
                    <?php if(check_privilege('Project Reports')){ ?>
                    <li><a href="#project_reports" data-toggle="tab">Project Reports</a></li>
                    <?php } ?>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="project_details">
                        <?php $this->load->view('projects/project_details/project_details_tab'); ?>
                    </div>
                    <?php if($project->manager_access() || check_privilege('Budgets')){ ?>
                    <div class="tab-pane" id="project_budgets">
                        <?php $this->load->view('projects/budgets/new_budgeting_tab'); ?>
                    </div>
                    <?php }
                        if($project->manager_access() || check_privilege('Planning')){
                    ?>
                    <div class="tab-pane" id="project_planning">
                        <?php $this->load->view('projects/plans/project_plans_tab'); ?>
                    </div>
                    <?php } ?>
                    <div class="tab-pane" id="project_executions">
                        <?php $this->load->view('projects/executions/project_executions_tab'); ?>
                    </div>
                    <div class="tab-pane" id="project_requisitions">
                        <?php $this->load->view('requisitions/requisitions_list/project_requisitions_tab'); ?>
                    </div>
                    <div class="tab-pane" id="project_costs">
                        <?php $this->load->view('projects/costs/costs_tab'); ?>
                    </div>
                    <div class="tab-pane" id="project_activities">
                        <?php $this->load->view('projects/activities/activities_tab'); ?>
                    </div>
                    <div class="tab-pane" id="project_store">
                    </div>

                    <?php  if($project->manager_access() || check_privilege('Sub Contracts')){ ?>
                    <div class="tab-pane" id="project_sub_contracts">
                        <?php $this->load->view('projects/sub_contracts/project_sub_contracts_tab'); ?>
                    </div>
                    <?php } if($project->manager_access() || check_privilege('Project Team')) { ?>
                    <div class="tab-pane" id="project_team">
                        <?php $this->load->view('projects/project_details/project_team_tab'); ?>
                    </div>
                    <?php } if($project->manager_access() || check_privilege('Contract Reviews')){ ?>
                    <div class="tab-pane" id="project_contract_reviews">
                        <?php $this->load->view('projects/contract_reviews/contract_reviews_tab'); ?>
                    </div>
                    <?php } if($project->manager_access() || check_permission('Projects')){ ?>
                    <div class="tab-pane" id="project_wall_posts">
                        <?php $this->load->view('projects/wallposts/project_wall_posts_tab'); ?>
                    </div>
                    <?php } if($project->manager_access() || check_privilege('Certificates')){ ?>
                    <div class="tab-pane" id="project_certificates">
                        <?php $this->load->view('projects/certificates/certificates_tab'); ?>
                    </div>
                    <?php } ?>
                    <div class="tab-pane" id="project_attachments">
                        <?php $this->load->view('projects/attachments/attachments_tab'); ?>
                    </div>
                    <?php if($project->manager_access() || check_privilege('Finance')){  ?>
                    <div class="tab-pane" id="project_finance">
                        <?php
                            if(!empty($project_accounts)){
                                $this->load->view('projects/finance/index');
                            } else {
                                echo "<div class='alert alert-info'>This project doesn't have a cash book account attached to it</div>";
                            }
                        ?>
                    </div>
                    <?php } ?>
                    <div class="tab-pane" id="project_gantt_chart">
                        <div class="row">
                            <div class="col-xs-12">
                                <div id="ganttChart" style="padding:0px; overflow-y:auto; overflow-x:hidden;border:1px solid #e5e5e5; position:relative;">

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if(check_privilege('Project Reports')){ ?>
                    <div class="tab-pane" id="project_reports">
                        <?php $this->load->view('projects/reports/project_reports_tab'); ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');