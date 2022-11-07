
<div class="box">
        <div class="box-header with-border">
            <div class="col-xs-12">
                <div class="box-tools pull-right">
                    <button data-toggle="modal" data-target="#new_department" class="btn btn-default btn-xs">
                        <i class="fa fa-plus"></i> New Department
                    </button>
                    <div id="new_department" class="modal fade" tabindex="-1" role="dialog">
                        <?php $this->load->view('settings/departments/department_form'); ?>
                    </div>
                </div>
            </div>
        </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table id="departments_list" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Department Name</th><th>Description</th><th>No. of Employees</th><th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>