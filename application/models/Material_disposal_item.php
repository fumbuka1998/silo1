<?php
class Material_disposal_item extends MY_Model{

    const DB_TABLE = 'material_disposal_items';
    const DB_TABLE_PK = 'id';

        public $disposal_id;
        public $material_item_id;
        public $sub_location_id;
        public $project_id;
        public $quantity;
        public $rate;
        public $remarks;

    public function material_item(){
        $this->load->model('Material_item');
        $material_item=new Material_item();
        $material_item->load($this->material_item_id);
        return $material_item;

    }

    public function sub_location()
    {
        $this->load->model('Sub_location');
        $sub_location = new Sub_location();
        $sub_location->load($this->sub_location_id);
        return $sub_location;
    }

    public  function project(){
            $this->load->model('Project');
            $project=new Project();
            $project->load($this->project_id);
            return $project;
    }

}