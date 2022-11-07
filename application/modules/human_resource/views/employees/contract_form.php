<?php
    $edit_contract = isset($employee_contract);
    $edit_designation = isset($employee_designation);
    $edit_salary = isset($employee_salary);

    /*
payroll_no
salary
tax_details
subsistence
responsibility
currency
payment_mode
ssf_contribution
*/

?>
<div id="contract_modal" class="modal-dialog modal-lg">
    <div class="modal-content">
        <form>
            <div class="modal-header" style="background-color: #3c8dbc; color: #FFFFFF">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title " >Contract Information</h2>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="start_date" class="control-label">Start Date</label>
                            <input type="text" class="form-control datepicker" required name="start_date" value="<?= $edit_contract ? $employee_contract->start_date : '' ?>">

                            <input type="hidden" name="employee_id" value="<?= $edit_contract ? $employee_contract->employee_id : $employee->{$employee::DB_TABLE_PK} ?>">
                            <input type="hidden" name="employee_contract_id" value="<?= isset($employee_contract) ? $employee_contract->{$employee_contract::DB_TABLE_PK} : '' ?>">
                            <input type="hidden" name="employee_salary_id" value="<?= $edit_salary ? $employee_salary->{$employee_salary::DB_TABLE_PK}: '' ?>">
                             
                            <input type="hidden" name="employee_designation_id" value="<?= $edit_designation ? $employee_designation->{$employee_designation::DB_TABLE_PK}: '' ?>">
                         </div>
                            <div class="form-group col-md-6">
                            <label for="end_date" class="control-label">End Date</label>
                            <input type="text" class="form-control datepicker" required name="end_date" value="<?= $edit_contract ? $employee_contract->end_date : '' ?>">
                            </div>
                    </div>
                </div>

                        <div class="row">
                            <div class="col-md-12"  style="background-color: #3c8dbc; color: #FFFFFF">
                                    <h4>Salary information</h4>
                            </div>
                        </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="payroll_no" class="control-label">Payroll No</label>
                            <input type="text" class="form-control" name="payroll_no" value="<?= $edit_salary? $employee_salary->payroll_no: ''?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="salary" class="control-label">Salary</label>
                            <input type="text" class="form-control  number_format" name="salary" value="<?= $edit_salary ? $employee_salary->salary : '' ?>">
                        </div>
                        <div class="form-group col-xs-4">
                            <label for="tax_details" class="control-label">Taxation</label>
                            <?php  $taxation_options=['taxable'=>'Taxable','non_taxable'=>'Non Taxable'];?>
                            <?php
                            echo form_dropdown('tax_details', $taxation_options,$edit_salary ? $employee_salary->tax_details : '', " class = ' searchable form-control' required ");
                            ?>
                        </div>
                    </div>
                </div>
<!---->
<!--                <div class="row">-->
<!--                    <div class="col-xs-12">-->
<!--                        <div class="form-group col-md-6">-->
<!--                            <label for="subsistence" class="control-label">Subsistence</label>-->
<!--                            <input type="text" name="subsistence" class="form-control number_format" value="--><?//= $edit_salary? $employee_salary->subsistance: ''?><!--">-->
<!--                        </div>-->
<!--                        <div class="form-group col-md-6">-->
<!--                            <label for="responsibility" class="control-label">Responsibility</label>-->
<!--                            <input type="text" name="responsibility" class="form-control number_format" value="--><?//= $edit_salary? $employee_salary->responsibility: ''?><!--">-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->


                <div class="row">
                    <div class="container-fluid">
                    <div class="col-xs-12">
                       <table class="table table-hover table-bordered table-responsive">
                           <thead>
                           <tr>
                               <td><label for="allowance_id" class="control-label">Allowance Name</label></td>
                               <td><label for="allowance_amount" class="control-label">Amount</label></td>
                               <td></td>
                           </tr>
                           <tr class="row_template" style="display: none">
                               <td>
                                   <?= form_dropdown('allowance_id', $allowance_options, '', ' class="orm-control" ') ?>
                               </td>
                               <td>
                                   <input type="text" name="allowance_amount" class="form-control number_format" value="">
                               </td>
                               <td>
                                   <button title="Remove Row" type="button" employee_allowance_id="" class="btn btn-xs btn-danger row_remover pull-right"><i class="fa fa-close"></i></button>
                               </td>
                           </tr>
                           </thead>

                           <tbody>

                           <?php
                           if(!$edit_contract){
                               ?>
                               <tr>
                                   <td>
                                       <?= form_dropdown('allowance_id', $allowance_options, '', ' class="orm-control searchable" ') ?>
                                   </td>
                                   <td>
                                       <input type="text" name="allowance_amount" class="form-control number_format" value="">
                                   </td>
                                   <td>
                                       <button title="Remove Row" type="button" employee_allowance_id="" class="btn btn-xs btn-danger row_remover pull-right"><i class="fa fa-close"></i></button>
                                   </td>
                               </tr>

                               <?php
                           }else{

                               foreach ($allowances as $allowance) {
                                   ?>
                                   <tr>
                                       <td>
                                           <?= form_dropdown('allowance_id', $allowance_options, $allowance['allowance_id'], ' class="orm-control searchable" ') ?>
                                       </td>
                                       <td>
                                           <input type="text" name="allowance_amount" class="form-control number_format" value="<?= number_format($allowance['allowance_amount']) ?>">
                                           <input type="hidden" id="<?= $allowance['employee_allowance_id'] ?>" name="employee_allowance_id" value="<?= $allowance['employee_allowance_id'] ?>">
                                       </td>
                                       <td>
                                           <button title="Remove Row" type="button" employee_allowance_id="<?= $allowance['employee_allowance_id'] ?>"
                                                   class="btn btn-xs btn-danger row_remover pull-right"><i
                                                       class="fa fa-close"></i></button>
                                       </td>
                                   </tr>

                                   <?php
                               }
                           }
                           ?>

                           </tbody>
                           <tfoot>
                           <tr>
                               <th colspan="3">
                                   <button type="button" class="btn btn-xs btn-default row_adder pull-right">Add Allowance</button>
                               </th>
                           </tr>
                           </tfoot>
                       </table>
                    </div>
                </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-4">
                            <label for="currency" class="control-label">Currency</label>
                            <?php
                            echo form_dropdown('currency', currency_dropdown_options(),$edit_salary ? $employee_salary->currency_id: '', " class = ' searchable form-control' required ");
                            ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="payment_mode" class="control-label">Payment Mode</label>
                            <?php  $payment_options=['bank'=>'Bank','cash'=>'Cash'];?>
                            <?php
                            echo form_dropdown('payment_mode', $payment_options,$edit_salary ? $employee_salary->payment_mode : '', " class = ' searchable form-control' required ");
                            ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="ssf_contribution" class="control-label">Social Security</label>
                            <?php  $contribution_options=['contribution'=>'Contribution','non_contribution'=>'Non Contribution'];?>
                            <?php
                            echo form_dropdown('ssf_contribution', $contribution_options,$edit_salary ? $employee_salary->ssf_contribution : '', " class = ' searchable form-control' required ");
                            ?>
                        </div>
                    </div>
                </div>
                        <div class="row">
                            <div class="col-md-12" style="background-color: #3c8dbc; color: #FFFFFF">
                               <h4>Designation,Department and Work Station</h4>
                            </div>
                       </div>

                <div class="row">
                    <div class="col-xs-12">

                        <div class="form-group col-md-4">
                            <label for="department_id" class="control-label">Departiment</label>
                            <?= form_dropdown('department_id',$department_options, $edit_designation ? $employee_designation->department_id : '', ' class="form-control searchable" ') ?>
                       </div>
                        <div class="form-group col-md-4">
                            <label for="job_position_id" class="control-label">Designation</label>
                            <?= form_dropdown('job_position_id',$job_position_options,$edit_designation? $employee_designation->job_position_id:'','class="form-control searchable"') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="branch_id" class="control-label">Work Station</label>
                            <?= form_dropdown('branch_id',$branch_options, $edit_designation? $employee_designation->branch_id:'','class="form-control searchable"') ?>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-default btn-sm save_contract_button" type="button">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>