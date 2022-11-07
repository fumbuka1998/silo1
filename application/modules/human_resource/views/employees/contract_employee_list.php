<div class="box">


    <?php if(check_privilege('Register Employee')){ ?>
        <div class="box-header with-border">
            <div class="col-xs-12">
                <div class="box-tools  pull-right">
                    <button data-toggle="modal" data-target="#employee_form" class="btn btn-default btn-xs">
                        <i class="ion-person-add"></i> New Employee
                    </button>
                    <div id="employee_form" class="modal fade" role="dialog">
                        <?php $this->load->view('human_resource/employees/employee_form'); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-bordered table-hover table-striped" id="contract_employee_list">
                    <thead>
                        <tr>
                            <th>Full Name</th><th>Phone Number</th><th>Alternative Phone</th><th>Email</th><th>Address</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
