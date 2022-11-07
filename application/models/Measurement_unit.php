<?php

class Measurement_unit extends MY_Model{
    
    const DB_TABLE = 'measurement_units';
    const DB_TABLE_PK = 'unit_id';

    public $name;
    public $symbol;
    public $description;

    public function datatable_list($limit, $start, $keyword, $order){
        //order string
        $order_string = dataTable_order_string(['name','symbol','description'],$order,'name');

        $where = '';
        if ($keyword != '') {
            $where .= 'name LIKE "%' . $keyword . '%" OR symbol LIKE "%' . $keyword . '%" OR description LIKE "%' . $keyword . '%"';
        }

        $measurement_units = $this->get($limit, $start, $where, $order_string);
        $rows = [];
        foreach ($measurement_units as $unit) {
            $data['unit'] = $unit;
            $rows[] = [
                $unit->name,
                $unit->symbol,
                $unit->description,
                $this->load->view('inventory/settings/measurement_units_list_actions', $data, true)
            ];
        }
        $records_filtered = $this->count_rows($where);
        $records_total = $this->count_rows();
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function material_unit_symbol($material_id){
        $sql = 'SELECT symbol FROM measurement_units
                LEFT JOIN material_items ON measurement_units.unit_id = material_items.unit_id
                WHERE material_items.item_id = "' . $material_id. '"
        ';
        $query = $this->db->query($sql);
        $results = $query->result();
        return !empty($results) ? array_shift($results)->symbol : '';
    }

    public function dropdown_options()
    {
        $options[''] = '&nbsp;';
        $units = $this->get(0,0,'','name');
        foreach($units as $unit){
            $options[$unit->{$unit::DB_TABLE_PK}] = $unit->symbol;
        }
        return $options;
    }

    public function excel_dropdown_list(){
        $sql = 'SELECT symbol FROM measurement_units';
        $query = $this->db->query($sql);
        $results = $query->result();
        $list = '';
        foreach ($results as $row){
            $list .= $row->symbol.',';
        }
        return rtrim($list,',');
    }

    public function measurement_unit_details($unit_id)
    {
        $unit = new self();
        $unit->load($unit_id);
        return $unit;
    }
}

