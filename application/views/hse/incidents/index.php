<?php
?>
<?php $this->load->view('includes/header'); ?>

    <section class="content-header">
        <h1>
            HSE
            <small>Incidents</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li class="active">Incidents</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#incident_form_form" class="btn btn-default btn-xs">
                                <i class="fa fa-plus"></i> New Incident
                            </button>
                            <div id="incident_form_form" class="modal fade" role="dialog">
                                <?php $this->load->view('hse/incidents/incident_form');?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="hse_incidents_list" class="table table-bordered table-hover" style="table-layout: fixed;">
                                <thead>
                                <tr>
                                    <th style="width: 8%">Date</th><th style="width: 30%">Site</th><th>Type</th><th>Causative Agent</th><th>Reference</th><th style="width: 18%"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');
