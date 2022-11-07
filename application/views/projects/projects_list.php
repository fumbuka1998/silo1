<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= !is_null($project_category_id) ? ucwords($project_category_name) : '' ?> PROJECTS
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('projects')?>"><i class="fa fa-product-hunt"></i>Projects</a></li>
        <li class="active"><?= !is_null($project_category_id) ? ucwords($project_category_name) : '' ?> PROJECTS</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools  pull-right">
                            <?php if(check_privilege('Project Actions')){ ?>
                            <button data-toggle="modal" data-target="#project_form" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> New Project
                            </button>
                            <div id="project_form" class="modal fade" role="dialog">
                                <?php $this->load->view('projects/project_form'); ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover table-striped" category_id="<?= isset($project_category_id) ? $project_category_id : '' ?>" id="projects_list">
                            <thead>
                                <tr>
                                    <th>Project Name</th><th>Category</th><th>Reference No.</th><th>Client</th><th>Start Date</th><th>End Date</th><th>Site Location</th><th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');