<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/11/2016
 * Time: 5:02 PM
 */

$cost_types = ['material','miscellaneous','permanent_labour','equipment','sub_contract','activities','tasks'];
$stakeholder = $project->stakeholder();
$bcws = $project->budgeted_figure_work_scheduled();
$bcwp = $project->budgeted_figure_work_performed();
$actual_cost = $project->actual_cost();
$budget_at_completion = $project->budget_figure_at_completion();
$cpi = $actual_cost != 0 ? ($bcwp / $actual_cost) : 0;
$etc = $cpi != 0 ? ($budget_at_completion - $bcwp) / $cpi : 0;

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="box-tools pull-right">
                        <?php if($project_status != 'closed'){ ?>
                        <button data-toggle="modal" data-target="#edit_form"
                                class="btn btn-default btn-xs">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <div id="edit_form" class="modal fade" role="dialog">
                            <?php $this->load->view('projects/project_form'); ?>
                        </div>
                        <button data-toggle="modal" data-target="#project_closure_form_<?= $project->{$project::DB_TABLE_PK}?>"
                                class="btn btn-default btn-xs">
                            <i class="fa fa-close"></i> Close
                        </button>
                        <div id="project_closure_form_<?= $project->{$project::DB_TABLE_PK}?>" class="modal fade" role="dialog">
                            <?php $this->load->view('projects/project_closure_form',['project'=>$project]); ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-horizontal">
                    <div class="form-group col-md-4 col-sm-6">
                        <label  class="col-sm-4 control-label">Name:</label>
                        <div class="form-control-static col-sm-8">
                            <?= $project->project_name ?>
                        </div>
                    </div>
                    <div class="form-group col-md-4 col-sm-6">
                        <label  class="col-sm-4 control-label">Category:</label>
                        <div class="form-control-static col-sm-8">
                            <?= $project->category()->category_name ?>
                        </div>
                    </div>
                    <div class="form-group col-md-4 col-sm-6">
                        <label  class="col-sm-4 control-label">Client:</label>
                        <div class="form-control-static col-sm-8">
                            <?= !empty($stakeholder) ? (check_permission('Stakeholders') ? anchor(base_url('stakeholders/stakeholder_profile/'.$stakeholder->{$stakeholder::DB_TABLE_PK}),$stakeholder->stakeholder_name) : $stakeholder->stakeholder_name) : 'N/A'; ?>
                        </div>
                    </div>
                    <div class="form-group col-md-4 col-sm-6">
                        <label  class="col-sm-4 control-label">Reference No:</label>
                        <div class="form-control-static col-sm-8">
                            <?= $project->reference_number ? $project->reference_number : 'N/A' ?>
                        </div>
                    </div>
                    <div class="form-group col-md-4 col-sm-6">
                        <label  class="col-sm-4 control-label">Start Date:</label>
                        <div class="form-control-static col-sm-8">
                            <?= $project->start_date != '' ? custom_standard_date($project->start_date) : 'N/A' ?>
                        </div>
                    </div>
                    <div class="form-group col-md-4 col-sm-6">
                        <label  class="col-sm-4 control-label">End Date:</label>
                        <div class="form-control-static col-sm-8">
                            <?= $project->completion_date() != '' ? custom_standard_date($project->completion_date()) : 'N/A' ?>
                        </div>
                    </div>
                    <div class="form-group col-md-4 col-sm-6">
                        <label  class="col-sm-4 control-label">Contract Sum:</label>
                        <div class="form-control-static col-sm-8">
                            <?= number_format($project->contract_sum(),2) ?>
                        </div>
                    </div>
                    <div class="form-group col-md-4 col-sm-6">
                        <label  class="col-sm-4 control-label">Project ID:</label>
                        <div class="form-control-static col-sm-8">
                            <?= $project->generated_project_id() ?>
                        </div>
                    </div>
                </div>
                <div>
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                        <th>BCWS</th>
                        <th>BCWP</th>
                        <th>ACWP</th>
                        <th>CPI</th>
                        <th>CV</th>
                        <th>SPI</th>
                        <th>SV</th>
                        <th>BAC</th>
                        <th>ETC</th>
                        <th>EAC</th>
                        </thead>
                        <tbody>
                        <td><?= number_format($bcws, 2) ?></td>
                        <td><?= number_format($bcwp, 2) ?></td>
                        <td><?= number_format($actual_cost, 2) ?></td>
                        <td><?= round($cpi,2) ?></td>
                        <td><?= number_format(($bcwp - $actual_cost), 2) ?></td>
                        <td><?= $bcws != 0 ? round(($bcwp / $bcws), 2) : 0 ?></td>
                        <td><?= number_format(($bcwp - $bcws), 2) ?></td>
                        <td><?= number_format($budget_at_completion, 2) ?></td>
                        <td><?= number_format($etc,2) ?></td>
                        <td><?= $etc != 0 ? number_format(($actual_cost + $etc),2) : number_format(0,2) ?></td>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    <?php
                    If($actual_cost!=0 && $bcws!=0 && $bcwp!=0) {
                        if ($bcwp / $actual_cost == 1) {
                            $status = 'On Budget';
                            ?>
                            <span style="font-size: 14px"  class="label label-success"><?= $status ?></span>
                            <?php
                        } else if ($bcwp / $actual_cost < 1) {
                            $status = 'Over Budget';
                            ?>
                            <span style="font-size: 14px"  class="label label-danger blink"><?= $status ?></span>
                            <?php
                        } else {
                            $status = 'Under Budget';
                            ?>
                            <span style="font-size: 14px" class="label label-info"><?= $status ?></span>
                            <?php
                        }
                    }

                    if($bcws!=0) {
                        if ($bcwp / $bcws == 1) {
                            $status = 'On Schedule';
                            ?>
                            <span style="font-size: 14px"  class="label label-info"><?= $status ?></span>
                            <?php
                        } else if ($bcwp / $bcws < 1) {
                            $status = 'Behind Schedule';
                            ?>
                            <span style="font-size: 14px"  class="label label-danger blink"><?= $status ?></span>
                            <?php
                        } else {
                            $status = 'Ahead Schedule';
                            ?>
                            <span style="font-size: 14px"  class="label label-success"><?= $status ?></span>
                            <?php
                        }
                    }
                    ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <hr/>
                        <?php $this->load->view('projects/project_details/project_summary'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
