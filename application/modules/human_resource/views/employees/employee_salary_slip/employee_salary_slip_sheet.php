<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 2/15/2019
 * Time: 3:39 PM
 */

    foreach ($employee_info as $employee) {
        $employees->load($employee['employee_id']);
        $this->load->view('includes/letterhead');
        ?>

        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>

        <table width="100%" border="1" cellspacing="-2">
            <thead>
            <tr>
                <td>

                    <table width="100%"  >
                        <thead>
                        <tr>
                            <td width="25%" style="background: #c1ceea"></td>
                            <td>
                                <table width="100%" >
                                    <thead>
                                    <tr>
                                        <td style="font-weight: bold; font-size: large; text-align: center"><h2>SALARY SLIP</h2></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size: large; text-align: center">
                                            <?= strtoupper(DateTime::createFromFormat('!m',
                                                date('m', strtotime($payroll_date)))->format('F')) . ' ' .
                                            date('Y', strtotime($payroll_date)) ?>
                                        </td>
                                    </tr>
                                    </thead>
                                </table>
                            </td>
                            <td width="25%" style="background: #c1ceea; text-align: center; font-weight: bold">
                                <h3>CONFIDENTIAL</h3>
                            </td>
                        </tr>
                        </thead>
                    </table>

                </td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>

                    <table width="100%"  >
                        <thead>
                        <tr>
                            <td width="50%">
                                <table>
                                    <thead>
                                    <tr>
                                        <td width="45%" style="font-weight: bold; font-size: 12px">Name</td>
                                        <td style="font-size: 11px">: <?= strtoupper($employee['employee_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <td width="45%" style="font-weight: bold; font-size: 12px">Employee ID</td>
                                        <td style="font-size: 11px">: <?= add_leading_zeros($employee['employee_id']) ?></td>
                                    </tr>
                                    </thead>
                                </table>
                            </td>
                            <td>
                                <table>
                                    <thead>
                                    <tr>
                                        <td width="45%" style="font-weight: bold; font-size: 12px">Title</td>
                                        <td style="font-size: 11px">: <?= strtoupper($employee['title']) ?></td>
                                    </tr>
                                    <tr>
                                        <td width="45%" style="font-weight: bold; font-size: 12px">Department</td>
                                        <td style="font-size: 11px">: <?= strtoupper($departments->department_name) ?></td>
                                    </tr>
                                    <tr>
                                        <td width="45%" style="font-weight: bold; font-size: 12px">Location</td>
                                        <td style="font-size: 11px">: <?= strtoupper($employee['location']) ?></td>
                                    </tr>
                                    </thead>
                                </table>
                            </td>
                        </tr>
                        </thead>
                    </table>

                    <table width="100%"  >
                        <thead>
                        <tr style="background: #c1ceea">
                            <td style="text-align: center; font-weight: bold" width="50%">Descriptions</td>
                            <td style="text-align: center; font-weight: bold" width="25%">Earnings</td>
                            <td style="text-align: center; font-weight: bold" >Deductions</td>
                        </tr>
                        </thead>
                    </table>

                    <table width="100%"  >
                        <thead>
                        <tr>
                            <td width="50%" style="font-size: 12px; text-align: left; padding-left: 5px">
                                Basic salary
                                <br/>
                                <?php
                                foreach ($all_allowances as $allowance){
                                    ?>
                                    <?= $allowance->allowance_name ?><br/>
                                    <?php
                                }
                                ?>
                                NSSF<br/>
                                P.A.Y.E<br/>
                                Advance<br/>
                                HESLB<br/>
                                Loan<br/>

                            </td>
                            <td width="25%" style="font-size: 12px; text-align: right; padding-right: 10px">
                                <?= number_format($employee['basic_salary'],2) ?><br/>
                                <?php

                                foreach ($all_allowances as $allowance){
                                    ?>
                                    <?= $employee[$allowance->allowance_name] > 0 ? number_format($employee[$allowance->allowance_name],2) : '-' ?><br/>
                                    <?php
                                }
                                ?>
                                - <br/>
                                - <br/>
                                - <br/>
                                - <br/>
                                - <br/>

                            </td>
                            <td style="font-size: 12px; text-align: right; padding-right: 10px" >
                                - <br/>
                                <?php
                                foreach ($all_allowances as $allowance){
                                    ?>
                                    - <br/>
                                    <?php
                                }
                                ?>
                                <?= $employee['deducted_nssf'] > 0 ? number_format($employee['deducted_nssf'],2) : '-' ?><br/>
                                <?= $employee['paye'] > 0 ? number_format($employee['paye'],2) : '-' ?><br/>
                                <?= $employee['advance_payment'] > 0 ?  number_format($employee['advance_payment'],2) : '-' ?><br/>
                                <?= $employee['heslb_loan_repay'] > 0 ? number_format($employee['heslb_loan_repay'],2) : '-'  ?><br/>
                                <?= $employee['company_loan_repay'] > 0 ? number_format($employee['company_loan_repay'],2) : '-' ?><br/>

                            </td>
                        </tr>
                        </thead>
                    </table>

                    <table width="100%" style="background: #c1ceea" >
                        <thead>
                        <tr>
                            <td style="font-size: 12px;text-align: left; font-weight: bold; padding-left: 5px" width="50%">Total</td>
                            <td style="font-size: 12px;text-align: right; font-weight: bold; padding-right: 10px" width="25%"><?= number_format($employee['total_earnings'],2) ?></td>
                            <td style="font-size: 12px;text-align: right; font-weight: bold; padding-right: 10px" ><?= number_format($employee['total_deductions'],2) ?></td>
                        </tr>
                        </thead>
                    </table>

                    <table width="100%"  >
                        <thead>
                        <tr>
                            <td width="50%">
                                <table >
                                    <thead>
                                    <tr>
                                        <td width="45%" style="font-size: 12px">Payment Date</td>
                                        <td style="font-size: 11px">: <?= $payment_date ? set_date($payment_date) : 'N/A' ?></td>
                                    </tr>
                                    <tr>
                                        <td width="45%" style="font-size: 12px">Bank Name</td>
                                        <td style="font-size: 11px">: <?= $employees->employee_bank() ? $employees->employee_bank()[0]->bank_name : 'N/A'?></td>
                                    </tr>
                                    <tr>
                                        <td width="45%" style="font-size: 12px">Bank Account Name</td>
                                        <td style="font-size: 11px">: <?= strtoupper($employees->employee_bank() ? $employee['employee_name'] : 'N/A') ?></td>
                                    </tr>
                                    <tr>
                                        <td width="45%" style="font-size: 12px">Bank Account #</td>
                                        <td style="font-size: 11px">: <?= $employees->employee_bank() ? $employees->employee_bank()[0]->account_no : 'N/A' ?></td>
                                    </tr>
                                    </thead>
                                </table>
                            </td>
                            <td style="font-size: 11px; text-align: center; font-style: italic">
                                <br/><h3 style="font-style: normal">NET PAY</h3>
                                ----------------------------------------------------------------------------------------------------<br/>
                                <h4 style="font-style: normal"><?= number_format($employee['net_pay'],2) ?></h4>
                                ----------------------------------------------------------------------------------------------------<br/>
                                <?= numbers_to_words($employee['net_pay']).' Tsh.' ?>
                            </td>
                        </tr>
                        </thead>
                    </table>



                </td>
            </tr>
            </tbody>
        </table>



        <br/>
        <br/>
        <br/>
        <div class="pull-left">
            <span>
                Aurhorized By:
            </span>
            <br/>
            <br/>
            <span>
                -----------------------------------
            </span>
            <br/>
            <span>
                Xxxxxx Xxxxxxxxx
            </span>

        </div>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>

        <?php
    }