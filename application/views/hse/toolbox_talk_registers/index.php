<?php
 $this->load->view('includes/header');
 ?>
    <section class="content-header">
        <h1>
            HSE
            <small>Toolbox Talk Register</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li class="active">Toolbox Tlk Registers</li>
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
                                <button data-toggle="modal" data-target="#toolbox_talk_register_form" class="btn btn-default btn-xs">
                                    <i class="fa fa-plus"></i> Toolbox Talk Register
                                </button>
                                <div id="toolbox_talk_register_form" class="modal fade toolbox_talk_register_form" role="dialog">
                                    <?php $this->load->view('hse/toolbox_talk_registers/toolbox_talk_register_form');?>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="toolbox_talk_registers_list" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th> Date </th><th>Project</th><th>Activity</th><th> Supervisor </th><th style="width: 15%"></th>
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