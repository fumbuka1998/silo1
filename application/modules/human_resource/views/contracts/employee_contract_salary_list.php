<?php if(count($employee_salary_list)>0){?>

    <table class="table table-bordered table-condensed">
    <thead>
	    <tr>
	    	<td>Start Date</td>
	    	<td>End Date</td>
	    	<td>Salary</td>
	    	<td>Taxation</td>
	    	<td>Social Security</td>
	    	<td>Payment Mode</td>
	    	<td>Actions</td>
	    </tr>
    </thead>

    <tbody>

    <?php foreach($employee_salary_list as $employee_salary){?>

	    <tr>

	    <td><?= custom_standard_date($employee_salary->start_date) ?></td>
	    <td><?= custom_standard_date($employee_salary->end_date) ?></td>
	    <td><?= number_format($employee_salary->salary) ?></td>
	    <td><?= $employee_salary->tax_details;?></td>
	    <td><?= $employee_salary->ssf_contribution;?></td>
	    <td><?= $employee_salary->payment_mode;?></td>
	    <td>

    	<span class="pull-right">

		    <button data-toggle="modal" data-target="#edit_employee_salaryf_<?= $employee_salary->id ?>"
		            class="btn btn-default btn-xs">
		        <i class="fa fa-edit"></i> Edit
		    </button>
		    <div id="edit_employee_salaryf_<?= $employee_salary->id?>" class="modal fade" role="dialog">
                       <?php  $data['employee_contract']=$employee_salary->employee_contract();
                            $data['employee_salary']=$employee_salary; 
                               
                        ?>

		             <?php  $this->load->view('contracts/employee_salary_form',$data); ?>

		    </div>

		    <button class="btn btn-danger btn-xs delete_employee_salary" employee_salary_id="<?= $employee_salary->{$employee_salary::DB_TABLE_PK} ?>">
		        <i class="fa fa-trash"></i> Delete
		    </button>
		</span>


	    </td>
	    </tr>

    <?php }?>
    	
    </tbody>
	
	</table>



<?php }else{?>
<p style="text-align: center;">NO SALARY FOUND</p>
<?php } ?>