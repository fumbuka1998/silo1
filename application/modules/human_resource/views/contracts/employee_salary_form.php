<?php
    
    $edit = isset($employee_salary);

    //inspect_object($employee_contract);

?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $edit ? ' Edit Salary Information': 'Review Salary' ?></h4>
            </div>
            <div class="modal-body">
               

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="start_date" class="control-label">Start Date</label>
                            <input type="text" class="form-control datepicker" required name="start_date" value="<?= $edit ?$employee_salary->start_date : '' ?>">

                          
                        </div>

                        <div class="form-group col-md-6">
                            <label for="end_date" class="control-label">End Date</label>
                            <input type="text" class="form-control datepicker" required name="end_date" value="<?= $edit ?$employee_salary->end_date : '' ?>">
                        </div>

                         <input type="hidden" name="employee_contract_id" value="<?= $employee_contract->{$employee_contract::DB_TABLE_PK} ?>">
                         <input type="hidden" name="employee_salary_id" value="<?= $edit ? $employee_salary->{$employee_salary::DB_TABLE_PK}: '' ?>">

                    </div>

                </div>


                <div class="row">
                    <div class="col-xs-12">

                     <div class="form-group col-md-4">
                            <label for="payroll_no" class="control-label">Payroll No</label>
                            <input type="text" class="form-control" name="payroll_no" value="<?= $edit? $employee_salary->payroll_no: ''?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="salary" class="control-label">Salary</label>
                            <input type="text" class="form-control number_format" required name="salary" value="<?= $edit ? $employee_salary->salary: '' ?>">
                        </div>

                         <div class="form-group col-md-4">
                            <?php  $taxation_options=['taxable'=>'Taxable','non_taxable'=>'Non Taxable'];?>

                            <?php
                                echo form_label('Taxation','tax_details');
                                echo form_dropdown('tax_details', $taxation_options,$edit ? $employee_salary->tax_details: '', " class = ' searchable form-control' required ");
                            ?>
                          
                        </div>

                    </div>
                </div>

                    <div class="row">
                      <div class="col-xs-12">
                            <div class="form-group col-md-6">
                                <label for="subsistance" class="control-label">Subsistence</label>
                                <input type="text" name="subsistance" class="form-control number_format" value="<?= $edit ? $employee_salary->subsistance: ''?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="responsibility" class="control-label">Responsibility</label>
                                <input type="text" name="responsibility" class="form-control number_format" value="<?= $edit ? $employee_salary->responsibility: ''?>">
                            </div>
                    </div>
                  </div>

                  <div class="row">
                      <div class="col-xs-12">

                        <div class="form-group col-md-4">
                            <?php
                                echo form_label('Currency','currency_id');
                                echo form_dropdown('currency_id', currency_dropdown_options(),$edit ? $employee_salary->currency_id : '', " class = ' searchable form-control' required ");
                            ?>

                        </div>

                        <div class="form-group col-md-4">


                            <?php  $payment_options=['bank'=>'Bank','cash'=>'Cash'];?>

                            <?php
                                echo form_label('Payment Mode','payment_mode');
                                echo form_dropdown('payment_mode', $payment_options,$edit ? $employee_salary->payment_mode: '', " class = ' searchable form-control' required ");
                            ?>

                        </div>
                        
                        <div class="form-group col-md-4">
                          
                             <?php  $contribution_options=['contribution'=>'Contribution','non_contribution'=>'Non Contribution'];?>

                            <?php
                                echo form_label('Social security Fund','ssf_contribution');
                                echo form_dropdown('ssf_contribution', $contribution_options,$edit ? $employee_salary->ssf_contribution: '', " class = ' searchable form-control' required ");
                            ?>
                        </div>

                    </div>
                </div>


            </div>

            <div class="modal-footer">
                <button class="btn btn-default btn-sm save_salary_button" type="button">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>