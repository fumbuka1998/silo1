<?php
class Asset_depreciation_rate extends MY_Model{

    const DB_TABLE = 'asset_depreciation_rates';
    const DB_TABLE_PK = 'id';

    public $start_date;
    public $created_by;
    public $created_at;

   
      public function depreciation_rates(){
           $rates = $this->get();
             return $rates;
       }

      public function depreciation_rate_items(){

            $this->load->model('Asset_depreciation_rate_item');
          $rate_items=new Asset_depreciation_rate_item();
          $rate_items= $rate_items->get(0,0,[
                'asset_depreciation_rate_id' => $this->{ $this::DB_TABLE_PK }
                ]);

            return $rate_items;
      }

      public function asset_group_list(){
          $this->load->model('Asset_group');
          $Asset_groups=new Asset_group();
          $Asset_groups= $Asset_groups->get();
            return $Asset_groups;
      }

     public function joined_asset_group($depreciation_rate_id)
        {
             $query=$this->db->query('SELECT * FROM asset_groups
                    WHERE id NOT IN (
                      SELECT asset_group_id FROM asset_depreciation_rate_items
                      where asset_depreciation_rate_id = '.$depreciation_rate_id.'
                    )');
             return $query->result();
        }

     public function initial_depreciation_rate($issue_date,$asset_group_id)
        {
                $this->db->select_max('asset_depreciation_rate_items.id')->from('asset_depreciation_rate_items');
                $this->db->join('asset_depreciation_rates','asset_depreciation_rate_items.asset_depreciation_rate_id=asset_depreciation_rates.id');
                $this->db->where('start_date <=',$issue_date);
                $this->db->where('asset_group_id',$asset_group_id);
                $query=$this->db->get();
                $sub_query = $this->db->last_query();
                $this->db->select()->from('asset_depreciation_rate_items');
                $this->db->join('asset_depreciation_rates','asset_depreciation_rate_items.asset_depreciation_rate_id=asset_depreciation_rates.id');
                $this->db->where("asset_depreciation_rate_items.id = ($sub_query)", NULL, FALSE);
                $query=$this->db->get();
                return $query->result();

        }

     public function next_depreciation_rate($issue_date,$asset_group_id)
        {
          
                $this->db->select()->from('asset_depreciation_rate_items');
                $this->db->join('asset_depreciation_rates','asset_depreciation_rate_items.asset_depreciation_rate_id=asset_depreciation_rates.id');
                $this->db->where('start_date >',$issue_date);
                $this->db->where('asset_group_id',$asset_group_id);
                $this->db->limit(1,0);
                $query=$this->db->get();
                return $query->result();

        }
   

}