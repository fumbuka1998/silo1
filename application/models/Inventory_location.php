<?php

class Inventory_location extends MY_Model{
    
    const DB_TABLE = 'inventory_locations';
    const DB_TABLE_PK = 'location_id';

    public $location_name;
    public $project_id;
    public $description;

    public function project()
    {
        $this->load->model('project');
        $project = new project();
        $has_project = $project->load($this->project_id);
        return $has_project ? $project : false;
    }

    public function sub_locations($keyword = ''){
        $this->load->model('sub_location');
        $where = ' location_id = '.$this->{$this::DB_TABLE_PK}.' AND status = "ACTIVE"';
        if($keyword != ''){
            $where .= ' AND sub_location_name LIKE "%'.$keyword.'%"';
        }
        return $this->sub_location->get(0,0,$where);
    }

    public function sub_location_options(){
        $this->load->model('sub_location');
        $where = [
            'location_id'=>$this->{$this::DB_TABLE_PK},
            'status'=>'ACTIVE'
        ];
        $sub_locations = $this->sub_location->get(0,0,$where);
        $options[''] = '&nbsp;';
        foreach($sub_locations as $sub_location){
            $options[$sub_location->{$sub_location::DB_TABLE_PK}] = $sub_location->sub_location_name;
        }
        return $options;
    }

    public function equipment_sub_locations()
    {
        $this->load->model('sub_location');
        $where = ' location_id = ' . $this->{$this::DB_TABLE_PK} . ' AND status = "ACTIVE"  AND equipment_id IS NOT NULL';
        return $this->sub_location->get(0, 0, $where);
    }

    public function equipment_sub_location_options()
    {
        $this->load->model('sub_location');
        $where = ' location_id = ' . $this->{$this::DB_TABLE_PK} . ' AND status = "ACTIVE"  AND equipment_id IS NOT NULL';
        $sub_locations = $this->sub_location->get(0, 0, $where);
        $options[''] = '&nbsp;';
        foreach ($sub_locations as $sub_location) {
            $options[$sub_location->{$sub_location::DB_TABLE_PK}] = $sub_location->sub_location_name;
        }
        return $options;
    }

    public function sub_location_dropdown_options(){
        $this->load->model('sub_location');
        $sub_locations = $this->sub_location->get(0,0,['location_id' => $this->{$this::DB_TABLE_PK}]);
        $options ='<option  value="">&nbsp;</option>';
        foreach($sub_locations as $sub_location){
            $options .='<option  value="'.$sub_location->sub_location_id.'">'.$sub_location->sub_location_name.'</option>';
        }

        return $options;
    }

    public function allowed_access(){
        if(($this->project_id == null && check_permission('Inventory')) || check_permission('Administrative Actions') || check_permission('All Projects')) {
            return true;
        } else {
            $sql = 'SELECT project_team_members.project_id FROM project_team_members
                  WHERE employee_id = "'.$this->session->userdata('employee_id').'" AND project_id = "'.$this->project_id.'"
                  ';
            $query = $this->db->query($sql);
            $num_rows = $query->num_rows();
            return ($num_rows > 0);
        }
    }

    public function locations_list($limit, $start, $keyword, $order){
        //order string
        $order_string = dataTable_order_string(['location_name','project_name','description'],$order,'location_name');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        //Where clause
        $where_clause = '( inventory_locations.project_id IS NULL OR inventory_locations.project_id IN ( SELECT project_id FROM project_team_members WHERE employee_id = "'.$this->session->userdata('employee_id').'") ';
        $where_clause .= ' OR (
                              SELECT COUNT(user_permission_privileges.permission_privilege_id) FROM user_permission_privileges
                                LEFT JOIN users_permissions ON user_permission_privileges.user_permission_id = users_permissions.user_permission_id
                                LEFT JOIN users ON users_permissions.user_id = users.user_id
                                LEFT JOIN permission_privileges ON user_permission_privileges.permission_privilege_id = permission_privileges.id
                              WHERE privilege = "All Locations" AND users.employee_id ='. $this->session->userdata('employee_id').'
                              ) > 0
                                OR '. $this->session->userdata('employee_id').' IN (
                                    SELECT projects.created_by FROM projects WHERE project_id = inventory_locations.project_id
                            )
                          )';
        //Total records
        $records_total = $this->count_rows($where_clause);

        //Get results
        if($keyword != ''){
            $where_clause .= ' AND (location_name LIKE "%'.$keyword.'%" OR inventory_locations.description LIKE "%'.$keyword.'%" OR project_name LIKE "%'.$keyword.'%")';
        }
        $sql = 'SELECT SQL_CALC_FOUND_ROWS location_id,inventory_locations.description, inventory_locations.project_id, location_name, project_name
                    FROM inventory_locations
                    LEFT JOIN projects ON inventory_locations.project_id = projects.project_id WHERE '.$where_clause.$order_string;

        $query = $this->db->query($sql);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        //Prepare rows
        $rows = [];
        foreach($results as $row){
            $rows[] = [
                anchor(base_url('inventory/location_profile/' . $row->location_id), $row->location_name),
                check_privilege('All Locations') && $row->project_name != null ? anchor(base_url('projects/profile/' . $row->project_id), $row->project_name) : $row->project_name,
                $row->description,
                '<span class="pull-right">' . anchor(base_url('inventory/location_profile/' . $row->location_id), '<i class="fa fa-eye"></i> Open', ' class="btn btn-default btn-xs"')
                //'
                     //<button location_id="' . $row->location_id . '" class="btn btn-danger btn-xs delete_location_button"><i class="fa fa-trash"></i> Delete</button></span>'
            ];
        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function dropdown_options($category = null,$just_an_array = false){
        $options[''] = '&nbsp;';
        $where = [];
        if($category == 'main'){
            $where = ' project_id IS NULL ';
        }
        $locations = $this->get(0,0,$where);
        if(!$just_an_array) {
            foreach ($locations as $location) {
                $options[$location->{$location::DB_TABLE_PK}] = $location->location_name;
            }
            return $options;
        } else {
            return $locations;
        }
    }

    public function sub_location_ids_query(){
        return 'SELECT sub_location_id FROM sub_locations WHERE location_id = '.$this->{$this::DB_TABLE_PK};
    }

    public function grns($from = null,$to = null){
        $this->load->model('goods_received_note');
        $where = [
            'location_id' => $this->{$this::DB_TABLE_PK},
        ];
        if(!is_null($from)){
            $where[' receive_date >= '] = $from;
        }
        if(!is_null($to)){
            $where[' receive_date <= '] = $to;
        }
        return $this->goods_received_note->get(0,0,$where);
    }

    public function material_item_balance_value($material_item,$project_id = 'all',$date = null){
        $sub_locations = $this->sub_locations();
        $balance_value = 0;
        foreach ($sub_locations as $sub_location){
            $balance_value += $sub_location->material_item_balance_value($material_item,$project_id,$date);
        }
        return $balance_value;
    }

    public function total_material_balance_value($project_id = 'all',$date = null){
        $sub_locations = $this->sub_locations();
        $total_value = 0;
        foreach ($sub_locations as $sub_location){
            $total_value += $sub_location->total_material_balance_value($project_id,$date);
        }
        return $total_value;
    }

    public function project_total_material_balance_value($project_id = 'all',$date = null){
        $location_id = $this->{$this::DB_TABLE_PK};

        $sql = '
          SELECT COALESCE(SUM(location_material_balance * latest_average_price)) AS material_balance_value FROM (
              SELECT DISTINCT
                main_table.item_id,
                (
                  SELECT (
                           ( 
                                SELECT (
                                     (
                                       SELECT COALESCE(SUM(quantity), 0)
                                       FROM material_stocks
                                       WHERE item_id = main_table.item_id
                                             AND sub_location_id IN (
                                         SELECT sub_location_id
                                         FROM sub_locations
                                         WHERE location_id = "'.$location_id.'"
                                       )
                                         AND date_received <= "'.$date.'" ';
                if(strtolower($project_id) != 'all'){
                    $sql .= ' AND project_id '.(is_null($project_id) ? ' IS NULL ' : ' = '.$project_id.'');
                }

                $sql .= '
                                     )
                               ) AS received_quantity
                           ) - (
                             (
                               (
                                 SELECT COALESCE(SUM(quantity), 0)
                                 FROM material_stocks
                                 LEFT JOIN internal_material_transfer_items ON material_stocks.stock_id = internal_material_transfer_items.stock_id
                                 WHERE internal_material_transfer_items.source_sub_location_id IN (
                                   SELECT sub_location_id
                                   FROM sub_locations
                                   WHERE location_id = "'.$location_id.'"
                                 )
                                 AND material_stocks.item_id = main_table.item_id
                                 AND date_received <= "'.$date.'" ';
                if(strtolower($project_id) != 'all'){
                    $sql .= ' AND material_stocks.project_id '.(is_null($project_id) ? ' IS NULL ' : ' = '.$project_id.'');
                }

                $sql .= '
                               ) + (
                                 SELECT COALESCE(SUM(quantity), 0)
                                 FROM external_material_transfer_items
                                 LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                                 WHERE source_sub_location_id IN (
                                   SELECT sub_location_id
                                   FROM sub_locations
                                   WHERE location_id = "'.$location_id.'"
                                 )
                                 AND external_material_transfers.status != "CANCELLED"
                                 AND material_item_id = main_table.item_id
                                 AND transfer_date <= "'.$date.'" ';
                if(strtolower($project_id) != 'all'){
                    $sql .= ' AND external_material_transfers.project_id '.(is_null($project_id) ? ' IS NULL ' : ' = '.$project_id.'');
                }

                $sql .= '
                               )
                             ) + (
                               SELECT COALESCE(SUM(quantity), 0) AS used_quantity
                               FROM material_costs
                               WHERE source_sub_location_id IN (
                                 SELECT sub_location_id
                                 FROM sub_locations
                                 WHERE location_id = "'.$location_id.'"
                               )
                                     AND material_item_id = main_table.item_id
                                     AND cost_date <= "'.$date.'" ';
                if(strtolower($project_id) != 'all'){
                    $sql .= ' AND project_id '.(is_null($project_id) ? ' IS NULL ' : ' = '.$project_id.'');
                }

                $sql .= '        
                             ) + (
                               SELECT COALESCE(SUM(quantity), 0) AS sold_quantity
                               FROM stock_sales_material_items
                                 LEFT JOIN stock_sales ON stock_sales_material_items.stock_sale_id = stock_sales.id
                               WHERE source_sub_location_id IN (
                                 SELECT sub_location_id
                                 FROM sub_locations
                                 WHERE location_id = "'.$location_id.'"
                               )
                                     AND material_item_id = main_table.item_id
                                     AND sale_date <= "'.$date.'" ';
                if(strtolower($project_id) != 'all'){
                    $sql .= ' AND project_id '.(is_null($project_id) ? ' IS NULL ' : ' = '.$project_id.'');
                }

                $sql .= ' 
                             ) + (
                               SELECT COALESCE(SUM(quantity), 0) AS disposed_quantity
                               FROM material_disposal_items
                                 LEFT JOIN material_disposals ON material_disposal_items.disposal_id = material_disposals.id
                               WHERE sub_location_id IN (
                                 SELECT sub_location_id
                                 FROM sub_locations
                                 WHERE location_id = "'.$location_id.'"
                               )
                                     AND material_item_id = main_table.item_id
                                     AND disposal_date <= "'.$date.'" ';
                if(strtolower($project_id) != 'all'){
                    $sql .= ' AND material_disposals.project_id '.(is_null($project_id) ? ' IS NULL ' : ' = '.$project_id.'');
                }

                $sql .= ' 
                             ) + (
                               SELECT COALESCE(SUM(quantity), 0) AS assigned_out_quantity
                               FROM material_stocks
                               LEFT JOIN material_cost_center_assignment_items ON material_stocks.stock_id = material_cost_center_assignment_items.stock_id
                               WHERE material_cost_center_assignment_items.material_cost_center_assignment_id IN (
                                 SELECT id
                                 FROM material_cost_center_assignments
                                 WHERE assignment_date <= "'.$date.'" ';
                if(strtolower($project_id) != 'all'){
                    $sql .= ' AND source_project_id '.(is_null($project_id) ? ' IS NULL ' : ' = '.$project_id.'');
                }

                $sql .= ' 
                               )
                                     AND material_stocks.sub_location_id IN (
                                 SELECT sub_location_id
                                 FROM sub_locations
                                 WHERE location_id = "'.$location_id.'"
                               )
                                     AND material_stocks.item_id = main_table.item_id
                             )
                           )
                         ) AS quantity_available
                ) AS location_material_balance,
                
                (
                  SELECT COALESCE(average_price, 0)
                  FROM material_average_prices
                  WHERE transaction_date <= "'.$date.'"
                        AND material_item_id = main_table.item_id
                        AND sub_location_id IN (
                        SELECT sub_location_id
                        FROM sub_locations
                        WHERE location_id = "'.$location_id.'"
                      )';
                if(strtolower($project_id) != 'all'){
                    $sql .= ' AND project_id '.(is_null($project_id) ? ' IS NULL ' : ' = '.$project_id.'');
                }

                $sql .= ' 
                  ORDER BY average_price_id DESC
                  LIMIT 1
                ) AS latest_average_price
                
          FROM material_stocks AS main_table ';
        if(strtolower($project_id) != 'all'){
        $sql .= ' WHERE project_id '.(is_null($project_id) ? ' IS NULL ' : ' = '.$project_id.'');
        }
        $sql .= ' 
                ) AS location_material_balance_values
        ';

        $query = $this->db->query($sql);
        return $query->row()->material_balance_value;
    }

    public function total_material_item_quantity($project_id, $material_item){
        $total_quantity = $material_item->location_balance($project_id,$this->location_id);
        return $total_quantity;
    }

    public function total_asset_item_quantity($project_id, $asset_item){
        $this->load->model(['sub_location','asset']);
        $sub_location_ids = $this->sub_location_ids_query();
        $total_quantity = $asset_item->sub_location_available_stock($sub_location_ids,$project_id ,true);
        return $total_quantity;
    }

    public function cost_center_assignments($source_id = "all", $destination_id = "all", $from = '', $to = ''){
        $sql = 'SELECT item_name, symbol, sources.project_name AS Source, destinations.project_name AS Destination, COALESCE(SUM(quantity),0) AS quantity, material_stocks.price FROM material_cost_center_assignment_items
                LEFT JOIN material_cost_center_assignments ON material_cost_center_assignment_items.material_cost_center_assignment_id = material_cost_center_assignments.id
                LEFT JOIN material_stocks ON material_cost_center_assignment_items.stock_id = material_stocks.stock_id
                LEFT JOIN material_items ON material_stocks.item_id = material_items.item_id
                LEFT JOIN measurement_units ON material_items.unit_id = measurement_units.unit_id
                LEFT JOIN projects AS sources ON material_cost_center_assignments.source_project_id = sources.project_id
                LEFT JOIN projects AS destinations ON material_cost_center_assignments.destination_project_id = destinations.project_id
                WHERE assignment_date >= "'.$from.'" AND assignment_date <= "'.$to.'" ';

        if($source_id != "all"){
            $sql .= ' AND sources.project_id '.(is_null($source_id) ? ' IS NULL ' : ' = '.$source_id).' ';
        }
        if($destination_id != "all"){
            $sql .= ' AND destinations.project_id '.(is_null($destination_id) ? ' IS NULL ' : ' = '.$destination_id).' ';
        }

        $sql .= ' GROUP BY material_items.item_id,source_project_id,destination_project_id ';

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function inventory_sales($project_id = null, $location_id = null, $sub_location_id = null, $client_id = null, $from = '', $to = ''){
        $sql = 'SELECT * FROM (

                SELECT stock_sales_material_items.stock_sale_id, sale_date, item_name, measurement_units.symbol AS unit_symbol, currencies.symbol AS currency_symbol, stock_sales_material_items.quantity, (
                  SELECT COALESCE(average_price, 0)
                  FROM material_average_prices
                  WHERE transaction_date <= "'.$to.'"
                  AND material_item_id = stock_sales_material_items.material_item_id
                  AND sub_location_id = stock_sales_material_items.source_sub_location_id
                  ORDER BY average_price_id DESC
                  LIMIT 1
                ) AS purchasing_price, stock_sales_material_items.price AS selling_price, (
                  SELECT exchange_rate FROM exchange_rate_updates
                  WHERE exchange_rate_updates.currency_id = stock_sales.currency_id
                  ORDER BY id DESC LIMIT 1
                ) AS exchange_rate, stock_sales.created_at FROM stock_sales_material_items
                LEFT JOIN stock_sales ON stock_sales_material_items.stock_sale_id = stock_sales.id
                LEFT JOIN material_items ON stock_sales_material_items.material_item_id = material_items.item_id
                LEFT JOIN currencies ON stock_sales.currency_id = currencies.currency_id
                LEFT JOIN measurement_units ON material_items.unit_id = measurement_units.unit_id
                WHERE sale_date >= "'.$from.'" AND sale_date <= "'.$to.'" ';

        if(!is_null($location_id)){
            $sql .= ' AND stock_sales.location_id = "'.$location_id.'" ';
        }
        if($project_id != "all"){
            $sql .= ' AND stock_sales.project_id'.(!is_null($project_id) ? ' = '.$project_id : ' IS NULL').'';
        }
        if(!is_null($client_id)){
            $sql .= ' AND stock_sales.client_id = '.$client_id.'';
        }
        if(!is_null($sub_location_id)){
            $sql .= ' AND stock_sales_material_items.source_sub_location_id = "'.$sub_location_id.'" ';
        }


        $sql .= '
                UNION 
                
                SELECT stock_sales_asset_items.stock_sale_id, sale_date, asset_code AS item_name, "item" AS unit_symbol, currencies.symbol AS currency_symbol, "1" AS quantity, assets.book_value AS purchasing_price, stock_sales_asset_items.price AS selling_price, (
                  SELECT exchange_rate FROM exchange_rate_updates
                  WHERE exchange_rate_updates.currency_id = stock_sales.currency_id
                  ORDER BY id DESC LIMIT 1
                ) AS exchange_rate, stock_sales.created_at FROM stock_sales_asset_items
                LEFT JOIN stock_sales ON stock_sales_asset_items.stock_sale_id = stock_sales.id
                LEFT JOIN currencies ON stock_sales.currency_id = currencies.currency_id
                LEFT JOIN asset_sub_location_histories ON stock_sales_asset_items.asset_sub_location_history_id = asset_sub_location_histories.id
                LEFT JOIN assets ON asset_sub_location_histories.asset_id = assets.id
                WHERE sale_date >= "'.$from.'" AND sale_date <= "'.$to.'" ';

        if(!is_null($location_id)){
            $sql .= ' AND stock_sales.location_id = "'.$location_id.'" ';
        }
        if($project_id != "all"){
            $sql .= ' AND stock_sales.project_id'.(!is_null($project_id) ? ' = '.$project_id : ' IS NULL').'';;
        }
        if(!is_null($client_id)){
            $sql .= ' AND stock_sales.client_id = '.$client_id.'';
        }
        if(!is_null($sub_location_id)){
            $sql .= ' AND asset_sub_location_histories.sub_location_id = "'.$sub_location_id.'" ';
        }

        $sql .= ' ) AS inventory_sales_report ORDER BY sale_date, created_at';

        $query = $this->db->query($sql);
        return $query->result();
    }

}

