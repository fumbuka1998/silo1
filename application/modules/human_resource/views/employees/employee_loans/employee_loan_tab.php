<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 6/9/2016
 * Time: 3:41 PM
 */
?>
<div class="box">
    <div class="box-header with-border">
        <div class="col-xs-12">


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="box-tools pull-right">

                                        <button data-toggle="modal" data-target="#new_employee_loan" class="btn btn-xs btn-default">
                                            <i class="fa fa-plus-circle"></i>&nbsp;Request for Loan
                                        </button>
                                        <div id="new_employee_loan" class="modal fade employee_loan_form" tabindex="-1" role="dialog">

                                        </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="box-tools pull-right">
                                    <?php
                                    if(check_permission('Human Resources')) {
                                        ?>
                                        <button data-toggle="modal" data-target="#new_account" class="btn btn-default btn-xs">
                                            <i class="fa fa-plus"></i> Generate Loan Account
                                        </button>
                                        <div id="new_account" class="modal fade" role="dialog">
                                            <?php $this->load->view('account_form_human_resources'); ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="col-xs-12 table-responsive">
                            <table employee_id="<?= $employee->{$employee::DB_TABLE_PK} ?>" class="table table-bordered table-hover table-condensed employee_loan_table">
                                <thead>
                                <tr>
                                    <th>Lonee</th>
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
