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
            <div class="box-tools pull-right">
                <?php
                if(check_permission('Human Resources')) {
                    ?>
                    <button data-toggle="modal" data-target="#new_employee_ssf" class="btn btn-xs btn-default">
                        <i class="fa fa-plus-circle"></i>&nbsp;New SSF
                    </button>
                    <div id="new_employee_ssf" class="modal fade form_modal" tabindex="-1" role="dialog">
                        <?php $this->load->view('employees/employee_ssfs/employee_ssf_form'); ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="col-xs-12 table-responsive">
            <table employee_id="<?= $employee->{$employee::DB_TABLE_PK} ?>" id="employee_ssf" class="table table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>SSF </th>
                    <th>SSF No</th>
                    <th>Start Date</th>
                    <th>Registered On</th>
                    <th>Registered By</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
