<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/23/2018
 * Time: 12:57 PM
 */

    if(isset($project)) {
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
                    <div class="box-header with-border bg-gray-light">
                        <p style="text-align: center; font-weight: bold; font-size: 16px"><?= anchor(base_url('projects/profile/'.$project->{$project::DB_TABLE_PK}),$project->project_name) ?></p>
                    </div>
                    <div class="box-body">
                        <div class="form-horizontal">
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="col-sm-4 control-label">Category:</label>
                                <div class="form-control-static col-sm-8">
                                    <?= $project->category()->category_name ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="col-sm-4 control-label">Client:</label>
                                <div class="form-control-static col-sm-8">
                                    <?= !empty($stakeholder) ? (check_permission('Stakeholders') ? anchor(base_url('stakeholders/profile/' . $stakeholder->{$stakeholder::DB_TABLE_PK}), $stakeholder->stakeholder_name) : $stakeholder->stakeholder_name) : 'N/A'; ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="col-sm-4 control-label">Site Location:</label>
                                <div class="form-control-static col-sm-8">
                                    <?= $project->site_location ? $project->site_location : 'N/A' ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="col-sm-4 control-label">Duration:</label>
                                <div class="form-control-static col-sm-8">
                                    <?= $project->duration() . '&nbsp;Days' ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="col-sm-4 control-label">Reference No:</label>
                                <div class="form-control-static col-sm-8">
                                    <?= $project->reference_number ? $project->reference_number : 'N/A' ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="col-sm-4 control-label">Start Date:</label>
                                <div class="form-control-static col-sm-8">
                                    <?= custom_standard_date($project->start_date) ?>
                                </div>
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label class="col-sm-4 control-label">End Date:</label>
                                <div class="form-control-static col-sm-8">
                                    <?= custom_standard_date($project->completion_date()) ?>
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

        <?php
    } else {
        ?>
        <div style="text-align: center" class=" alert alert-info">Please select a project</div>
<?php
    }
