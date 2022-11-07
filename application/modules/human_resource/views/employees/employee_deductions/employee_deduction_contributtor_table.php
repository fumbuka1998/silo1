<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 2/15/2019
 * Time: 3:39 PM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center"><?= strtoupper($deduction_name) ?> CONTRIBUTIONS SHEET </h2>
<br/>
<br/>

<div class="container-fluid">
    <table>
        <thead>
        <tr>
            <td style="font-weight: bold; font-size: large">
                <?= strtoupper($departments->department_name) ?> DEPARTMENT:  <?= strtoupper($deduction_name) ?> CONTRIBUTIONS FOR
                <?= strtoupper(DateTime::createFromFormat('!m', date('m', strtotime($payroll_date)))->format('F')) . ' ' . date('Y', strtotime($payroll_date)) ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-size: large">
                Currency: Tanzanian Shilings (TSh)
            </td>
        </tr>
        </thead>
    </table>
</div>


<table width="100%" border="1" cellspacing="0"
       style="font-size: 11px">
    <thead>
    <tr style="background: #a4b2cb">
    <th>S/No</th>
    <th>Name</th>
    <th>Member Number</th>
    <th>Basic Salary</th>
    <th>Employee Deducted Amount </th>
    <th>Employer Deducted Amount </th>
    <th>Total Amount </th>
    </tr>
    </thead>

    <tbody>
    <?php
        $total_basic_salary = 0;
        $total_employee_amount = 0;
        $total_employer_amount = 0;
        $total_total_amount = 0;
      foreach ($payment_info as $index => $payment){
          $employee->load($sallary_info[$index]->employee_id);
          $basic_salary = $sallary_info[$index]->basic_salary;
          $total_basic_salary += $basic_salary;
          $employee_amount = strtoupper($deduction_name) == 'NSSF' ? $sallary_info[$index]->deducted_nssf : 0;
          $total_employee_amount += $employee_amount;
          $employer_amount = strtoupper($deduction_name) == 'NSSF' ? ($payment->amount - $sallary_info[$index]->deducted_nssf) : $payment->amount;
          $total_employer_amount += $employer_amount;
          $total_amount = $payment->amount;
          $total_total_amount += $total_amount;
          if($ssf){
              $employee_ssf_details = $employee_ssf->employee_ssf_details($sallary_info[$index]->employee_id, $ssf[0]->id);
          }else{
              $employee_ssf_details = false;
          }

          ?>

          <tr>
              <td style="text-align: left"><?= $index+1 ?></td>
              <td style="text-align: left" ><?= strtoupper($employee->full_name()) ?></td>
              <td style="text-align: center" ><?= $employee_ssf_details ? $employee_ssf_details->ssf_no : '-' ?></td>
              <td style="text-align: right"><?= number_format($basic_salary,2) ?></td>
              <td style="text-align: right"><?= number_format($employee_amount,2) ?></td>
              <td style="text-align: right"><?= number_format($employer_amount,2) ?></td>
              <td style="text-align: right"><?= number_format($total_amount,2) ?></td>
          </tr>

         <?php
      }
    ?>
    </tbody>
    <tfoot>
      <tr>
          <td style="text-align: left; font-weight: bold" colspan="3">TOTAL</td>
          <td style="text-align: right; font-weight: bold"><?= number_format($total_basic_salary,2) ?></td>
          <td style="text-align: right; font-weight: bold"><?= number_format($total_employee_amount,2) ?></td>
          <td style="text-align: right; font-weight: bold"><?= number_format($total_employer_amount,2) ?></td>
          <td style="text-align: right; font-weight: bold"><?= number_format($total_total_amount,2) ?></td>
      </tr>
    </tfoot>


</table>
