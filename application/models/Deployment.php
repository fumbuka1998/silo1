<?php
class Deployment extends MY_Model {
    const DB_TABLE = 'deployments';
    const DB_TABLE_PK = 'id';
    public $name;
    public $departure_time;
    public $arrival_time;
    public $registration_number;
    public $driver;
    public $relax_station;
    public $created_by;

    public function created_by(){
        $this->load->model('employee');
        $created_by = new Employee();
        $created_by->load($this->created_by);
        return $created_by;
    }

    public function deployment_attachment(){
      $this->load->model('deployment_attachment');
      $attachment = $this->deployment_attachment->get(1,0,['deployment_id' => $this->{$this::DB_TABLE_PK}]);
      return !empty($attachment) ? array_shift($attachment) : '' ;
    }

    public function deployment_attachments(){
        $this->load->model('deployment_attachment');
        return $this->deployment_attachment->get(0,0,['deployment_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function delete_deployment_category_parameter(){
        $this->db->where('deployment_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete('deployment_category_parameters');
    }

    public function delete_deployment_erson(){
        $this->db->where('deployment_id', $this->{$this::DB_TABLE_PK});
        $this->db->delete('deployment_persons');
    }

    public function deployment_category_parameters(){
        $this->load->model('deployment_category_parameter');
        return $this->deployment_category_parameter->get(0,0,['deployment_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function deployment_category_parameter(){
        $this->load->model('deployment_category_parameter');
        $parameter = $this->deployment_category_parameter->get(1,0,['deployment_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($parameter) ? array_shift($parameter) : '';
    }

    public function deployment_persons(){
        $this->load->model('deployment_person');
        return $this->deployment_person->get(0,0,['deployment_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function deployment_person(){
        $this->load->model('deployment_person');
        $deployment_person = $this->deployment_person->get(1,0,['deployment_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($deployment_person) ? array_shift($deployment_person) : '';
    }

    public function deployments_list($limit, $start, $keyword, $order){
        $order_string = dataTable_order_string(['name','departure_time'],$order,'departure_time');

        $where = '';
        if($keyword != ''){
            $where = ' name LIKE "%'.$keyword.'%" OR departure_time LIKE "%'.$keyword.'%"';
        }

        $deployments = $this->get($limit, $start, $where,$order_string);
        $rows = array();
        foreach ($deployments as $deployment) {
            $data['deployment'] = $deployment;
            $rows[] = array(
                $deployment->name,
                $deployment->departure_time,
                $deployment->arrival_time,
                $deployment->registration_number,
                $deployment->driver,
                $this->load->view('hse/deployments/deployments_list_actions',$data,true)
            );
        }
        $data['data']=$rows;
        $data['recordsFiltered']=$this->count_rows($where);
        $data['recordsTotal']=$this->count_rows();
        return json_encode($data);

    }
}