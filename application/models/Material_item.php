<?php

class Material_item extends MY_Model{

    const DB_TABLE = 'material_items';
    const DB_TABLE_PK = 'item_id';
    
    public $item_name;
    public $unit_id;
    public $category_id;
    public $part_number;
    public $description;
    public $thumbnail_name;

    public function datatable_items_list(){
        $this->load->model(['material_item_category','measurement_unit']);
        $data['measurement_unit_options'] = $this->measurement_unit->dropdown_options();
        $data['material_item_category_options'] = $this->material_item_category->dropdown_options();

        $limit = $this->input->post('length');
        $category_id = $this->input->post('category_id');

        $this->load->model('material_item_category');

        $keyword = $this->input->post('search')['value'];
        $start = $this->input->post('start');

        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        switch ($order_column) {
            case 1;
                $order_column = 'item_name';
                break;
            case 2;
                $order_column = 'category_name';
                break;
            case 3;
                $order_column = 'part_number';
                break;
            case 4;
                $order_column = 'symbol';
                break;
            case 4;
                $order_column = 'description';
                break;
            default:
                $order_column = 'item_name';
        }

        $order_string = $order_column.' '.$order_dir;

        $sql = 'SELECT material_items.*, measurement_units.symbol AS unit, category_name
                FROM material_items
                LEFT JOIN measurement_units ON material_items.unit_id = measurement_units.unit_id
                LEFT JOIN material_item_categories ON material_items.category_id = material_item_categories.category_id
                ';

        $where = '';
        if(trim($category_id) != ''){
            $where = ' WHERE material_items.category_id = "'.$category_id.'" ';
            $records_total_filter = ['category_id' => $category_id];
        } else {
            $records_total_filter = [];
        }

        if($keyword != ''){
            $where .= ($where != '' ? ' AND ' : ' WHERE '). ' ( item_name LIKE "%'.$keyword.'%" OR symbol LIKE "%'.$keyword.'%" OR category_name LIKE "%'.$keyword.'%" OR part_number LIKE "%'.$keyword.'%" OR material_items.description LIKE "%'.$keyword.'%") ';
        }

        $sql .= $where;

        $query = $this->db->query($sql);
        $records_filtered = $query->num_rows();

        $sql .= " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $query = $this->db->query($sql);
        $results = $query->result();

        $records_total = $this->material_item->count_rows($records_total_filter);

        $rows = [];
        foreach($results as $row){
            $item = new self();
            $item->load($row->item_id);
            $data['item'] = $item;
            $rows[] = [
                '<img class="gallery-items" src="'.$item->thumbnail().'" alt="thumbnail" width="100px">',
                $row->item_name,
                $row->category_name ? $row->category_name : 'N/A',
                $row->part_number,
                $row->unit,
                $row->description,
                $this->load->view('inventory/material/material_item_list_actions',$data,true)
            ];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function items_list($category_id = null,$nature_id = null){
        $sql = 'SELECT item_name, CONCAT(name," (",symbol,")") AS UOM, material_item_categories.category_name,nature.category_name AS material_nature 
                FROM material_items
                LEFT JOIN material_item_categories ON material_items.category_id = material_item_categories.category_id
                LEFT JOIN measurement_units ON material_items.unit_id = measurement_units.unit_id
                LEFT JOIN project_categories AS nature ON material_item_categories.project_nature_id = nature.category_id 
                ';

        if(!is_null($category_id)){
            $sql .= ' WHERE material_items.category_id = '.$category_id;
        }

        $query = $this->db->query($sql);
        $material_items = $query->result();
        return $material_items;

    }

    public function thumbnail(){
        $directory_path = 'images/material_items_thumbnails/';
        return base_url(($this->thumbnail_name != '' && file_exists($directory_path.$this->thumbnail_name) ? $directory_path.$this->thumbnail_name : 'images/default_thumbnail.jpg'));
    }

    public function dropdown_options($project_nature_id = null, $all_of_them = false){

        $options['Uncategorized'][''] = "&nbsp;";
        $sql = 'SELECT item_name, item_id FROM material_items WHERE category_id IS NULL';
        $query = $this->db->query($sql);
        $material_items = $query->result();
        foreach ($material_items as $material_item) {
            $options['Uncategorized'][$material_item->item_id] = $material_item->item_name;
        }

        if($all_of_them){
            $where_clause = [];
        } else {
            $where_clause = $project_nature_id == 'ALL' || $project_nature_id == 'all' ? [] : ' project_nature_id = "' . $project_nature_id . '" OR project_nature_id IS NULL ';
        }

        $this->load->model('material_item_category');
        $item_categories = $this->material_item_category->get(0,0,$where_clause);
        foreach($item_categories as $category){
            $material_options = $category->material_items();
            foreach($material_options as $material_option) {
                $options[$category->category_name][$material_option->item_id] = $material_option->item_name;
            }
        }

        return $options;
    }

    public function excel_dropdown_list(){
        $results = $this->get();
        $list = '';
        foreach ($results as $row){
            $list .= $row->item_name.',';
        }
        return rtrim($list,',');
    }

    public function delete_thumbnail(){
        $file_path = 'images/material_items_thumbnails/'.$this->thumbnail_name;
        if($this->thumbnail_name != '' && file_exists($file_path)){
            unlink($file_path);
        }
    }

    public function unit()
    {
        $this->load->model('measurement_unit');
        $unit = new Measurement_unit();
        $unit->load($this->unit_id);
        return $unit;
    }

    public function name_with_part_number()
    {
        return $this->item_name.($this->part_number != '' ? ' - '.$this->part_number : '');
    }

    public function sub_location_stock($sub_location_id,$limit,$start,$keyword,$order){

        $order_string = dataTable_order_string(['item_name','category_name','symbol','part_number','description','quantity_available'],$order,'item_name');
        $order_string = " ORDER BY ".$order_string." LIMIT ".$limit." OFFSET ".$start;

        $sql_header = 'SELECT * FROM
                (
                    SELECT ';

        $sql_body = '
                (
                    (
                        SELECT COALESCE(SUM(quantity),0)
                        FROM material_stocks
                        WHERE sub_location_id = "' . $sub_location_id . '"
                        AND item_id = material_items.item_id
                    )  - (
                            (
                                SELECT COALESCE(SUM(quantity),0)
                                FROM external_material_transfer_items
                                LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                                WHERE source_sub_location_id = "' . $sub_location_id . '"
                                AND external_material_transfer_items.material_item_id = material_items.item_id
                                AND external_material_transfers.status != "CANCELLED"
                            ) + (
                                SELECT COALESCE(SUM(quantity),0) FROM material_stocks
                                LEFT JOIN internal_material_transfer_items ON material_stocks.stock_id = internal_material_transfer_items.stock_id
                                WHERE internal_material_transfer_items.source_sub_location_id = "' . $sub_location_id . '"
                                AND material_stocks.item_id = material_items.item_id
                            ) + (
                                  SELECT COALESCE(SUM(quantity),0) FROM material_costs
                                  WHERE source_sub_location_id = "' . $sub_location_id . '"
                                  AND material_costs.material_item_id = material_items.item_id
                            ) + (
                                SELECT COALESCE(SUM(quantity),0) FROM material_disposal_items
                                WHERE sub_location_id = '.$sub_location_id.'
                                AND material_disposal_items.material_item_id = material_items.item_id
                            )
                            
                    )
                ) AS quantity_available, category_name, symbol
                FROM material_items
                LEFT JOIN measurement_units ON material_items.unit_id = measurement_units.unit_id
                LEFT JOIN material_item_categories ON material_items.category_id = material_item_categories.category_id
            ) AS stock
            WHERE quantity_available > 0
        ';


        $query = $this->db->query($sql_header.$sql_body);
        $records_total = $query->num_rows();

        $sql_header = 'SELECT SQL_CALC_FOUND_ROWS * FROM
                (
                    SELECT  material_items.item_id,material_items.item_name, material_items.description,material_items.part_number,';

        if ($keyword != '') {
            $sql_body .= ' AND (item_name LIKE "%' . $keyword . '%" OR symbol LIKE "%' . $keyword . '%" OR part_number LIKE "%' . $keyword . '%" OR category_name LIKE "%' . $keyword . '%" OR description LIKE "%' . $keyword . '%") ';
        }

        $sql_body .= $order_string;

        $query = $this->db->query($sql_header.$sql_body);
        $results = $query->result();

        //Get number of records filtered
        $sql = 'SELECT FOUND_ROWS() AS records_filtered';
        $query = $this->db->query($sql);
        $records_filtered = $query->row()->records_filtered;

        $rows = [];
        $this->load->model(['measurement_unit','material_item_category']);
        $data['measurement_unit_options'] = $this->measurement_unit->dropdown_options();
        $data['material_item_category_options'] = $this->material_item_category->dropdown_options();
        $data['project_options'] = projects_dropdown_options();
        foreach ($results as $row) {
            $item = new self();
            $item->load($row->item_id);
            $data['item'] = $item;
            $balance = $item->sub_location_balance($sub_location_id,'all');
            if($balance > 0) {
                $rows[] = [
                    '<img class="gallery-items" src="' . $item->thumbnail() . '" alt="thumbnail" width="100px">',
                    $row->item_name,
                    $row->category_name ? $row->category_name : 'N/A',
                    $row->symbol,
                    $row->part_number,
                    $item->description,
                    '<span class="pull-right">' . $item->sub_location_balance($sub_location_id, 'all') . '</span>',
                    ''
                ];
            }
        }

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function location_stock($location_id,$limit,$start,$keyword,$order){
        $order_column = $order['column'];
        $order_dir = $order['dir'];
        switch ($order_column) {
            case 1;
                $order_column = 'item_name';
                break;
            case 2;
                $order_column = 'category_name';
                break;
            case 3;
                $order_column = 'symbol';
                break;
            case 4;
                $order_column = 'part_number';
                break;
            case 5;
                $order_column = 'description';
                break;
            case 7;
                $order_column = 'quantity_available';
                break;
            default:
                $order_column = 'item_name';
        }

        $order_string = $order_column . ' ' . $order_dir;

        $sql = 'SELECT * FROM
                (
                    SELECT material_items.item_id,material_items.item_name, material_items.description,material_items.part_number,
                    (
                        (
                            SELECT COALESCE(SUM(quantity),0)
                            FROM material_stocks
                            LEFT JOIN sub_locations ON material_stocks.sub_location_id = sub_locations.sub_location_id
                            WHERE sub_locations.location_id = "' . $location_id . '"
                            AND item_id = material_items.item_id
                        ) - (
                                (
                                    SELECT COALESCE(SUM(quantity),0)
                                    FROM external_material_transfer_items
                                    LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                                    WHERE external_material_transfers.source_location_id = "' . $location_id . '"
                                    AND external_material_transfer_items.material_item_id = material_items.item_id
                                    AND external_material_transfers.status != "CANCELLED"
                                ) + (
                                    SELECT COALESCE(SUM(quantity),0) FROM material_stocks
                                    LEFT JOIN internal_material_transfer_items ON material_stocks.stock_id = internal_material_transfer_items.stock_id
                                    LEFT JOIN sub_locations ON internal_material_transfer_items.source_sub_location_id = sub_locations.sub_location_id
                                    WHERE sub_locations.location_id = "' . $location_id . '"
                                    AND material_stocks.item_id = material_items.item_id
                                )  + (
                                      SELECT COALESCE(SUM(quantity),0) FROM material_costs
                                      LEFT JOIN sub_locations ON material_costs.source_sub_location_id = sub_locations.sub_location_id
                                      WHERE sub_locations.location_id = "' . $location_id . '"
                                      AND material_costs.material_item_id = material_items.item_id
                                ) + (
                                      SELECT COALESCE(SUM(quantity),0) FROM material_disposal_items
                                      LEFT JOIN sub_locations ON material_disposal_items.sub_location_id = sub_locations.sub_location_id
                                      WHERE sub_locations.location_id = "' . $location_id . '"
                                      AND material_disposal_items.material_item_id = material_items.item_id
                                )
                        )
                    ) AS quantity_available,
                     category_name, symbol
                    FROM material_items
                    LEFT JOIN measurement_units ON material_items.unit_id = measurement_units.unit_id
                    LEFT JOIN material_item_categories ON material_items.category_id = material_item_categories.category_id
                ) AS stock
                WHERE quantity_available > 0
            ';


        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        if ($keyword != '') {
            $sql .= ' AND (item_name LIKE "%' . $keyword . '%" OR symbol LIKE "%' . $keyword . '%" OR part_number LIKE "%' . $keyword . '%" OR category_name LIKE "%' . $keyword . '%" OR description LIKE "%' . $keyword . '%") ';
        }

        $query = $this->db->query($sql);
        $records_filtered = $query->num_rows();

        $sql .= " ORDER BY " . $order_string . " LIMIT " . $limit . " OFFSET " . $start;

        $query = $this->db->query($sql);
        $results = $query->result();
        $rows = [];
        $this->load->model('inventory_location');
        $location = new Inventory_location();
        $location->load($location_id);
        foreach ($results as $row) {
            $item = new self();
            $item->load($row->item_id);
            $data['item'] = $item;
            $balance = $item->sub_location_balance($location->sub_location_ids_query(),'all',null,'external');
            if($balance > 0) {
                $rows[] = [
                    '<img class="gallery-items" src="' . $item->thumbnail() . '" alt="thumbnail" width="100px">',
                    $row->item_name,
                    $row->category_name ? $row->category_name : 'N/A',
                    $row->symbol,
                    $row->part_number,
                    $item->description,
                    '<span class="pull-right">' . $item->sub_location_balance($location->sub_location_ids_query(), 'all', null, 'external') . '</span>',
                    ''
                ];
            }
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function location_material_items($location_id, $sub_location_id = null, $with_balance = false, $project_id = 'ALL',$category_id = null){
        $project_id = $project_id == 'all' ? strtoupper($project_id) : $project_id;
        if(is_null($sub_location_id)) {
            $sql = 'SELECT item_id FROM
                (
                    SELECT material_items.item_id,
                    (
                        (
                            SELECT COALESCE(SUM(quantity),0)
                            FROM material_stocks
                            LEFT JOIN sub_locations ON material_stocks.sub_location_id = sub_locations.sub_location_id';
            if(!is_null($category_id)){
                $sql .= ' LEFT JOIN material_items ON material_stocks.item_id = material_items.item_id';
            }
            $sql .= ' WHERE sub_locations.location_id = "' . $location_id . '"
                            AND material_stocks.item_id = material_items.item_id ';
            if(is_null($project_id)){
                $sql .= ' AND material_stocks.project_id IS NULL ';
            } else if($project_id != 'ALL'){
                $sql .= ' AND material_stocks.project_id = "'.$project_id.'" ';
            } else if(!is_null($category_id)){
                $sql .= ' AND material_items.category_id = "'.$category_id.'"';
            }
            $sql .='
                        )';

            $sql .= ' - (
                                (
                                    SELECT COALESCE(SUM(quantity),0)
                                    FROM external_material_transfer_items
                                    LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                                    WHERE external_material_transfers.source_location_id = "' . $location_id . '"
                                    AND external_material_transfer_items.material_item_id = material_items.item_id
                                    ';
            if(is_null($project_id)){
                $sql .= ' AND external_material_transfer_items.project_id IS NULL ';
            } else if($project_id != 'ALL'){
                $sql .= ' AND external_material_transfer_items.project_id  = "'.$project_id.'" ';
            }
            $sql .= '
                                    AND external_material_transfers.status != "CANCELLED"
                                ) + (
                                    SELECT COALESCE(SUM(quantity),0) FROM material_stocks
                                    LEFT JOIN internal_material_transfer_items ON material_stocks.stock_id = internal_material_transfer_items.stock_id';
            if(!is_null($category_id)){
                $sql .= ' LEFT JOIN material_items ON material_stocks.item_id = material_items.item_id';
            }
            $sql .= ' LEFT JOIN sub_locations ON internal_material_transfer_items.source_sub_location_id = sub_locations.sub_location_id
                                    WHERE sub_locations.location_id = "' . $location_id . '"
                                    AND material_stocks.item_id = material_items.item_id
                                    ';
            if(is_null($project_id)){
                $sql .= ' AND material_stocks.project_id IS NULL ';
            } else if($project_id != 'ALL'){
                $sql .= ' AND material_stocks.project_id = "'.$project_id.'" ';
            } else if(!is_null($category_id)){
                $sql .= ' AND material_items.category_id = "'.$category_id.'"';
            }
            $sql .= '
                                )  + (
                                      SELECT COALESCE(SUM(quantity),0) FROM material_costs
                                      LEFT JOIN sub_locations ON material_costs.source_sub_location_id = sub_locations.sub_location_id
                                      WHERE sub_locations.location_id = "' . $location_id . '"
                                      AND material_costs.material_item_id = material_items.item_id ';
            if($project_id != 'ALL' && $project_id != 'all' && !is_null($project_id)){
                $sql .= ' AND material_costs.project_id = '.$project_id;
            }
            $sql .= '
                                )
                        )
                    ) AS quantity_available
                    FROM material_stocks
                    LEFT JOIN material_items ON material_stocks.item_id = material_items.item_id
                    LEFT JOIN sub_locations ON material_stocks.sub_location_id = sub_locations.sub_location_id
                    WHERE sub_locations.location_id = ' . $location_id;

            if(!is_null($category_id)){
                $sql .= ' AND material_items.category_id = "'.$category_id.'"';
            }

            $sql .= ' ) AS stock';
        } else {
            $sql = 'SELECT item_id FROM
                (
                    SELECT  material_items.item_id,(
                        (
                            SELECT COALESCE(SUM(quantity),0)
                            FROM material_stocks
                            WHERE sub_location_id = "' . $sub_location_id . '"
                            AND item_id = material_items.item_id ';
            if(is_null($project_id)){
                $sql .= ' AND material_stocks.project_id IS NULL ';
            } else if($project_id != 'ALL'){
                $sql .= ' AND material_stocks.project_id = "'.$project_id.'" ';
            }
            $sql .= '
                        )  - (
                                (
                                    SELECT COALESCE(SUM(quantity),0)
                                    FROM external_material_transfer_items
                                    LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                                    WHERE source_sub_location_id = "' . $sub_location_id . '"
                                    AND external_material_transfer_items.material_item_id = material_items.item_id
                                    AND external_material_transfers.status != "CANCELLED" ';

            if(is_null($project_id)){
                $sql .= ' AND external_material_transfer_items.project_id IS NULL ';
            } else if($project_id != 'ALL'){
                $sql .= ' AND external_material_transfer_items.project_id = "'.$project_id.'" ';
            }
            $sql .= '
                                ) + (
                                    SELECT COALESCE(SUM(quantity),0) FROM material_stocks
                                    LEFT JOIN internal_material_transfer_items ON material_stocks.stock_id = internal_material_transfer_items.stock_id
                                    WHERE internal_material_transfer_items.source_sub_location_id = "' . $sub_location_id . '"
                                    AND material_stocks.item_id = material_items.item_id ';
            if(is_null($project_id)){
                $sql .= ' AND material_stocks.project_id IS NULL ';
            } else if($project_id != 'ALL'){
                $sql .= ' AND material_stocks.project_id =  "'.$project_id.'" ';;
            }
            $sql .= '
                                ) + (
                                      SELECT COALESCE(SUM(quantity),0) FROM material_costs
                                      WHERE source_sub_location_id = "' . $sub_location_id . '"
                                      AND material_costs.material_item_id = material_items.item_id
                                      ';
            if($project_id != 'ALL' && $project_id != 'all' && !is_null($project_id)){
                $sql .= ' AND material_costs.project_id = '.$project_id;
            }
            $sql .= '
                                )
                        )
                    ) AS quantity_available
                    FROM material_stocks
                    LEFT JOIN material_items ON material_stocks.item_id = material_items.item_id
                    WHERE material_stocks.sub_location_id ='.$sub_location_id;
            if(!is_null($category_id)){
                $sql .= ' AND material_items.category_id = "'.$category_id.'"';
            }
            $sql .= ' ) AS stock ';
        }

        if($with_balance){
            $sql .= ' WHERE quantity_available > "0"';
        }
        $sql .= ' GROUP BY item_id ';

        $query = $this->db->query($sql);

        $results = $query->result();
         $material_items = [];
         foreach ($results as $row){
            $material_item = new self;
            $material_item->load($row->item_id);
             $material_items[] = $material_item;
         }
         return $material_items;

    }

    public function project_material_items($project_id){
        //$sql = 'SELECT DISTINCT item_id FROM material_stocks WHERE project_id = '.$project_id;
        $sql = 'SELECT DISTINCT item_id FROM (
        SELECT item_id FROM material_stocks WHERE project_id = '.$project_id.'
        UNION
        SELECT material_item_id as item_id FROM purchase_order_material_items
        LEFT JOIN purchase_orders ON purchase_order_material_items.order_id = purchase_orders.order_id
        LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
        WHERE project_id = '.$project_id.'
        ) AS project_material_items';
        $query = $this->db->query($sql);
        $results = $query->result();
        $material_items = [];
        foreach ($results as $row){
            $material_item = new self();
            $material_item->load($row->item_id);
            $material_items[] = $material_item;
        }

        return $material_items;
    }

    public function average_ordered_price($project_id = 'all')
    {
        $sql = 'SELECT (COALESCE(SUM(price*quantity*(
                SELECT exchange_rate FROM exchange_rate_updates
                WHERE exchange_rate_updates.currency_id = currencies.currency_id
                ORDER BY update_date DESC LIMIT 1
                )),0)/SUM(quantity)) AS order_price FROM purchase_order_material_items
                LEFT JOIN purchase_orders ON purchase_order_material_items.order_id = purchase_orders.order_id
                LEFT JOIN currencies ON purchase_orders.currency_id = currencies.currency_id
                LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id 
                WHERE material_item_id = '.$this->{$this::DB_TABLE_PK};

        if(strtolower($project_id) != 'all'){
            $sql .= ' AND project_id = '.$project_id;
        }

        $query = $this->db->query($sql);
        return $query->row()->order_price;
    }

    public function sub_location_item_movement_transactions($sub_location_id, $from, $to,$project_id = 'all'){
        $project_id = $project_id == 'ALL' ? strtolower($project_id) : $project_id;
        $sql = 'SELECT "OPENING BALANCE" AS document_type, "N/A" AS reference, date_received AS transaction_date, SUM(material_stocks.quantity) AS qty_in, 0 AS qty_out, material_stocks.price AS rate, material_stocks.created_at
                FROM material_stocks LEFT JOIN material_opening_stocks ON material_stocks.stock_id = material_opening_stocks.stock_id
                WHERE material_opening_stocks.item_id = '.$this->{$this::DB_TABLE_PK}.' AND date_received >= "'.$from.'" AND date_received <= "'.$to.'"
                AND material_stocks.sub_location_id IN ('.$sub_location_id.')';
        if(is_null($project_id)){
            $sql .= ' AND material_stocks.project_id IS NULL ';
        } else if($project_id != 'all'){
            $sql .= ' AND material_stocks.project_id = '.$project_id;
        }
        $sql .= '
                GROUP BY material_opening_stocks.item_id, date_received, price
                ';

        $sql .= ' UNION ALL
                SELECT IF(inventory_locations.project_id IS NULL, "GRN", "DELIVERY" ) AS document_type, goods_received_notes.grn_id AS reference, date_received AS transaction_date, SUM(material_stocks.quantity) AS qty_in, 0 AS qty_out, material_stocks.price AS rate, material_stocks.created_at
                FROM material_stocks 
                LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                LEFT JOIN inventory_locations ON goods_received_notes.location_id = inventory_locations.location_id
                WHERE material_stocks.item_id = '.$this->{$this::DB_TABLE_PK}.' AND goods_received_notes.receive_date >= "'.$from.'" AND goods_received_notes.receive_date <= "'.$to.'"
                AND material_stocks.sub_location_id IN ('.$sub_location_id.')';
        if(is_null($project_id)){
            $sql .= ' AND material_stocks.project_id IS NULL ';
        } else if($project_id != 'all'){
            $sql .= ' AND material_stocks.project_id = '.$project_id;
        }
        $sql .= ' GROUP BY material_stocks.item_id, date_received, price, goods_received_notes.grn_id';
    if($project_id != 'all') {
        $sql .= ' UNION ALL
                SELECT "COST ASSIGNMENT" AS document_type, material_cost_center_assignments.id AS reference,
                assignment_date AS transaction_date,SUM(material_stocks.quantity) AS qty_in, 0 AS qty_out, material_stocks.price AS rate, material_cost_center_assignments.created_at
                FROM material_stocks
                LEFT JOIN material_cost_center_assignment_items ON material_stocks.stock_id = material_cost_center_assignment_items.stock_id
                LEFT JOIN material_cost_center_assignments ON material_cost_center_assignment_items.material_cost_center_assignment_id = material_cost_center_assignments.id
                WHERE material_stocks.project_id  ' .(is_null($project_id) ? 'IS NULL' :' = '. $project_id ). ' 
                AND material_stocks.item_id = ' . $this->{$this::DB_TABLE_PK} . ' 
                AND material_stocks.sub_location_id IN (' . $sub_location_id . ') 
                AND assignment_date >= "' . $from . '" 
                AND assignment_date <= "' . $to . '"
                GROUP BY material_cost_center_assignments.id
                ';
        $sql .= ' UNION ALL
                SELECT "COST ASSIGNMENT" AS document_type, material_cost_center_assignments.id AS reference,
                assignment_date AS transaction_date,0 AS qty_in, SUM(material_stocks.quantity) AS qty_out, material_stocks.price AS rate, material_cost_center_assignments.created_at
                FROM material_stocks
                LEFT JOIN material_cost_center_assignment_items ON material_stocks.stock_id = material_cost_center_assignment_items.stock_id
                LEFT JOIN material_cost_center_assignments ON material_cost_center_assignment_items.material_cost_center_assignment_id = material_cost_center_assignments.id
                WHERE material_cost_center_assignments.source_project_id ' .(is_null($project_id) ? 'IS NULL' :' = '. $project_id ). ' 
                AND material_stocks.item_id = ' . $this->{$this::DB_TABLE_PK} . ' 
                AND material_stocks.sub_location_id IN (' . $sub_location_id . ') 
                AND assignment_date >= "' . $from . '" 
                AND assignment_date <= "' . $to . '"
                GROUP BY material_cost_center_assignments.id
                ';
    }

        $sql .= ' UNION ALL
                SELECT "EXT. TRANSFER" AS document_type, external_material_transfer_items.transfer_id AS reference, transfer_date AS transaction_date,0 as qty_in, SUM(external_material_transfer_items.quantity) AS qty_out,  external_material_transfer_items.price AS rate, created_at
                FROM external_material_transfer_items
                LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                WHERE source_sub_location_id IN ('.$sub_location_id.')
                AND external_material_transfer_items.material_item_id = "'.$this->{$this::DB_TABLE_PK}.'"
                AND transfer_date >= "'.$from.'"
                AND transfer_date <= "'.$to.'" 
                AND external_material_transfers.status != "CANCELLED" ';
        if(is_null($project_id)){
            $sql .= ' AND external_material_transfers.project_id IS NULL ';
        } else if($project_id != 'all'){
            $sql .= ' AND external_material_transfers.project_id = '.$project_id;
        }
        $sql .='
                GROUP BY external_material_transfer_items.material_item_id, transfer_date, price, external_material_transfer_items.transfer_id
                ';

        $arr = explode(' ',trim($sub_location_id));
        if($arr[0] != 'SELECT' || (isset($arr[1]) && $arr[1] == ',')) {

            $sql .= ' UNION ALL
                    SELECT "INT. TRANSFER" AS document_type, internal_material_transfer_items.transfer_id AS reference, transfer_date AS transaction_date, 0 AS qty_in, SUM(material_stocks.quantity) AS qty_out, material_stocks.price AS rate, created_at
                    FROM material_stocks
                    LEFT JOIN internal_material_transfer_items ON material_stocks.stock_id = internal_material_transfer_items.stock_id
                    LEFT JOIN internal_material_transfers ON internal_material_transfer_items.transfer_id = internal_material_transfers.transfer_id
                    WHERE source_sub_location_id IN ('.$sub_location_id.')
                    AND material_stocks.item_id = '.$this->{$this::DB_TABLE_PK}.'
                    AND transfer_date >= "'.$from.'"
                    AND transfer_date <= "'.$to.'" ';
            if(is_null($project_id)){
                $sql .= ' AND material_stocks.project_id IS NULL ';
            } else if($project_id != 'all'){
                $sql .= ' AND material_stocks.project_id = '.$project_id;
            }

            $sql .= '
                    GROUP BY material_stocks.item_id, transfer_date, price, internal_material_transfer_items.transfer_id
                    
                    UNION ALL
                    
                    SELECT "INT. TRANSFER" AS document_type, internal_material_transfer_items.transfer_id AS reference, transfer_date AS transaction_date, SUM(material_stocks.quantity) AS qty_in, 0 AS qty_out, material_stocks.price AS rate, created_at
                    FROM material_stocks
                    LEFT JOIN internal_material_transfer_items ON material_stocks.stock_id = internal_material_transfer_items.stock_id
                    LEFT JOIN internal_material_transfers ON internal_material_transfer_items.transfer_id = internal_material_transfers.transfer_id
                    WHERE material_stocks.sub_location_id IN ('.$sub_location_id.')
                    AND material_stocks.item_id = '.$this->{$this::DB_TABLE_PK}.'
                    AND transfer_date >= "'.$from.'"
                    AND transfer_date <= "'.$to.'" ';
            if(is_null($project_id)){
                $sql .= ' AND material_stocks.project_id IS NULL ';
            } else if($project_id != 'all'){
                $sql .= ' AND material_stocks.project_id = '.$project_id;
            }
            $sql .= '
                    GROUP BY material_stocks.item_id, transfer_date, price, internal_material_transfer_items.transfer_id
                ';
        }

        $sql .= ' UNION ALL
                SELECT "INSTALLATION/USAGE/COST" AS document_type, COALESCE(task_name,"PROJECT SHARED") AS reference, cost_date AS transaction_date, 0 AS qty_in, material_costs.quantity AS qty_out, material_costs.rate AS rate, created_at
                FROM material_costs
                LEFT JOIN tasks ON material_costs.task_id = tasks.task_id
                WHERE material_item_id = '.$this->{$this::DB_TABLE_PK}.'
                AND source_sub_location_id IN ('.$sub_location_id.')
                AND cost_date >= "'.$from.'"
                AND cost_date <= "'.$to.'"';

        $sql .= ' UNION ALL
                SELECT "STOCK SALE" AS document_type, stock_sale_id AS reference, sale_date AS transaction_date, 0 AS qty_id, stock_sales_material_items.quantity AS qty_out,stock_sales_material_items.price AS rate, created_at
                FROM stock_sales_material_items
                LEFT JOIN stock_sales ON stock_sales_material_items.stock_sale_id = stock_sales.id
                WHERE material_item_id = '.$this->{$this::DB_TABLE_PK}.'
                AND source_sub_location_id IN( '.$sub_location_id.')
                AND sale_date >= "'.$from.'"
                AND sale_date <= "'.$to.' "
              ';

        $sql .= ' UNION ALL
                SELECT "MATL DISPOSAL" AS document_type, disposal_id AS reference, disposal_date AS transaction_date, 0 AS qty_id, material_disposal_items.quantity AS qty_out,material_disposal_items.rate AS rate, created_at
                FROM material_disposal_items
                LEFT JOIN material_disposals ON material_disposal_items.disposal_id = material_disposals.id
                WHERE material_item_id = '.$this->{$this::DB_TABLE_PK}.'
                AND sub_location_id IN( '.$sub_location_id.')
                AND disposal_date >= "'.$from.'"
                AND disposal_date <= "'.$to.'"';
            if(is_null($project_id)){
                $sql .= ' AND material_disposal_items.project_id IS NULL ';
            } else if($project_id != 'all'){
                $sql .= ' AND material_disposal_items.project_id = '.$project_id;
            }

        $sql .= ' ORDER BY created_at ASC ';

        $query = $this->db->query($sql);
        return $query->result();

    }

    public function location_average_price($location_id, $to = null,$transfer_type = null){
        $this->load->model('inventory_location');
        $location = new Inventory_location();
        $location->load($location_id);
        $sub_locations = $location->sub_locations();
        $stock_value = $stock_quantity = 0;
        foreach ($sub_locations as $sub_location) {
            $stock_quantity += $quantity = $this->sub_location_balance($sub_location->{$sub_location::DB_TABLE_PK}, 'all', $to, $transfer_type);
            $stock_average_price = $this->sub_location_average_price($sub_location->{$sub_location::DB_TABLE_PK}, 'ALL');
            $stock_value += $quantity * $stock_average_price;
        }
        return $stock_value / $stock_quantity;
    }

    public function sub_location_received_quantity($sub_location_id, $project_id = null, $from = null, $to = null, $query_string = false){
        $project_id = $project_id == '' ? null : $project_id;
        $sql = 'SELECT COALESCE(SUM(quantity),0) '.(!$query_string ? ' AS received_quantity' : '').'
                            FROM material_stocks
                            WHERE item_id = "' . $this->{$this::DB_TABLE_PK} . '"
                            AND sub_location_id IN (' . $sub_location_id . ')
        ';

        if(!is_null($from)){
            $sql .= ' AND date_received >= "'.$from.'" ';
        }
        if(!is_null($to)){
            $sql .= ' AND date_received <= "'.$to.'" ';
        }

        $assigned_in = '0';
        if($project_id != 'all' && $project_id != 'ALL') {
            if (!is_null($project_id)) {
                $sql .= ' AND project_id = "' . $project_id . '"';
            } else {
                $sql .= ' AND project_id IS NULL';
            }
        } else {
            $assigned_in = $this->sub_location_assigned_in_quantity($sub_location_id,$project_id,$from,$to,true);
        }

        $arr = explode(' ',trim($sub_location_id));
        if($arr[0] == 'SELECT' || (isset($arr[1]) && $arr[1] == ',')){
            $sql = 'SELECT(('.$sql.")-(".$this->sub_location_transferred_out_quantity($sub_location_id,$project_id,$from,$to,'internal',true).')) AS received_quantity';
        }

        $sql = 'SELECT (('.$sql.') - ('.$assigned_in.')) AS received_quantity';

        if(!$query_string){
            $query = $this->db->query($sql);
            $results = $query->result();
            return array_shift($results)->received_quantity;
        } else {
            return $sql;
        }
    }

    public function sub_location_transferred_out_quantity($sub_location_id, $project_id = null,$from = null, $to = null, $transfer_type = 'ALL', $query_string = false, $on_transit = false){

        $project_id = $project_id != '' ? $project_id : null;
        $sql = '';

        $external_transferred = 'SELECT COALESCE(SUM(quantity),0)
                                FROM external_material_transfer_items
                                LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                                WHERE source_sub_location_id IN (' . $sub_location_id . ')';
        if($on_transit) {
            $external_transferred .= ' AND external_material_transfers.status = "ON TRANSIT" ';
        } else {
            $external_transferred .= ' AND external_material_transfers.status != "CANCELLED" ';
        }
        $external_transferred .= ' AND material_item_id = "' . $this->{$this::DB_TABLE_PK} . '" ';

        if(!is_null($from)){
            $external_transferred .= ' AND transfer_date >= "'.$from.'" ';
        }
        if(!is_null($to)){
            $external_transferred .= ' AND transfer_date <= "'.$to.'" ';
        }

        if($project_id != 'all' && $project_id != 'ALL') {
            if (!is_null($project_id)) {
                $external_transferred .= ' AND external_material_transfers.project_id = "' . $project_id . '"';
            } else {
                $external_transferred .= ' AND external_material_transfers.project_id IS NULL';
            }
        }


        $internal_transferred = ' SELECT COALESCE(SUM(quantity),0) FROM material_stocks
                                  LEFT JOIN internal_material_transfer_items ON material_stocks.stock_id = internal_material_transfer_items.stock_id
                                  WHERE internal_material_transfer_items.source_sub_location_id IN ('.$sub_location_id.')
                                  AND material_stocks.item_id = "'.$this->{$this::DB_TABLE_PK}.'"
        ';

        if(!is_null($from)){
            $internal_transferred .= ' AND date_received >= "'.$from.'" ';
        }
        if(!is_null($to)){
            $internal_transferred .= ' AND date_received <= "'.$to.'" ';
        }


        if($project_id != 'all' && $project_id != 'ALL') {
            if (!is_null($project_id)) {
                $internal_transferred .= ' AND material_stocks.project_id = "' . $project_id . '"';
            } else {
                $internal_transferred .= ' AND material_stocks.project_id IS NULL';
            }
        }

        if($transfer_type == 'external'){
            $sql .= $external_transferred;
        } else if($transfer_type == 'internal'){
            $sql .= $internal_transferred;
        } else {
            $sql = ' ('.$internal_transferred.') + ('.$external_transferred.')';
        }

        $sql = $query_string ? $sql : 'SELECT ('.$sql.') AS transferred_quantity';

        if(!$query_string){
            $query = $this->db->query($sql);
            $results = $query->result();
            return array_shift($results)->transferred_quantity;
        } else {
            return $sql;
        }
    }

    public function sub_location_on_transit_quantity($sub_location_id,$project_id = 'all',$query_string = false){
        $sql = 'SELECT COALESCE(SUM(quantity),0) AS on_transit_quantity FROM external_material_transfer_items
              LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
              WHERE material_item_id = '.$this->{$this::DB_TABLE_PK}.' AND status = "ON TRANSIT" AND source_sub_location_id IN('.$sub_location_id.')
              ';

        if(strtolower($project_id) != 'all'){
            $sql .= ' AND external_material_transfer_items.project_id '.(is_null($project_id) ? ' IS NULL' : ' = '.$project_id);
        }

        if($query_string){
            return $sql;
        } else {
            $query = $this->db->query($sql);
            return $query->row()->on_transit_quantity;
        }

    }

    public function sub_location_used_quantity($sub_location_id,$project_id = 'ALL',$from = null,$to = null,$query_string = false){
        $project_id = $project_id != '' ? $project_id : null;
        $sql = 'SELECT COALESCE(SUM(quantity),0) AS used_quantity FROM material_costs
                WHERE source_sub_location_id IN (' . $sub_location_id . ')
                AND material_item_id = "'.$this->{$this::DB_TABLE_PK}.'"
                ';

        if(!is_null($from)){
           $sql .= ' AND cost_date >= "'.$from.'" ';
        }
        if(!is_null($to)){
           $sql .= ' AND cost_date <= "'.$to.'" ';
        }

        if($project_id != 'ALL' && $project_id != 'all'){
            $sql .= ' AND project_id = "'.$project_id.'" ';
        }


        if(!$query_string){
            $query = $this->db->query($sql);
            $results = $query->result();
            return array_shift($results)->used_quantity;
        } else {
            return $sql;
        }
    }

    public function sub_location_assigned_out_quantity($sub_location_id,$project_id,$from = null,$to = null,$query_string = false){
        $project_id = $project_id != '' ? $project_id : null;
        $sql = 'SELECT COALESCE(SUM(quantity),0) AS assigned_out_quantity
                FROM material_stocks
                LEFT JOIN material_cost_center_assignment_items ON material_stocks.stock_id = material_cost_center_assignment_items.stock_id
                WHERE material_cost_center_assignment_items.material_cost_center_assignment_id IN (
                  SELECT id FROM material_cost_center_assignments WHERE source_project_id '.(is_null($project_id) ? ' IS NULL ' : ' = '.$project_id);
        if(!is_null($from)){
            $sql .= ' AND assignment_date >= "'.$from.'" ';
        }
        if(!is_null($to)){
            $sql .= ' AND assignment_date <= "'.$to.'" ';
        }
        $sql .= '
                ) AND material_stocks.sub_location_id IN ('.$sub_location_id.')
                AND material_stocks.item_id = '.$this->{$this::DB_TABLE_PK};

        if(!$query_string){
            $query = $this->db->query($sql);
            $results = $query->result();
            return array_shift($results)->assigned_out_quantity;
        } else {
            return $sql;
        }
    }

    public function sub_location_disposed_quantity($sub_location_id,$project_id = 'ALL',$from = null,$to = null,$query_string = false){

        $sql = 'SELECT COALESCE(SUM(quantity),0) AS disposed_quantity FROM material_disposal_items
                LEFT JOIN material_disposals ON material_disposal_items.disposal_id = material_disposals.id
                WHERE sub_location_id IN (' . $sub_location_id . ')
                AND material_item_id = "'.$this->{$this::DB_TABLE_PK}.'"
        ';

        if(!is_null($from)){
           $sql .= ' AND disposal_date >= "'.$from.'" ';
        }
        if(!is_null($to)){
           $sql .= ' AND disposal_date <= "'.$to.'" ';
        }

        if($project_id != 'ALL' && $project_id != 'all'){
            if(is_null($project_id)){
                $sql .= ' AND material_disposals.project_id IS NULL ';
            } else {
                $sql .= ' AND material_disposals.project_id = "' . $project_id . '" ';
            }
        }


        if(!$query_string){
            $query = $this->db->query($sql);
            $results = $query->result();
            return array_shift($results)->disposed_quantity;
        } else {
            return $sql;
        }
    }

    public function sub_location_sold_quantity($sub_location_ids,$project_id = 'ALL',$from = null,$to = null,$query_string = false){
        $sql = 'SELECT COALESCE(SUM(quantity),0) AS sold_quantity FROM stock_sales_material_items
                LEFT JOIN stock_sales ON stock_sales_material_items.stock_sale_id = stock_sales.id
                WHERE source_sub_location_id IN('.$sub_location_ids.') AND material_item_id = '.$this->{$this::DB_TABLE_PK};

        if(!is_null($from)){
            $sql .= ' AND sale_date >= "'.$from.'" ';
        }
        if(!is_null($to)){
            $sql .= ' AND sale_date <= "'.$to.'" ';
        }

        if($project_id != 'ALL' && $project_id != 'all'){
            if(is_null($project_id)){
                $sql .= ' AND project_id IS NULL ';
            } else {
                $sql .= ' AND project_id = "' . $project_id . '" ';
            }
        }

        if(!$query_string){
            $query = $this->db->query($sql);
            $results = $query->result();
            return array_shift($results)->sold_quantity;
        } else {
            return $sql;
        }
    }

    public function sub_location_balance($sub_location_id, $project_id = null, $to = null,$transfer_type = 'all'){
        $to = !is_null($to) ? $to : date('Y-m-d');
        $sql = 'SELECT
                  (
                      (
                           '.$this->sub_location_received_quantity($sub_location_id,$project_id,null,$to,true);
            $sql .= ' ) - (
                            (
                                '.$this->sub_location_transferred_out_quantity($sub_location_id,$project_id,null,$to,$transfer_type,true).'
                            ) ';
                if(!is_null($project_id)) {
                $sql .= '  + (
                                '.$this->sub_location_used_quantity($sub_location_id,$project_id,null,$to,true).'
                              )';
                }

                $sql .= '  + (
                                '.$this->sub_location_sold_quantity($sub_location_id,$project_id,null,$to,true).'
                              )';
                $sql .= '  + (
                                '.$this->sub_location_disposed_quantity($sub_location_id,$project_id,null,$to,true).'
                              )';

                if($project_id != 'ALL' && $project_id != 'all'){
                $sql .= '  + (
                                  '.$this->sub_location_assigned_out_quantity($sub_location_id,$project_id,null,$to,true).'
                              )';
                }
        $sql .= ' )
                )  AS quantity_available
            ';
        $query = $this->db->query($sql);
        return round($query->row()->quantity_available,3);
    }

    public function location_balance($project_id, $location_id){
        $sub_location_ids = 'SELECT sub_location_id FROM sub_locations WHERE location_id = '.$location_id;
        return $this->sub_location_balance($sub_location_ids,$project_id,null,'external');
    }

    public function update_average_price($sub_location_id,$new_quantity, $new_price,$project_id,$transaction_datetime = null,$stock_id = null){
        $this->load->model('material_average_price');
        $transaction_date = is_null($transaction_datetime) ? datetime() : $transaction_datetime;
        $average_price = new Material_average_price();
        $average_price->project_id = $project_id;
        $average_price->datetime_updated = datetime();
        $average_price->transaction_date = $transaction_date;
        $average_price->sub_location_id = $sub_location_id;
        $average_price->material_item_id = $this->{$this::DB_TABLE_PK};
        $current_price = $this->sub_location_average_price($sub_location_id,$project_id);
        $average_price->material_stock_id = $stock_id;
        //$current_stock = $this->sub_location_balance($sub_location_id,$project_id) == 0 ? $new_quantity : $this->sub_location_balance($sub_location_id,$project_id) - $new_quantity;
        $current_stock = $this->sub_location_balance($sub_location_id,$project_id) - $new_quantity;
        $average_price->average_price = (($current_stock*$current_price) + ($new_price*$new_quantity)) / ($current_stock + $new_quantity);
        $average_price->save();
    }

    public function sub_location_average_price($sub_location_id,$project_id = null,$date = null){
        $date = is_null($date) ? datetime() : $date;
        $sql = 'SELECT COALESCE(average_price,0) AS average_price  FROM material_average_prices
                WHERE transaction_date <= "'.$date.'" AND material_item_id = "'.$this->{$this::DB_TABLE_PK}.'"
                AND sub_location_id = "'.$sub_location_id.'" ';

        if($project_id != 'all' && $project_id != 'ALL') {
            if (is_null($project_id)) {
                $sql .= ' AND project_id IS NULL';
            } else {
                $sql .= ' AND project_id = "' . $project_id . '"';
            }
        }

        $sql .= ' ORDER BY average_price_id DESC LIMIT 1';
        $query = $this->db->query($sql);

        if($query->num_rows() > 0){
            return $query->row()->average_price;
        } else {
            $this->load->model('material_stock');
            $material_stocks = $this->material_stock->get(1,0,[
                'item_id' => $this->{$this::DB_TABLE_PK},
                'sub_location_id' => $sub_location_id,
                'project_id' => $project_id
            ]);

            if(!empty($material_stocks)){
                $material_stock = array_shift($material_stocks);
                $this->load->model('material_average_price');
                $material_average_price = new Material_average_price();
                $material_average_price->project_id = $material_stock->project_id;
                $material_average_price->average_price = $material_stock->price;
                $material_average_price->transaction_date = $material_stock->date_received;
                $material_average_price->datetime_updated = datetime();
                $material_average_price->sub_location_id = $material_stock->sub_location_id;
                $material_average_price->material_item_id = $material_stock->item_id;
                $material_average_price->save();
                return $material_average_price->average_price;
            } else {
                return 0;
            }
        }
    }

    public function budgeted_quantity_for_project($project_id){
        $sql = '
              SELECT COALESCE(SUM(quantity),0) AS budgeted_quantity FROM material_budgets
              WHERE project_id = "'.$project_id.'"
              AND material_item_id = "'.$this->{$this::DB_TABLE_PK}.'"
            ';
        $query = $this->db->query($sql);
        return $query->row()->budgeted_quantity;
    }

    public function requested_quantity_for_project($project_id){
        $sql = 'SELECT COALESCE(SUM(requested_quantity),0) AS requested_quantity FROM requisition_material_items
                LEFT JOIN requisitions ON requisition_material_items.requisition_id = requisitions.requisition_id
                LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                WHERE requisitions.status != "INCOMPLETE" AND  project_id = "'.$project_id.'"
                AND material_item_id = "'.$this->{$this::DB_TABLE_PK}.'"
                ';
        $query = $this->db->query($sql);
        return $query->row()->requested_quantity;
    }

    public function approved_quantity_for_project($project_id){
        $sql = 'SELECT COALESCE(SUM(approved_quantity),0) AS approved_quantity FROM requisition_approval_material_items
                LEFT JOIN requisition_material_items ON requisition_approval_material_items.requisition_material_item_id = requisition_material_items.id
                LEFT JOIN requisitions ON requisition_material_items.requisition_id = requisitions.requisition_id
                LEFT JOIN project_requisitions ON requisitions.requisition_id = project_requisitions.requisition_id
                WHERE project_id = "'.$project_id.'"
                AND material_item_id = "'.$this->{$this::DB_TABLE_PK}.'"
                AND requisitions.status = "APPROVED"
                ';
        $query = $this->db->query($sql);
        return $query->row()->approved_quantity;
    }

	public function ordered_quantity_for_project($project_id,$issue_date = null){
		$sql = 'SELECT COALESCE(SUM(quantity),0) AS ordered_quantity FROM purchase_order_material_items
                LEFT JOIN purchase_orders ON purchase_order_material_items.order_id = purchase_orders.order_id
                LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
                WHERE status != "CANCELLED" 
                AND project_id = "'.$project_id.'"
                AND material_item_id = "'.$this->{$this::DB_TABLE_PK}.'" ';
		if(!is_null($issue_date)){
			$sql .= ' AND issue_date <= "'.$issue_date.'"';
		}
		$query = $this->db->query($sql);
		return $query->row()->ordered_quantity;
	}

    public function received_quantity_from_orders($project_id = 'all',$date = null){
        $date = !is_null($date) ? $date : date('Y-m-d');
        $sql = 'SELECT COALESCE(SUM(quantity),0) delivered_quantity FROM material_stocks
                  LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                  LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                  LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                  LEFT JOIN purchase_orders ON purchase_order_grns.purchase_order_id = purchase_orders.order_id ';
        if($project_id != 'all' && !is_null($project_id)) {
            $sql .= ' LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
                  WHERE project_purchase_orders.project_id = "' . $project_id . '" AND ';
        } else {
            $sql .= ' WHERE ';
        }
        $sql .= ' date_received <= "'.$date.'"
                  AND material_stocks.item_id = "'.$this->{$this::DB_TABLE_PK}.'"
                ';
        $query = $this->db->query($sql);
        return $query->row()->delivered_quantity;
    }

    public function delivered_quantity_in_site_store_for_project($project_id){
        $sql = 'SELECT COALESCE(SUM(quantity),0) delivered_quantity FROM material_stocks
                  LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                  LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                  LEFT JOIN inventory_locations ON goods_received_notes.location_id = inventory_locations.location_id
                  WHERE material_stocks.item_id = "'.$this->{$this::DB_TABLE_PK}.'"
                  AND inventory_locations.project_id = "'.$project_id.'"
                ';
        $query = $this->db->query($sql);
        return $query->row()->delivered_quantity;
    }

    public function used_quantity_from_site_store_for_project($project_id,$date = null){
        $sql = 'SELECT 
            (
                SELECT COALESCE(SUM(quantity),0) FROM material_costs
                WHERE project_id = "'.$project_id.'"
                AND material_item_id = "'.$this->{$this::DB_TABLE_PK}.'" ';

        if(!is_null($date)) {
            $sql .= '
                AND cost_date <= "' . $date . '" ';
        }
        $sql .= '
            ) AS quantity_used';
        $query = $this->db->query($sql);
        return $query->row()->quantity_used;
    }

    public function budget_material_options($cost_center_level,$cost_center_id){
        $sql = 'SELECT item_name,item_id
                FROM material_items
                WHERE item_id NOT IN(
                    SELECT material_item_id FROM material_budgets
                    WHERE ';
        if($cost_center_level == 'project'){
            $sql .= ' project_id = "'.$cost_center_id.'" AND task_id IS NULL';
        } else {
            $sql .= ' task_id = "'.$cost_center_id.'"';
        }
        $sql .= '
                )
       ';

        $query = $this->db->query($sql);
        $material_items = $query->result();

        $options = '<option value="">&nbsp;</option>';
        foreach($material_items as $item){
            $options .= '<option value="'.$item->item_id.'">'.$item->item_name.'</option>';
        }
        return $options;
    }

    public function last_approved_price($currency_id, $material_item_id = null){
        $material_item_id = !is_null($material_item_id) ? $material_item_id : $this->{$this::DB_TABLE_PK};

        $sql = 'SELECT price AS approved_rate FROM material_stocks
                LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                WHERE  material_stocks.item_id = '.$material_item_id.'
                ORDER BY goods_received_notes.receive_date DESC LIMIT 1
                ';


        $query = $this->db->query($sql);
        if($query->num_rows() < 1){
            return 0;
        } else {
            if($currency_id == 1){
                $exchange_rate = 1;
            } else {
                $this->load->model('exchange_rate_update');
                $updates = $this->exchange_rate_update->get(1,0,[
                    'currency_id' => $currency_id
                ],'update_date DESC');
                $exchange_rate = array_shift($updates)->exchange_rate;
            }
            return $query->row()->approved_rate/$exchange_rate;
        }

    }

    public function material_item_category()
    {
        $this->load->model('Material_item_category');
        $Material_item_category = new Material_item_category();
        $Material_item_category->load($this->category_id);
        return $Material_item_category;
    }

    public function sub_location_opening_quantity($sub_location_ids = 'all',$project_id = 'all'){
        $sql = 'SELECT COALESCE(SUM(quantity),0) AS balance FROM material_stocks
                LEFT JOIN material_opening_stocks ON material_stocks.stock_id = material_opening_stocks.stock_id
                WHERE material_opening_stocks.opening_stock_id IS NOT NULL AND material_stocks.item_id = '.$this->{$this::DB_TABLE_PK};
        if($sub_location_ids != 'all'){
            $sql .= ' AND material_stocks.sub_location_id IN('.$sub_location_ids.') ';
        }
        if(is_null($project_id)){
            $sql .= ' AND material_stocks.project_id IS NULL ';
        } else if($project_id != 'all'){
            $sql .= ' AND material_stocks.project_id = '.$project_id;
        }
        $query = $this->db->query($sql);
        return $query->row()->balance;
    }

    public function sub_location_assigned_in_quantity($sub_location_id, $project_id, $from = null, $to = null,$query_string = false){
        $sql = 'SELECT COALESCE(SUM(quantity),0) AS assigned_in_quantity FROM material_stocks
                LEFT JOIN material_cost_center_assignment_items ON material_stocks.stock_id = material_cost_center_assignment_items.stock_id
                LEFT JOIN material_cost_center_assignments ON material_cost_center_assignment_items.material_cost_center_assignment_id = material_cost_center_assignments.id
                WHERE material_cost_center_assignment_items.id IS NOT NULL AND material_stocks.item_id = "'.$this->{$this::DB_TABLE_PK}.'" 
                AND material_stocks.sub_location_id IN('.$sub_location_id.')';

        if($project_id != 'all'){
            $sql .=  ' AND material_stocks.project_id = '.$project_id;
        }

        if(!is_null($from)){
            $sql .= ' AND assignment_date >= "'.$from.'" ';
        }

        if(!is_null($to)){
            $sql .= ' AND assignment_date <= "'.$to.'" ';
        }
        if($query_string){
            return $sql;
        } else {
            $query = $this->db->query($sql);
            return $query->row()->assigned_in_quantity;
        }
    }

    public function received_from_cash_purchase($sub_location_ids = 'all',$project_id = 'all'){
        $sql = 'SELECT COALESCE(SUM(quantity),0) AS quantity FROM material_stocks
                LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                LEFT JOIN imprest_grns ON goods_received_notes.grn_id = imprest_grns.grn_id
                LEFT JOIN imprest_voucher_retirement_grns ON goods_received_notes.grn_id = imprest_voucher_retirement_grns.grn_id
                WHERE  (imprest_grns.id IS NOT NULL OR imprest_voucher_retirement_grns.id IS NOT NULL) AND material_stocks.item_id =  '.$this->{$this::DB_TABLE_PK};
                if($sub_location_ids != 'all'){
                    $sql .= ' AND sub_location_id IN ('.$sub_location_ids.') ';
                }

                if(is_null($project_id)){
                    $sql .= ' AND project_id IS NULL ';
                } else if(strtolower($project_id) != 'all'){
                    $sql .= ' AND project_id = '.$project_id;
                }
        $query = $this->db->query($sql);
        return $query->row()->quantity;

    }

    public function last_average_price($project_id = null){
        $sql = 'SELECT material_average_prices.average_price FROM material_average_prices WHERE average_price > 0 AND material_item_id = '.$this->{$this::DB_TABLE_PK};
        if(is_null($project_id)){
            $sql .= ' AND project_id IS NULL ';
        } else {
            $sql .= ' AND project_id = '.$project_id;
        }
        $sql .= ' ORDER BY transaction_date DESC LIMIT 1';
        $query = $this->db->query($sql);

        if(!empty($query->result())){
            return $query->row()->average_price;
        } else {
            $this->load->model('material_stock');
            $material_stocks = $this->material_stock->get(1,0,[
                'item_id' => $this->{$this::DB_TABLE_PK},
                'project_id' => $project_id
            ]);

            return !empty($material_stocks) ? array_shift($material_stocks)->price : 0;
        }
    }

    public function material_stock_item($latest = false){
        $where['item_id'] = $this->{$this::DB_TABLE_PK};
        if($latest) {
            $material_stocks = $this->material_stock->get(0, 0, $where, 'stock_id DESC');
        } else {
            $material_stocks = $this->material_stock->get(0, 0, $where, 'stock_id ASC');
        }
        $this->load->model('material_stock');
        if(!empty($material_stocks)) {
            foreach ($material_stocks as $material_stock) {
                $stock_item = new Material_stock();
                $stock_item->load($material_stock->{$material_stock::DB_TABLE_PK});
                return $stock_item;
            }
        } else {
            return false;
        }
    }

    public function received_quantity_from_grns($project_id = 'all',$date = null){
        $date = !is_null($date) ? $date : date('Y-m-d');
        $sql = 'SELECT COALESCE(SUM(material_stocks.quantity),0) delivered_quantity FROM material_stocks
                  LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                  LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                  LEFT JOIN external_material_transfer_grns ON goods_received_notes.grn_id = external_material_transfer_grns.grn_id';

        if($project_id != 'all' && !is_null($project_id)) {
            $sql .= '
                  WHERE material_stocks.project_id = "' . $project_id . '" AND ';
        } else {
            $sql .= ' WHERE ';
        }
        $sql .= ' date_received <= "'.$date.'"
                   AND goods_received_note_material_stock_items.grn_id IS NOT NULL
                   AND external_material_transfer_grns.id IS NULL
                  AND material_stocks.item_id = "'.$this->{$this::DB_TABLE_PK}.'"
                ';
        $query = $this->db->query($sql);
        return $query->row()->delivered_quantity;
    }

    public function projects_with_this_item(){
        $sql = 'SELECT DISTINCT material_stocks.project_id, project_name FROM material_stocks
                LEFT JOIN projects ON material_stocks.project_id = projects.project_id
                WHERE item_id ='.$this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        return $query->result();
    }

}

