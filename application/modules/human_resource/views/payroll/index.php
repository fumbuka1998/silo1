<?php
/**
 * Created by PhpStorm.
 * User: zeus
 * Date: 23/03/2019
 * Time: 14:49
 *
 *
 */

$this->load->view('includes/header');
?>
    <section class="content-header">
        <h1>
            Payroll
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('/human_resource/human_resources') ?>"><i class="fa fa-dashboard"></i>Human Resources</a></li>
            <li class="active">Payroll</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
    <div class="row">
    <div class="col-xs-12">


        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <?php if(check_privilege('Register Employee')){ ?>
                        <li class="active"><a href="#generate_new_payroll" data-toggle="tab">Generate Payroll</a></li>
                    <?php } ?>
                    <li <?php if(!check_privilege('Register Employee')){?>class="active"<?php } ?>><a id="payrolls_button" href="#payrolls" data-toggle="tab">Payrolls</a></li>
                </ul>
                <div class="tab-content">
                    <?php if(check_privilege('Register Employee')){ ?>
                        <div class="active tab-pane" id="generate_new_payroll">

                            <div class="col-xs-12">
                                <div class="box-tools col-xs-12">
                                    <form method="post" target="_blank" action="<?= base_url('human_resource/human_resources/payroll') ?>">
                                        <div class="form-group col-md-4">
                                            <label for="department_id" class="control-label">Department</label>
                                            <?= form_dropdown('department_id', $departments->department_options(),'',' id="department_id" class="form-control searchable" required ') ?>
                                            <input name="print" value="true" type="hidden">
                                        </div>

                                        <div class="container col-md-2">
                                            <label for="payroll_date" class="control-label">Month</label>
                                            <input id="payroll_date" type="text" class="form-control datepicker" required name="payroll_date" value="<?= date('Y-m-d') ?>" placeholder="Month">
                                        </div>

                                        <div class="container col-md-4 ">
                                            <br/>
                                            <button type="button" id="generate_payroll" class="btn btn-default btn-xs margin">
                                                Generate
                                            </button>
                                            <button name="pdf" value="true" class="btn btn-default btn-xs">
                                                <i class="fa fa-file-pdf-o"></i> PDF
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <br/><br/><br/>
                            <div class="box-body">
                                <div id="payroll_container" class="container-fluid">

                                </div>

                                <div id="approve_div" style="display: none;" class="form-group col-md-3 pull-right">
                                    <div id="special_level_approval">

                                    </div>
                                    <br/>
                                    <button id="submit_payroll_for_approval" class="btn btn-info btn-sm pull-right" type="button">
                                        Subbmit For Approval
                                    </button>
                                </div>
                            </div>

                        </div>
                    <?php } ?>

                        <div class="tab-pane  <?php if(!check_privilege('Register Employee')){?>active<?php } ?>" id="payrolls">

                        </div>

                </div>
            </div>
        </div>
















    </div>
    </div>
    </section><!-- /.content -->

<?php $this->load->view('includes/footer');








