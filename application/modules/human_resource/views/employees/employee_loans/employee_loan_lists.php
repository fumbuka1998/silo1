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
            Payroll Payments
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('/finance/') ?>"><i class="fa fa-dashboard"></i>Finance</a></li>
            <li class="active">loans</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">

                <?php
                if($all_loans){
                    ?>


                <div class="col-md-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a   href="#loans_list" data-toggle="tab">Loans</a></li>
                            <li><a href="#pay_loans" data-toggle="tab">Payroll Loan Payments</a></li>
                            <li><a href="#deductions_loans" data-toggle="tab">Payroll Deductions Payments</a></li>
                            <li><a href="#net_payable" data-toggle="tab">Net Payable</a></li>
                            <li><a href="#salary_slip" data-toggle="tab">Salary Slip </a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane" id="deductions_loans">

                                <div class="box">
                                    <div class="box-header with-border">

                                    <div class="col-xs-12">
                                        <div class="box-tools col-xs-12">
                                            <form>
                                                <div class="form-group col-md-4">
                                                    <label for="payroll_id" class="control-label">Payroll for:</label>
                                                    <?= form_dropdown('payroll_id', $payroll_options,'','  class="form-control searchable" ') ?>
                                                </div>

                                                <div style="display: none" class="container col-md-4 ">
                                                    <br/>
                                                    <button style="font-weight: bold; font-size: larger" type="button" id="view_payroll_deductions" class="btn btn-default btn-xs margin">
                                                        Go
                                                    </button>
                                                </div>

                                            </form>
                                        </div>
                                    </div>

                                    <div style="display: none" id="payroll_deductions_div">

                                    </div>

                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="salary_slip">

                                <div class="box">
                                    <div class="box-header with-border">

                                    <div class="col-xs-12">
                                        <div class="box-tools col-xs-12">
                                            <form>
                                                <div class="form-group col-md-4">
                                                    <label for="payroll_id" class="control-label">Payroll for:</label>
                                                    <?= form_dropdown('payroll_id', $payroll_options,'','  class="form-control searchable" ') ?>
                                                </div>

                                                <div style="display: none" class="container col-md-4 ">
                                                    <br/>
                                                    <button style="font-weight: bold; font-size: larger" type="button" id="wiew_payroll_salary_slip" class="btn btn-default btn-xs margin">
                                                        Go
                                                    </button>
                                                </div>

                                            </form>
                                        </div>
                                    </div>

                                    <div style="display: none" id="payroll_salary_slip_div_div">

                                    </div>

                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="net_payable">


                                <div class="box">
                                    <div class="box-header with-border">

                                    <div class="col-xs-12">
                                        <div class="box-tools col-xs-12">
                                            <form>
                                                <div class="form-group col-md-4">
                                                    <label for="payroll_id" class="control-label">Payroll for:</label>
                                                    <?= form_dropdown('payroll_id', $payroll_options,'','  class="form-control searchable" ') ?>
                                                </div>

                                                <div style="display: none" class="container col-md-4 ">
                                                    <br/>
                                                    <button style="font-weight: bold; font-size: larger" type="button" id="view_payroll_net_pay" class="btn btn-default btn-xs margin">
                                                        Go
                                                    </button>
                                                </div>

                                            </form>
                                        </div>
                                    </div>

                                    <div style="display: none" id="payroll_netpay_div">

                                    </div>

                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="pay_loans">

                                <div class="box">
                                    <div class="box-header with-border">

                                    <div class="col-xs-12">
                                        <div class="box-tools col-xs-12">
                                            <form>
                                                <div class="form-group col-md-4">
                                                    <label for="payroll_id" class="control-label">Payroll for:</label>
                                                    <?= form_dropdown('payroll_id', $payroll_options,'','  class="form-control searchable" ') ?>
                                                </div>

                                                <div style="display: none" class="container col-md-4 ">
                                                    <br/>
                                                    <button style="font-weight: bold; font-size: larger" type="button" id="view_payroll_loan_repay" class="btn btn-default btn-xs margin">
                                                        Go
                                                    </button>
                                                </div>

                                            </form>
                                        </div>
                                    </div>

                                    <div style="display: none" id="payroll_repay_div">

                                    <div id="payroll_loan_head_div">
                                    </div>

                                        <table  class="table table-bordered table-hover employee_loan_repay_table">
                                            <thead>
                                            <tr><th>Name</th>
                                                <th>Title</th>
                                                <th>Location</th>
                                                <th>Advance</th>
                                                <th>HESLB LOAN</th>
                                                <th>COMPANY LOAN</th>
                                            </tr>
                                            </thead>
                                            <tfoot >
                                            <tr style="font-weight: bold; background: #a4b2cb">
                                                <td style="font-weight: bold" colspan="3">TOTAL</td>
                                                <td id="total_advance" > </td>
                                                <td id="total_heslb"></td>
                                                <td id="total_company"></td>
                                            </tr>
                                            <tr id="tfooter" style="display: none">
                                                <td colspan="3"></td>
                                                <td style="text-align: right"><button style="display: none" id="receive_advance" class="button btn-primary btn-xs">Receive Advance</button>
                                                    <form method="post" target="_blank" action="<?= base_url('human_resource/human_resources/payroll_heslb_preview') ?>">
                                                        <input id="advance_payroll_id" type="hidden" name="payroll_id" value="">
                                                        <input type="hidden" name="loan_name" value="advance">
                                                        <button style="display: none" id="preview_advance" class="button btn-yahoo btn-xs preview_loan"><i class="fa fa-file-pdf-o"></i> Advance Payments</button>
                                                    </form>
                                                </td>
                                                <td style="text-align: right">
                                                    <button style="display: none" id="receive_heslb" class="button btn-success btn-xs">Pay HESLB</button>
                                                    <form method="post" target="_blank" action="<?= base_url('human_resource/human_resources/payroll_heslb_preview') ?>">
                                                        <input id="heslb_payroll_id" type="hidden" name="payroll_id" value="">
                                                        <input type="hidden" name="loan_name" value="heslb">
                                                        <button style="display: none" id="preview_heslb" class="button btn-info btn-xs preview_loan"><i class="fa fa-file-pdf-o"></i> HESLB Contributors</button>
                                                    </form>
                                                </td>
                                                <td style="text-align: right"><button style="display: none" id="receive_company_loan" class="button btn-info btn-xs">Receive Company Loan</button>
                                                    <form method="post" target="_blank" action="<?= base_url('human_resource/human_resources/payroll_heslb_preview') ?>">
                                                        <input id="company_payroll_id" type="hidden" name="payroll_id" value="">
                                                        <input type="hidden" name="loan_name" value="company">
                                                        <button style="display:  none" id="preview_company" class="button btn-success btn-xs preview_loan"><i class="fa fa-file-pdf-o"></i> Company Loans</button>
                                                    </form>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    </div>
                                </div>
                            </div>

                            <div class="active  tab-pane" id="loans_list">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <div class="col-xs-12">
                                            <div class="box-tools pull-right">

                                                <?php
                                                if(check_permission('Human Resources')) {
                                                    ?>
                                                    <button data-toggle="modal" data-target="#new_employee_loan" class="btn btn-xs btn-default">
                                                        <i class="fa fa-plus-circle"></i>&nbsp;New Loan
                                                    </button>
                                                    <div id="new_employee_loan" class="modal fade employee_loan_form" role="dialog">
                                                        <?php $this->load->view('employees/employee_loans/employee_loan_form_finance'); ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-xs-12">

                                                <table employee_id="all" class="table table-bordered table-hover table-condensed employee_loan_table">
                                                    <thead>
                                                    <tr><th>Lonee</th>
                                                        <th>Loan</th>
                                                        <th>Approved Date</th>
                                                        <th>Deduction Start Date</th>
                                                        <th>Total Loan Amount</th>
                                                        <th>Monthly Deduction Amount</th>
                                                        <th>Loan Balance</th>
                                                        <th>Loan Application Letter</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>

                                                <br/>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                    <?php
                }else{
                    ?>

                    <div class="box">
                        <div class="box-header with-border">
                            <div class="col-xs-12">
                                <div class="box-tools pull-right">

                                    <?php
                                    if(check_permission('Human Resources')) {
                                        ?>
                                        <button data-toggle="modal" data-target="#new_employee_loan" class="btn btn-xs btn-default">
                                            <i class="fa fa-plus-circle"></i>&nbsp;New Loan
                                        </button>
                                        <div id="new_employee_loan" class="modal fade employee_loan_form" role="dialog">
                                            <?php $this->load->view('employees/employee_loans/employee_loan_form_finance'); ?>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-12">

                                    <table employee_id="all" class="table table-bordered table-hover table-condensed employee_loan_table">
                                        <thead>
                                        <tr><th>Lonee</th>
                                            <th>Loan</th>
                                            <th>Approved Date</th>
                                            <th>Deduction Start Date</th>
                                            <th>Total Loan Amount</th>
                                            <th>Monthly Deduction Amount</th>
                                            <th>Loan Balance</th>
                                            <th>Loan Application Letter</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>

                <?php }

                ?>


            </div>
        </div>
    </section><!-- /.content -->

<?php $this->load->view('includes/footer');


