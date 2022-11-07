<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/30/2018
 * Time: 10:41 AM
 */
?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        
                        <?php if (check_privilege('Planning')) {
                            ?>
                            <div class="box-tools">
                                <form class="form-inline" method="post" target="_blank" action="<?= base_url('projects/preview_project_plans/'.$project->{$project::DB_TABLE_PK} ) ?>">
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
                                <div class="pull-right">
                                    <button data-toggle="modal" data-target="#project_plan" class="btn btn-default btn-xs  pull-right">
                                        <i class="fa fa-plus-circle"></i> New Plan
                                    </button>
                                </div>
                                <div id="project_plan" class="modal fade" role="dialog">
                                    <?php $this->load->view('projects/plans/project_plan_form'); ?>
                                </div>
                            </div>
                            <?php
                        } ?>
                        

                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="project_plans_table"  project_id="<?= $project->{$project::DB_TABLE_PK} ?>" class="table table-bordered table-hover table-striped" >
                                <thead>
                                <tr>
                                    <th>Tittle</th><th>Start Date</th><th>End Date</th><th>Plan Budget</th><th style="width: 15%"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

