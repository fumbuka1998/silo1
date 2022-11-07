<?php

class Stakeholder_evaluation_factor extends MY_Model
{

    const DB_TABLE = 'stakeholder_evaluation_factors';
    const DB_TABLE_PK = 'id';

    public $general_experience;
    public $certificate_of_completion;
    public $two_team_supervisors_with_atleast_a_bachelor_degree;
    public $financial_capacity_of_at_least_payment_of_workers_for_one_month;
    public $proof_of_training_of_casual_laborers;

    function get_enum_values($choice = false)
    {
        $options[''] = '&nbsp;';
        if($choice){
            $type = $this->db->query( "SHOW COLUMNS FROM stakeholder_evaluation_factors WHERE Field = '$choice'" )->row( 0 )->Type;
            preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
            $enum = explode("','", $matches[1]);
            foreach ($enum as $item){
                $options[$item] =$item;
            }
        }else{
            $options[0] ='NO';
            $options[1] ='YES';
        }
        return $options;
    }

    public function factor_to_points($choice)
    {
       switch ($choice){
           case "Registered 1-2 years":
               return 5;
               break;
           case "Registered 2-3 years":
               return 10;
               break;
           case "Registered 3 years and above":
               return 15;
               break;
           case "1 Certificate From a recognised Institution":
               return 10;
               break;
           case "2 Certificate from a recognised Institution":
               return 20;
               break;
           case "1 Certificate from a non-recognised Institution":
               return 5;
               break;
           case "2 Certificate from a non-recognised institution":
               return 10;
               break;
           case "1 Supervisor within relevant field":
               return 15;
               break;
           case "2 Supervisor within relevant field":
               return 30;
               break;
           case "1 Supervisor not in relevant field":
               return 5;
           case "Contract amounts under 5 Million":
               return 2;
               break;
           case "Contracts amounts between 5 and 20 Million":
               return 3.5;
               break;
           case "Contract amounts above 20 Million":
               return 5;
               break;
           case "1":
               return 30;
               break;
           default:
               return 0;

       }
    }

    public function load_stakeholder_factor_and_points($id)
    {
        $this->load->model(['stakeholder_evaluation_score','stakeholder']);
        $stakeholder = new Stakeholder();
        $stakeholder->load($id);
        $evaluation_score = $this->stakeholder_evaluation_score->get(1,0, ['stakeholder_id' => $id]);
        if ($evaluation_score) {
            $found_score = array_shift($evaluation_score);
            $found_id = $found_score->stakeholder_evaluation_factor_id;

            $data2 = [];
            $evaluation_factor = new Stakeholder_evaluation_factor();
            $evaluation_factor->load($found_id);
            $total = 0;
            foreach ($evaluation_factor as $factor){
				if ($this->factor_to_points($factor) != '') {
					$data2[] = $this->factor_to_points($factor);
				}else{
					$data2[] = 0;
				}
				$eval_factor[] = $factor;
				$total += $this->factor_to_points($factor);
            }
            $stakeholders_data['evaluation_point'] = $data2;
            $stakeholders_data['total_points'] = $total;
            $stakeholders_data['evaluation_factor'] = $eval_factor;
            return $stakeholders_data;
        }
    }

}
