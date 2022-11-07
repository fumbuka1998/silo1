<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?= $project->project_name ?> Budget
<!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('budgets') ?>"><i class="fa fa-calculator"></i>Budgets</a></li>
        <li class="active"><?= $project->project_name ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#budget_summary" data-toggle="tab">Budget Summary</a></li>
                    <li><a href="#budgeting_tab" data-toggle="tab">Budgeting</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="budget_summary">
                        <?php $this->load->view('budgets/budget_summary_tab'); ?>
                    </div>
                    <div class="tab-pane" id="budgeting_tab">
                        <?php $this->load->view('budgets/budgeting_tab'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');