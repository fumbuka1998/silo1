<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/23/2018
 * Time: 11:27 AM
 */

$this->load->view('includes/header');

?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Projects Overview
            <!--<small>Preview page</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('projects/projects_overview') ?>"><i class="fa fa-hourglass"></i>Project Overview</a></li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#on_going_projects_tab" data-toggle="tab">On Going Projects</a></li>
                    <li><a href="#completed_projects_tab" data-toggle="tab">Completed</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="on_going_projects_tab">
                        <?php $this->load->view('projects/projects_overview/on_going_projects_tab'); ?>
                    </div>
                    <div class="tab-pane" id="completed_projects_tab">
                        <?php $this->load->view('projects/projects_overview/completed_projects_tab'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');