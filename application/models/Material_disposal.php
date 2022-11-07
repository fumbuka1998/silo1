<?php
class Material_disposal extends MY_Model
{

    const DB_TABLE = 'material_disposals';
    const DB_TABLE_PK = 'id';

    public $disposal_date;
    public $location_id;
    public $project_id;
    public $created_by;

    public function location_material_disposal_list($limit, $start, $keyword, $order, $location_id)
    {

        $order_string = dataTable_order_string(['disposal_date', 'created_by'], $order, 'disposal_date');

        $where = 'location_id = "' . $location_id . '"';

        if ($keyword != '') {
            $where .= ' AND disposal_date LIKE "%' . $keyword . '%" ';
        }

        $material_disposals = $this->get($limit, $start, $where, $order_string);
        $rows = [];
        $this->load->model('Inventory_location');
        $Inventory_location = new Inventory_location();
        $Inventory_location->load($location_id);
        $data['sub_location_options'] = $Inventory_location->sub_location_options();

        foreach ($material_disposals as $material_disposal) {
            $data['material_disposal'] = $material_disposal;

            $rows[] = [

                custom_standard_date($material_disposal->disposal_date),
                $material_disposal->location()->location_name,
                $material_disposal->employee()->full_name(),
                $this->load->view('inventory/material/disposals/material_disposal_actions', $data, true)

            ];
        }
        $records_filtered = $this->count_rows($where);

        $records_total = $this->count_rows([
            'location_id' => $location_id
        ]);

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function employee()
    {
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->created_by);
        return $employee;
    }

    public function location()
    {
        $this->load->model('inventory_location');
        $location = new Inventory_location();
        $location->load($this->location_id);
        return $location;
    }

    public function project()
    {
        $this->load->model('Project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }

    public function material_items()
    {
        $this->load->model('material_disposal_item');

        $where = ' disposal_id = ' . $this->{$this::DB_TABLE_PK};

        return $this->material_disposal_item->get(0, 0, $where);
    }

    public function stock_items($disposal_id = '')
    {
        $this->load->model('stock_disposal_asset_item');

        $where = ' disposal_id = ' . $this->{$this::DB_TABLE_PK};

        return $this->stock_disposal_asset_item->get(0, 0, $where);
    }

    public function amount()
    {
        $amount = 0;
        foreach ($this->material_items() as $item) {
            $amount += $item->quantity * $item->rate;
        }
        return $amount;
    }
}
