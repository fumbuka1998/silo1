<?php
/**
 * HUMAN RESOURCE USEFUL FUNCTIONS
 */
function contract_employees($employee_id='all', $issue_date='today',$limit=0,$start=0,$where='',$order=''){

    $CI =& get_instance();
    $CI->load->model('Employee');
    $Employee = new Employee();

	$employee_id_where='';

	
	if($employee_id != 'all'){

		$where=[ 'employee_id' => $employee_id ];

	}

	$where .= $employee_id_where;

   
	if($issue_date =='today'){

		$issue_date=date('Y-m-d');
	}
    
    $Employees = $Employee->get(0,0,$where,$order);

     $contracts=[];
     $non_contracts=[];
     $incomplete_contracts=[];
     $closed_contracts=[];
     $expired_contracts=[];

    foreach($Employees as $Employee){

    	$latest_contract=$Employee->latest_employee_contract();

    	if(!empty($latest_contract)>0){


			    	if($latest_contract->end_date >= $issue_date){


			    		if(!empty($latest_contract->employee_contract_close()) == 0){


			    		 $latest_salary=$latest_contract->latest_contract_salary();

				    		if(!empty($latest_salary)>0){

								    		if($latest_salary->end_date >= $issue_date){

								    			
										    			$latest_designation=$latest_contract->latest_contract_designation();

											    			if(!empty($latest_designation)>0){


													    			if($latest_designation->end_date >= $issue_date){

													    				$contracts[]=$Employee;

													    			}else{

													    				 //incomplete contract
												    	    	         $incomplete_contracts[]=$Employee;
													    			}
													    	}else{

												    	    	//incomplete contract
												    	    	 $incomplete_contracts[]=$Employee;
												    	    }

													  
								    		}else{

								    			//incomplete contract
												 $incomplete_contracts[]=$Employee;
								    		}
				    	    }else{

				    	    	//incomplete contract
				    	    	 $incomplete_contracts[]=$Employee;
				    	    }

				    	}else{

						    $closed_contracts[]=$Employee;
					    }



			    	}else{

			    		//expired contract
			    		$expired_contracts[]=$Employee;
			    	}
		}else{

			//no contract
			$non_contracts[]=$Employee;
		}

    }


    return [

		    'contracts'=> $contracts,
		    'non_contracts'=>$non_contracts,
		    'incomplete_contracts'=>$incomplete_contracts,
		    'closed_contracts'=>$closed_contracts,
		    'expired_contracts'=>$expired_contracts

		   ];

}


function employee_contract_status($employee_contract_id){
	$CI =& get_instance();
	$CI->load->model('Employee_contract');
	$Employee_contract= new Employee_contract();

	//active_contract
    //incomplete_contract
    //closed_contract
    //expired_contract

    $contract_status='';
    $issue_date=date('Y-m-d');
	
	$Employee_contract = $Employee_contract->get(1,0,['id'=>$employee_contract_id]);

	$Employee_contract = array_shift($Employee_contract);

		if($Employee_contract->end_date >= $issue_date){

            if(!$Employee_contract->employee_contract_close()){

                $latest_salary = $Employee_contract->latest_contract_salary();
                $latest_designation = $Employee_contract->latest_contract_designation();
                if($latest_salary && ($latest_salary->end_date >= $issue_date) && $latest_designation && ($latest_designation->end_date >= $issue_date)){
                    $contract_status='active_contract';
                }else{
                    $contract_status='incomplete_contract';
                }

            }else{

              $contract_status='closed_contract';
            }

		}else{
			
		   $contract_status='expired_contract';
		}	

	  return $contract_status;


}

