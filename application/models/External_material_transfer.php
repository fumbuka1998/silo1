<?php

class External_material_transfer extends MY_Model{
    
    const DB_TABLE = 'external_material_transfers';
    const DB_TABLE_PK = 'transfer_id';

    public $source_location_id;
    public $destination_location_id;
    public $transfer_date;
    public $project_id;
    public $comments;
    public $vehicle_number;
    public $driver_name;
    public $sender_id;
    public $status;

    public function transfer_number(){
        return 'EXT/'.add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function source()
    {
        $this->load->model('inventory_location');
        $source = new Inventory_location();
        $source->load($this->source_location_id);
        return $source;
    }

    public function destination()
    {
        $this->load->model('inventory_location');
        $destination = new Inventory_location();
        $destination->load($this->destination_location_id);
        return $destination;
    }

    public function sender()
    {
        $this->load->model('employee');
        $sender = new Employee();
        $sender->load($this->sender_id);
        return $sender;
    }

    public function grn()
    {
        $this->load->model('goods_received_note');
        $grn = new Goods_received_note();
        $grn->load($this->grn_id);
        return $grn;
    }

    public function clear_items(){

        $this->db->delete('external_material_transfer_items', array('transfer_id' => $this->{$this::DB_TABLE_PK}));
        $this->db->delete('external_transfer_asset_items', array('transfer_id' => $this->{$this::DB_TABLE_PK}));
    }

    public function material_items(){
        $this->load->model('external_material_transfer_item');
        $where['transfer_id'] = $this->{$this::DB_TABLE_PK};
        return $this->external_material_transfer_item->get(0,0,$where);
    }

    public function asset_items()
    {
        $this->load->model('external_transfer_asset_item');
        return $this->external_transfer_asset_item->get(0,0,['transfer_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function location_transfer_orders_list($location_id,$limit, $start, $keyword, $order){
        //order string
        $order_string = dataTable_order_string(['approved_date','requisition_id','location_name','approver_name'],$order,'approved_date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $where_clause = ' WHERE is_final = 1 AND status = "APPROVED" AND source_type = "store" ';
        $sql = 'SELECT * FROM (
                      SELECT requisition_approval_id FROM requisition_approval_material_items
                      LEFT JOIN requisition_approvals ON requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
                      LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                      '.$where_clause.'
                      
                      UNION
                      
                      SELECT requisition_approval_id FROM requisition_approval_asset_items
                      LEFT JOIN requisition_approvals ON requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
                        LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                      '.$where_clause.'
                ) AS artificial_table GROUP BY requisition_approval_id';

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();


        $sql = 'SELECT  SQL_CALC_FOUND_ROWS  * FROM (
                      SELECT requisitions.requisition_id, requisition_approval_id, destinations.location_id AS destination_id,
                       destinations.location_name  AS destination_name, projects.project_id, project_name,
                      approved_date, CONCAT(first_name," ",COALESCE(middle_name,"")," ",last_name) AS approver_name
                      FROM requisition_approval_material_items
                      LEFT JOIN requisition_approvals ON requisition_approval_material_items.requisition_approval_id = requisition_approvals.id
                      LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                      LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                      LEFT JOIN projects ON project_requisitions.project_id = projects.project_id
                      LEFT JOIN inventory_locations destinations ON projects.project_id = destinations.project_id
                      LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id
                      '.$where_clause.' AND requisition_approval_material_items.location_id = '.$location_id.'
                  
                      UNION
                      
                      SELECT requisitions.requisition_id, requisition_approval_id, destinations.location_id AS destination_id,
                       destinations.location_name  AS destination_name, projects.project_id, project_name,
                      approved_date, CONCAT(first_name," ",COALESCE(middle_name,"")," ",last_name) AS approver_name
                      FROM requisition_approval_asset_items
                      LEFT JOIN requisition_approvals ON requisition_approval_asset_items.requisition_approval_id = requisition_approvals.id
                      LEFT JOIN requisitions ON requisition_approvals.requisition_id = requisitions.requisition_id
                      LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                      LEFT JOIN projects ON project_requisitions.project_id = projects.project_id
                      LEFT JOIN inventory_locations destinations ON projects.project_id = destinations.project_id
                      LEFT JOIN employees ON requisition_approvals.created_by = employees.employee_id
                      '.$where_clause.' AND requisition_approval_asset_items.location_id = '.$location_id.'
                ) AS artificial_table GROUP BY requisition_approval_id '.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $this->load->model('Inventory_location');
        $Inventory_location= new Inventory_location();
        $Inventory_location->load($location_id);
        $data['sub_location_options'] = $Inventory_location->sub_location_options();
        $rows = [];
        foreach ($results as $row){
            $data['destination_id'] = $row->destination_id;
            $data['requisition_id'] = $row->requisition_id;
            $data['requisition_approval_id'] = $row->requisition_approval_id;
            $data['project_id'] = $row->requisition_approval_id;
            $data['transfer_order'] = $row;
            $data['location_id'] = $location_id;
            $data['material_items'] = $this->transfer_order_material_items($row->requisition_approval_id,$location_id);
            $data['asset_items'] = $this->transfer_order_asset_items($row->requisition_approval_id,$location_id);
            $data['transferable'] = $this->transferable($row->requisition_approval_id,$location_id);
            $rows[] = [
                custom_standard_date($row->approved_date),
                add_leading_zeros($row->requisition_id),
                $row->destination_name,
                $row->project_name,
                $row->approver_name,
                $this->load->view('inventory/material/transfer_orders/transfer_order_list_actions',$data,true)
            ];
        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function location_material_transfers_list($location_id,$limit, $start, $keyword, $order){
        //order string
        $order_string = dataTable_order_string(['type','transfer_date','transfer_id','source_name','destination_name','status'],$order,'transfer_date');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $external_where_clause = '  WHERE (source_location_id = '.$location_id.' OR destination_location_id = '.$location_id.') ';
        $internal_where_clause = ' WHERE location_id = '.$location_id;
        $sql = 'SELECT transfer_id FROM external_material_transfers '.$external_where_clause.' UNION ALL 
                SELECT transfer_id FROM internal_material_transfers '.$internal_where_clause;

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        if ($keyword != '') {
            $external_where_clause .= '
                AND (
                    transfer_id LIKE "%' . $keyword . '%" OR transfer_date LIKE "%' . $keyword . '%" OR status LIKE "%' . $keyword . '%" OR sources.location_name LIKE "%' . $keyword . '%"
                     OR destinations.location_name LIKE "%' . $keyword . '%" OR external_material_transfers.comments LIKE "%' . $keyword . '%"
                )
            ';

            $internal_where_clause .= '
                AND (transfer_id LIKE "%' . $keyword . '%" OR comments LIKE "%' . $keyword . '%")
            ';
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS  * FROM (SELECT "EXTERNAL" AS type, external_material_transfers.status,
                external_material_transfers.transfer_date,
                external_material_transfers.transfer_id,
                external_material_transfers.destination_location_id,
                external_material_transfers.source_location_id,
                sources.location_name AS source_name,
                destinations.location_name AS destination_name
                FROM external_material_transfers
                LEFT JOIN inventory_locations sources ON external_material_transfers.source_location_id = sources.location_id
                LEFT JOIN inventory_locations destinations ON external_material_transfers.destination_location_id = destinations.location_id
                ' . $external_where_clause . '
                 
                 UNION ALL 
                 
                 SELECT "INTERNAL" AS type,"N/A" AS status,transfer_date,transfer_id, "N/A" AS destination_location_id, "N/A" AS source_location_id,
                 "N/A" AS source_name, "N/A" AS destination_name
                 FROM internal_material_transfers
                ' . $internal_where_clause . '
                
                ) temporary_table '.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $data['location_id'] = $location_id;
        $rows = [];
        $this->load->model(['internal_material_transfer']);
        foreach ($results as $row) {
            $status_label_class = 'label label-';
            if ($row->status == 'ON TRANSIT') {
                $status_label_class .= 'info';
            } else if ($row->status == 'RECEIVED') {
                $status_label_class .= 'success';
            } else {
                $status_label_class .= 'danger';
            }
            $transfer = $row->type == 'EXTERNAL' ? new self : new Internal_material_transfer();
            $transfer->load($row->transfer_id);
            $data['project'] = $transfer->project();
            $data['transfer_type'] = $row->type;
            $data['transfer'] = $transfer;
            $data['destination_id'] = $row->destination_location_id;
            $status = $row->status;
            if($row->type == 'EXTERNAL'){
                $data['receivable'] = $transfer->receivable();
                $status = $status == 'RECEIVED' && $data['receivable'] ? 'PARTIAL RECEIVED' : $row->status;
            }
            $rows[] = [
                $row->type,
                custom_standard_date($row->transfer_date),
                $transfer->transfer_number(),
                check_permission('Inventory') && $row->type == 'EXTERNAL' ? anchor(base_url('inventory/location_profile/' . $row->source_location_id), $row->source_name) : $row->source_name,
                check_permission('Inventory') && $row->type == 'EXTERNAL' ? anchor(base_url('inventory/location_profile/' . $row->destination_location_id), $row->destination_name) : $row->destination_name,
                $row->type == 'EXTERNAL' ? '<span class="' . $status_label_class . '">' . $status . '</span>' : $status,
                $this->load->view('inventory/material/transfers/material_transfers_list_actions', $data, true)
            ];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];

        return json_encode($json);
    }

    public function transferable($requisition_approval_id,$location_id){

        $sql1 = 'SELECT COALESCE(SUM(approved_quantity),0) FROM requisition_approval_material_items
                 WHERE source_type = "store" AND requisition_approval_id = '.$requisition_approval_id.' AND location_id = '.$location_id;

        $sql2 = 'SELECT COALESCE(SUM(quantity),0) FROM external_material_transfer_items
                LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                LEFT JOIN transferred_transfer_orders ON external_material_transfers.transfer_id = transferred_transfer_orders.transfer_id
                WHERE source_location_id = '.$location_id.' AND transferred_transfer_orders.requisition_approval_id = '.$requisition_approval_id;

        $sql3 = 'SELECT COALESCE(SUM(approved_quantity),0) FROM requisition_approval_asset_items
                 WHERE location_id = '.$location_id.' AND requisition_approval_id = '.$requisition_approval_id.' AND source_type = "store"
                 ';

        $sql4 = 'SELECT COUNT(external_transfer_asset_items.id) FROM external_transfer_asset_items
                 LEFT JOIN external_material_transfers ON external_transfer_asset_items.transfer_id = external_material_transfers.transfer_id
                 LEFT JOIN transferred_transfer_orders ON external_material_transfers.transfer_id = transferred_transfer_orders.transfer_id
                 WHERE source_location_id = '.$location_id.' AND transferred_transfer_orders.requisition_approval_id = '.$requisition_approval_id.'
                 ';

        $sql = 'SELECT ((('.$sql1.')+('.$sql3.'))-(('.$sql2.')+('.$sql4.'))) AS order_balance';
        $query = $this->db->query($sql);
        return $query->row()->order_balance > 0;
    }

    public function transfer_order_material_items($requisition_approval_id,$location_id){
        $sql = 'SELECT material_item_id, requisition_approval_material_items.id AS approval_item_id FROM requisition_material_items
                LEFT JOIN requisition_approval_material_items ON requisition_material_items.id = requisition_approval_material_items.requisition_material_item_id
                LEFT JOIN inventory_locations ON requisition_approval_material_items.location_id = inventory_locations.location_id
                WHERE requisition_approval_material_items.source_type = "store" AND inventory_locations.location_id = '.$location_id.' AND requisition_approval_id = '.$requisition_approval_id;
        $query = $this->db->query($sql);
        $results =  $query->result();
        $this->load->model(['material_item','requisition_approval_material_item']);
        $items = [];
        foreach ($results as $row){
            $material_item = new Material_item();
            $material_item->load($row->material_item_id);
            $requisition_approval_material_item = new Requisition_approval_material_item();
            $requisition_approval_material_item->load($row->approval_item_id);
            $items[] = [
                'approval_item' => $requisition_approval_material_item,
                'material_item' => $material_item
            ];
        }
        return $items;
    }

    public function transfer_order_asset_items($requisition_approval_id,$location_id){
        $sql = 'SELECT asset_item_id, requisition_approval_asset_items.id AS approval_item_id FROM requisition_asset_items
                LEFT JOIN requisition_approval_asset_items ON requisition_asset_items.id = requisition_approval_asset_items.requisition_asset_item_id
                LEFT JOIN inventory_locations ON requisition_approval_asset_items.location_id = inventory_locations.location_id
                WHERE requisition_approval_asset_items.source_type = "store" AND inventory_locations.location_id = '.$location_id.' AND requisition_approval_id = '.$requisition_approval_id;
        $query = $this->db->query($sql);
        $results =  $query->result();
        $this->load->model(['asset_item','requisition_approval_asset_item']);
        $items = [];
        foreach ($results as $row){
            $asset_item = new Asset_item();
            $asset_item->load($row->asset_item_id);
            $requisition_approval_material_item = new Requisition_approval_asset_item();
            $requisition_approval_material_item->load($row->approval_item_id);
            $items[] = [
                'approval_item' => $requisition_approval_material_item,
                'asset_item' => $asset_item
            ];
        }
        return $items;
    }

    public function receivable(){
        $sql = 'SELECT * FROM (
                        SELECT COUNT(external_transfer_asset_items.id) AS quantity, (
                            SELECT COUNT(asset_sub_location_histories.id) FROM asset_sub_location_histories WHERE asset_sub_location_histories.id IN (
                              SELECT asset_sub_location_history_id FROM grn_asset_sub_location_histories WHERE grn_id IN (
                                SELECT grn_id FROM external_material_transfer_grns WHERE transfer_id = ' . $this->{$this::DB_TABLE_PK} . '
                              )
                            )
                        ) AS received_quantity 
                        FROM external_transfer_asset_items WHERE transfer_id = '.$this->{$this::DB_TABLE_PK}.'
                        
                        UNION
                        SELECT quantity,(
                            SELECT COALESCE(SUM(quantity),0) FROM material_stocks WHERE stock_id IN (
                              SELECT stock_id FROM goods_received_note_material_stock_items WHERE grn_id IN (
                                SELECT grn_id FROM external_material_transfer_grns WHERE transfer_id = ' . $this->{$this::DB_TABLE_PK} . '
                              )
                            ) AND material_stocks.item_id = external_material_transfer_items.material_item_id
                        ) AS received_quantity 
                        FROM external_material_transfer_items WHERE transfer_id = '.$this->{$this::DB_TABLE_PK}.'
                ) AS artificial_table 
                WHERE (quantity - received_quantity) > 0 LIMIT 1';

        $query = $this->db->query($sql);
        return $query->num_rows() > 0;
    }

}

