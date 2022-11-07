<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 12/17/2018
 * Time: 5:46 PM
 */

class Enquiry extends MY_Model
{
    const DB_TABLE = 'enquiries';
    const DB_TABLE_PK = 'id';

    public $enquiry_date;
    public $enquiry_to;
    public $enquiry_for;
    public $project_id;
    public $cost_center_id;
    public $required_date;
    public $comments;
    public $created_by;
    public $status;



    public function enquiry_number(){
        return 'EN/' . add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function enquiry_to(){
        $this->load->model('stakeholder');
        $stakeholder = new Stakeholder();
        $stakeholder->load($this->enquiry_to);
        return $stakeholder;
    }

    public function delete_items(){
        $this->db->delete('enquiry_material_items',['enquiry_id'=>$this->{$this::DB_TABLE_PK}]);
        $this->db->delete('enquiry_asset_items',['enquiry_id'=>$this->{$this::DB_TABLE_PK}]);
        $this->db->delete('enquiry_service_items',['enquiry_id'=>$this->{$this::DB_TABLE_PK}]);
    }

    public function enquiries_list($limit, $start, $keyword, $order)
    {
        $order_string = dataTable_order_string(['enquiry_date', 'enquiries.id', 'required_date'], $order, 'enquiry_date');
        $filter = $this->input->post('filter');
        if($filter == "ALL") {
            $where = '';
        } else if($filter == "requested") {
            $where = ' status = "REQUESTED" ';
        } else {
            $where = ' status = "PENDING" ';
        }
        $records_total = $this->count_rows($where);

        if ($keyword != '') {
            $where = ( $where == '' ? ' WHERE' : ''.$where.' AND ' ). ' (enquiry_date LIKE "%' . $keyword . '%" OR enquiries.id LIKE "%' . $keyword . '%" OR required_date LIKE "%' . $keyword . '%") ';
        }

        $enquiries = $this->enquiry->get($limit, $start, $where, $order_string);

        $this->load->model(['stakeholder','asset_item']);
        $data['material_options'] = material_item_dropdown_options('all');
        $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
        $data['asset_options'] = $this->asset_item->dropdown_options();
        $rows = [];
        foreach ($enquiries as $enquiry) {
            $data['enquiry'] = $enquiry;
            $rows[] = [
                custom_standard_date($enquiry->enquiry_date),
                $enquiry->enquiry_number(),
                $enquiry->enquiry_to()->stakeholder_name,
                'Enquiry For',
                $enquiry->required_date,
                $this->load->view('requisitions/enquiries/list_actions',$data,true)
            ];
        }
        $records_filtered = $this->count_rows($where);
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);
    }

    public function material_items(){
        $this->load->model('enquiry_material_item');
        $where['enquiry_id'] = $this->{$this::DB_TABLE_PK};
        return $this->enquiry_material_item->get(0,0,$where);
    }

    public function asset_items(){
        $this->load->model('enquiry_asset_item');
        return $this->enquiry_asset_item->get(0,0,['enquiry_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function service_items(){
        $this->load->model('enquiry_service_item');
        $where['enquiry_id'] = $this->{$this::DB_TABLE_PK};
        return $this->enquiry_service_item->get(0,0,$where);
    }

}
