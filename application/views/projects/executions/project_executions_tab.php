<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/30/2018
 * Time: 10:42 AM
 */
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools">
                            <form class="form-inline" method="post" target="_blank" action="<?= base_url('projects/preview_project_plans_execution/'.$project->{$project::DB_TABLE_PK} ) ?>">
                                <div class="form-group pull-left">
                                    <label for="from">From:  </label>
                                    <input type="text" class="form-control datepicker" name="from">
                                </div>
                                <div class="form-group pull-left">
                                    <label for="to">To:  </label>
                                    <input type="text" class="form-control datepicker" name="to">
                                </div>
                                <button class="btn btn-default btn-sm pull-left">
                                    <i class="fa fa-file-pdf-o"></i> PDF
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="project_executions_table"  project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover table-striped" >
                                <thead>
                                <tr>
                                    <th>Tittle</th><th>Start Date</th><th>End Date</th><th>Plan Execution Cost</th><th></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
