<?php if(count($employee_designation_list)>0){?>

    <table class="table table-bordered table-condensed">
        <thead>
        <tr>
            <td>Start Date</td>
            <td>End Date</td>
            <td>Department</td>
            <td>Designation</td>
            <td>Work Station</td>
            <td>Actions</td>
        </tr>
        </thead>

        <tbody>

        <?php foreach($employee_designation_list as $employee_designation){?>

            <tr>

                <td><?= $employee_designation->start_date;?></td>
                <td><?= $employee_designation->end_date;?></td>
                <td><?= $employee_designation->employee_department()->department_name;?></td>
                <td><?= $employee_designation->employee_job_position()->position_name;?></td>
                <td><?= $employee_designation->employee_branch()->branch_name;?></td>

                <td>

    	<span class="pull-right">

		    <button data-toggle="modal" data-target="#edit_employee_designationf_<?= $employee_designation->id ?>"
                    class="btn btn-default btn-xs">
		        <i class="fa fa-edit"></i> Edit
		    </button>
		    <div id="edit_employee_designationf_<?= $employee_designation->id?>" class="modal fade" role="dialog">
                       <?php  $data['employee_contract']=$employee_designation->employee_contract();
                       $data['employee_designation']=$employee_designation;

                       ?>

                       <?php  $this->load->view('contracts/employee_designation_form',$data); ?>

		    </div>

		    <button class="btn btn-danger btn-xs delete_employee_designation" employee_designation_id="<?= $employee_designation->{$employee_designation::DB_TABLE_PK} ?>">
		        <i class="fa fa-trash"></i> Delete
		    </button>
		</span>


                </td>
            </tr>

        <?php }?>

        </tbody>

    </table>



<?php }else{?>
    <p style="text-align: center;">NOT YET SIGNED TO ANY POSITION</p>
<?php } ?>