<div class="row">
    <div class="col-xs-12">

        <div class="panel-group" id="accordion">

        <!-- start panel-->

            <div class="panel panel-default">

                <div class="panel-heading">

                    <div class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion"
                           target="#collapse"
                           href="#salary_collapse">
                           <span style=" font-style: italic">
                              <small></small>
                           </span>
                            <span style="color: #3c8dbc;"> SALARY INFORMATION</span>&nbsp;&nbsp;&nbsp;
                           
                        </a>
                    </div>
                </div>

                 <div id="salary_collapse" class="panel-collapse collapse">

	                <div class="panel-body" id="panel">

                    <div class="row">

                      <div class="col-lg-12">

  				               <button data-toggle="modal" data-target="#new_salary" class="btn btn-xs btn-default pull-right">
                              <i class="fa fa-plus-circle"></i>&nbsp;Review salary
                          </button>
                          <div id="new_salary" class="modal fade form_modal" tabindex="-1" role="dialog">

                              <?php $data['employee_contract']=$employee_contract; 
                               $this->load->view('contracts/employee_salary_form',$data);?>
                          </div>
                      </div>
                    </div>
                    <br>

                        <div class="employee_salary_list" employee_contract_id="<?= $employee_contract->{$employee_contract::DB_TABLE_PK} ?>">

                        		
                        </div>

				  
	                </div>

	            </div>

            </div>

        <!-- end panel-->

        <!-- start panel-->

            <div class="panel panel-default">

                <div class="panel-heading">

                    <div class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion"
                           target="#collapse"
                           href="#designation_collapse">
                           <span style=" font-style: italic">
                              <small></small>
                           </span>
                            <span style="color: #3c8dbc;"> DESIGNATION,DEPARTMENT AND WORK STATION</span>&nbsp;&nbsp;&nbsp;
                           
                        </a>
                    </div>
                </div>

                 <div id="designation_collapse" class="panel-collapse collapse">

                        <div class="panel-body" id="panel">

                            <div class="row">

                                <div class="col-lg-12">

                                    <button data-toggle="modal" data-target="#new_designation" class="btn btn-xs btn-default pull-right">
                                        <i class="fa fa-plus-circle"></i>&nbsp;Review Designation
                                    </button>
                                    <div id="new_designation" class="modal fade form_modal" tabindex="-1" role="dialog">

                                        <?php $data['employee_contract']=$employee_contract;
                                        $this->load->view('contracts/employee_designation_form',$data);?>
                                    </div>
                                </div>
                            </div>
                            <br>

                            <div class="employee_designation_list" employee_contract_id="<?= $employee_contract->{$employee_contract::DB_TABLE_PK} ?>">


                            </div>


                        </div>


	            </div>

            </div>

        <!-- end panel-->

        </div>
    </div>
</div>
                