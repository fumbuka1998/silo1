<?php
class Unprocured_delivery extends MY_Model{
    const DB_TABLE = 'unprocured_deliveries';
    const DB_TABLE_PK = 'delivery_id';

    public $location_id;
    public $client_id;
    public $delivery_date;
    public $delivery_for;
    public $currency_id;
    public $comments;
    public $receiver_id;

    public function delivery_number(){
        return 'PD/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function project(){
        $this->load->model('project');
        $project = new Project();
        $project->load($this->delivery_for);
        return $project;
    }
}
