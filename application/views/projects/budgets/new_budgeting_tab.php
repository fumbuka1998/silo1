<?php
/**
 * Created by PhpStorm.
 * User: bizytechlimited
 * Date: 24/10/2018
 * Time: 08:59
 */
?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools">
                            <div class="form-group col-md-2">
                                <label for="" class="control-label">Activity</label>
                                <?= form_dropdown('activity_id', $activities_options,'','style = "width: 300px" class="form-control" ') ?>
                            </div>
							<div class="form-group pull-right">
								<form class="form-inline">
									<?php if($project_status != 'closed'){ ?>
										<div class="form-group pull-right">
											<a target="_blank" class="btn btn-xs btn-default" href="<?= base_url('budgets/download_excel_material_budget_template/'.$project->{$project::DB_TABLE_PK}) ?>">Download Excel Template</a>
											&nbsp; &nbsp;&nbsp; &nbsp;
											<label for="activities_excel">Excel File:  </label>
											<input type="file" name="activities_excel" project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="form-control">
											<button type="button" excel_type="budget" class="btn btn-default btn-sm upload_project_excel">Upload Excel</button>
										</div>
									<?php } ?>
								</form>
							</div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <table id="project_tasks_budget_table" project_id = "<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-hover table-bordered">
                                <thead>
                                    <th style="width: 40%">Task Name</th><th>Start Date</th><th>End Date</th><th>Quantity</th><th>Contract Sum</th><th>Budget</th><th style="text-align: left"></th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

