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
                        <button data-toggle="modal" data-target="#new_contract" class="btn btn-xs btn-default">
                            New Contract
                        </button>
                        <div id="new_contract" class="modal fade form_modal" tabindex="-1" role="dialog">
                            <?php $this->load->view('human_resources/employees/contract_form'); ?>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="col-xs-12 table-responsive">
            <table employee_id="<?= $employee->{$employee::DB_TABLE_PK} ?>" id="employee_contracts" class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Start Date</th><th>End Date</th><th>Salary</th><th>Registered By</th><th>Date Registered</th><th>Description</th><th>Status</th><th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
