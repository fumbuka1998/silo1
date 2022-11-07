<?php
class Tax_table extends MY_Model{

    const DB_TABLE = 'tax_tables';
    const DB_TABLE_PK = 'id';

    public $start_date;
    public $end_date;
    public $created_at;
    public $created_by;


    public function tax_table_rates()
    {
        $rates = $this->get();
        return $rates;
    }
    
    public function rate_list(){

        $rates = $this->get(0,0,['id'=>$this::DB_TABLE_PK]);
               foreach ($rates as $rate) {
                   $data['rates_list'] = $rate;
                   $this->load->view('settings/tax_tables/tax_tables_list_actions', $data);
               }
        }

    public function tax_rate_items(){
        $this->load->model('Tax_table_item');
        $items = $this->Tax_table_item->get(0,0,[
            'tax_table_id' => $this->{ $this::DB_TABLE_PK }
        ]);

        return $items;
    }

    public function tax_table_rate_items(){

        $this->load->model('tax_table_item');
        $items = $this->tax_table_item->get(0,0,['tax_table_id' => $this->{$this::DB_TABLE_PK}]);
        inspect_object($items); exit;

        return $items;
    }

    public function initial_tax_table_rate($issue_date,$tax_table_id)
    {

        $this->db->select_max('tax_table_items.id')->from('tax_table_items');
        $this->db->join('tax_tables','tax_table_items.tax_table_id=tax_tables.id');
        $this->db->where('start_date <=',$issue_date);
        $this->db->where('tax_table_id',$tax_table_id);
        $query=$this->db->get();
        $sub_query = $this->db->last_query();
        $this->db->select()->from('tax_table_items');
        $this->db->join('tax_tables','tax_table_items.tax_table_id=tax_tables.id');
        $this->db->where("tax_table_items.id = ($sub_query)", NULL, FALSE);
        $query=$this->db->get();
        return $query->result();

    }
    
    public function last_inserted_id()
    {

        $this->db->select_max('id')->from('tax_tables');
        $last_id=$this->db->get();

        return $last_id->result();

    }
    
    public function next_tax_table_rate($issue_date,$tax_table_id)
    {

        $this->db->select()->from('tax_table_items');
        $this->db->join('tax_tables','tax_table_items.tax_table_id=tax_tables.id');
        $this->db->where('start_date >',$issue_date);
        $this->db->where('tax_table_id',$tax_table_id);
        $this->db->limit(1,0);
        $query=$this->db->get();
        return $query->result();

    }

    public function paye_formula($taxable_amount, $minimum_group_amount, $group_rate, $group_addition_amount)
    {
       return (($taxable_amount - $minimum_group_amount)*($group_rate/100)+$group_addition_amount);
    }


    



}