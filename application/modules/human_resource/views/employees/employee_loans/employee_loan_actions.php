<span class="pull-right">
<?php
$employee_loan_id = $employee_loan_data->{$employee_loan_data::DB_TABLE_PK};

?>
    <div id="pay_employee_loan<?= $employee_loan_id ?>" class="modal fade loan_payment_form" role="dialog">
        <?php $this->load->view('employees/employee_loans/loan_payment_form'); ?>
    </div>


<div style="width: 100% !important; overflow-x: visible">
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
            View
        </button>
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">

            <?php

            if(check_privilege('Loan')){
            if($employee_loan_data->loan_balance_amount > 0) {
            ?>

            <li>
                <button data-toggle="modal" data-target="#pay_employee_loan<?= $employee_loan_id ?>" class="btn btn-xs btn-default">
                    <i class="fa fa-edit"></i>&nbsp;Make Payments
                </button>
            </li>

           <?php
            }
            }?>
            <li>
                <form method="post" target="_blank" action="<?= base_url('finance/employee_loan_history') ?>">
                    <input name="employee_id" value="<?= $employee_id ?>" type="hidden">
                    <input name="employee_loan_id" value="<?= $employee_loan_id ?>" type="hidden">
                    <input name="loan_id" value="<?= $employee_loan_data->loan_id ?>" type="hidden">
                    <input name="cr_account" value="<?= $employee_loan_data->loan_account_id ?>" type="hidden">

                    <button type="submit" id="loan_history<?= $employee_loan_id ?>" class="btn btn-xs btn-default">
                        <i class="fa fa-eye"></i> Payment History
                    </button>

                </form>

            </li>

        </ul>
    </div>

</div>