<?php
require 'vendor/autoload.php';

//require_once __DIR__ . '/vendor/autoload.php';
//$mpdf = new \Mpdf\Mpdf();
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;



class Inventory extends MY_Controller
{
    protected function middleware()
    {
        return array();
    }

    public function index()
    {
        $data['number_of_requisitions'] = $this->db->count_all('requisitions');
        $data['number_of_locations'] = $this->db->count_all('inventory_locations');
        $data['number_of_items'] = $this->db->count_all('material_items');
        $data['title'] = 'Inventory';
        $this->load->view('inventory/index', $data);
    }

    private function projects_without_stores()
    {
        $this->load->model('project');
        return $this->project->projects_without_stores();
    }

    public function locations()
    {
        check_permission('Inventory', true);
        $this->load->model('inventory_location');
        $limit = $this->input->post('length');
        if ($limit != '') {
            $posted_params = dataTable_post_params();
            echo $this->inventory_location->locations_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
        } else {
            $data['title'] = 'Locations';
            $data['projects_options'] = $this->projects_without_stores();
            $this->load->view('inventory/locations/index', $data);
        }
    }

    public function save_location($id = 0)
    {
        check_permission('Inventory', true);
        $this->load->model('inventory_location');
        $location = new Inventory_location();
        $edit = $location->load($id);
        $location->location_name = $this->input->post('location_name');
        $location->description = $this->input->post('description');
        if ($location->save()) {
            $action = $edit ? 'Location Update' : 'Location Registration';
            $description = 'Location ' . $location->location_name . ' was ';
            if ($edit) {
                $description .= 'updated';
            } else {
                $description .= 'registered';
                $this->load->model('sub_location');
                $sub_location = new Sub_location();
                $sub_location->location_id = $location->{$location::DB_TABLE_PK};
                $sub_location->sub_location_name = 'Default Sub-location';
                $sub_location->description = 'Default Sub-location for ' . $location->location_name;
                $sub_location->save();
            }
            system_log($action, $description, $location->project_id);
            redirect(base_url('inventory/location_profile/' . $location->{$location::DB_TABLE_PK}));
        }
    }

    public function location_profile($id = 0)
    {
        check_permission('Inventory', true);
        $this->load->model(['inventory_location', 'asset_item', 'material_item_category']);
        $location = new Inventory_location();
        if ($location->load($id) && $location->allowed_access()) {
            $this->load->model(['currency', 'asset', 'stakeholder', 'asset_group', 'material_item', 'asset_item']);
            $data['title'] = $location->location_name;
            $data['location'] = $location;
            $data['currency_options'] = $this->currency->dropdown_options();
            $data['material_options'] = $this->material_item->dropdown_options('all');
            $data['asset_options'] = $this->asset_item->dropdown_options();
            $data['project_options'] = projects_dropdown_options();
            $project = $location->project();
            if ($project) {
                $data['project'] = $project;
            }
            $data['sub_location_options'] = $location->sub_location_options();
            $data['asset_items_options'] = $this->asset_item->dropdown_options();
            $data['asset_stock_options'] = $this->asset->location_asset_options('location', $id, 'all');
            $data['asset_group_options'] = $this->asset_group->dropdown_options();
            $data['material_item_category_options'] = $this->material_item_category->dropdown_options();
            $data['employee_options'] = employee_options(true);
            $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
            $this->load->view('inventory/locations/location_profile', $data);
        } else {
            redirect(base_url());
        }
    }

    public function load_sub_location_options()
    {
        $this->load->model('Inventory_location');
        $Inventory_location = new Inventory_location();
        if ($this->input->post('location_id') != '') {
            $Inventory_location->load($this->input->post('location_id'));
            $return['sub_location_options'] = $Inventory_location->sub_location_dropdown_options();
        } else {
            $return['sub_location_options'] = '<option></option>';
        }

        echo json_encode($return);
    }

    public function delete_location()
    {
        $this->load->model('inventory_location');
        $location = new Inventory_location();
        if ($location->load($this->input->post('location_id'))) {
            $description = 'Location ' . $location->location_name . ' was deleted';
            system_log('Location Delete', $description, $location->project_id);
            $location->delete();
        }
    }

    public function sub_locations_list()
    {
        $this->load->model(['inventory_location']);
        $location = new Inventory_location();
        $location->load($this->input->post('location_id'));
        $data['location'] = $location;
        $data['sub_locations'] = $location->sub_locations($this->input->post('keyword'));
        $data['asset_items_options'] = asset_item_dropdown_options();
        $data['project_options'] = projects_dropdown_options();
        $this->load->view('inventory/locations/sub_locations_list', $data);
    }

    public function save_sub_location()
    {
        $this->load->model('sub_location');
        $sub_location = new Sub_location();
        $edit = $sub_location->load($this->input->post('sub_location_id'));
        $sub_location->sub_location_name = $this->input->post('sub_location_name');
        $sub_location->location_id = $this->input->post('location_id');
        $sub_location->description = $this->input->post('description');
        $sub_location->equipment_id = $this->input->post('equipment_id');
        $sub_location->status = 'ACTIVE';
        if ($sub_location->save()) {
            $location = $sub_location->location();
            $action = $edit ? 'Sub-Location Update' : 'Sub-Location Registration';
            $description = $sub_location->sub_location_name . ' for ' . $location->location_name . ' was ';
            $description .= $edit ? 'Updated' : 'Registered';
            system_log($action, $description, $location->project_id);
        }
    }

    public function delete_sub_location()
    {
        $this->load->model('sub_location');
        $sub_location = new Sub_location();
        if ($sub_location->load($this->input->post('sub_location_id'))) {
            $location = $sub_location->location();
            $description = $sub_location->sub_location_name . ' for ' . $location->location_name . ' was deleted';
            $sub_location->delete();
            system_log('Sub-Location Delete', $description, $location->project_id);
        }
    }

    public function save_material_item($synchronization = false)
    {
        $this->load->model('material_item');
        $item = new Material_item();
        $edit = $item->load($this->input->post('item_id'));
        $item->item_name = $this->input->post('item_name');
        $item->unit_id = $this->input->post('unit_id');
        $item->category_id = $this->input->post('category_id');
        $item->category_id = $item->category_id != '' ? $item->category_id : null;
        $item->part_number = $this->input->post('part_number');
        $item->description = $this->input->post('description');

        if (!empty($_FILES['file'])) {
            $config = [
                'upload_path' => "./images/material_items_thumbnails/",
                'allowed_types' => 'gif|jpg|png|jpeg'
            ];

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file')) {
                $item->delete_thumbnail();
                $item->thumbnail_name = $this->upload->data()['file_name'];
            }
        }

        if ($item->save()) {
            $this->domain_name = $this->config->item('domain_name');
            $domain_name = $this->domain_name != '' ? $this->domain_name : null;
            if (!is_null($domain_name) && $domain_name == "epm_derm") {
                $this->dermstore_url = $this->config->item('dermstore_url');
                $url = $this->dermstore_url != '' ? $this->dermstore_url : null;
            } else if (!is_null($domain_name) && $domain_name == "epm_dermstore") {
                $this->derm_url = $this->config->item('derm_url');
                $url = $this->derm_url != '' ? $this->derm_url : null;
            }
            if (!$synchronization && !is_null($url) && !is_null($domain_name)) {
                $material_item = new Material_item();
                $material_item_id = $this->input->post('item_id');
                $material_item->load($material_item_id);
                $this->load->library('MY_Curl');
                $curl = new MY_Curl();
                $curl->setPost(
                    array(
                        "item_name" => $item->item_name,
                        "unit_id" => $item->unit_id,
                        "category_id" => $item->category_id,
                        "part_number" => $item->part_number,
                        "description" => $item->description,
                        "file" => !empty($_FILES['file']) ? $_FILES['file'] : null
                    )
                );
            }
            $action = $edit ? 'Material Item Update' : 'Material Item Registration';
            $description = 'Material Item ' . $item->item_name . ' was ' . ($edit ? 'updated' : 'registered');
            system_log($action, $description);
        }
    }

    public function material_items()
    {
        check_permission('Inventory', true);
        $limit = $this->input->post('length');

        if ($limit != '') {
            $this->load->model('material_item');
            echo $this->material_item->datatable_items_list();
        } else {
            $this->load->model(['material_item_category', 'measurement_unit', 'project_category']);
            $data['category_nature_options'] = $this->project_category->category_options() + ['unnatured' => 'UN-NATURED'];
            $data['measurement_unit_options'] = $this->measurement_unit->dropdown_options();
            $data['material_item_category_options'] = $this->material_item_category->dropdown_options();
            $data['title'] = 'Items';
            $this->load->view('inventory/material/index', $data);
        }
    }

    public function delete_material_item()
    {
        $this->load->model('material_item');
        $item = new Material_item();
        if ($item->load($this->input->post('item_id'))) {
            $description = 'Material item ' . $item->item_name . ' was deleted';
            $item->delete_thumbnail();
            $item->delete();
            system_log('Material Item Delete', $description);
        }
    }

    public function load_material_unit()
    {
        $this->load->model('measurement_unit');
        echo $this->measurement_unit->material_unit_symbol($this->input->post('material_id'));
    }

    public function load_material_item_categories_options()
    {
        $this->load->model('material_item_category');
        $project_nature_id = $this->input->post('project_nature_id');
        $project_nature_id = $project_nature_id != '' ? $project_nature_id : null;
        echo stringfy_dropdown_options($this->material_item_category->dropdown_options($project_nature_id));
    }

    public function settings()
    {
        $this->load->model('material_item_category');
        $data['title'] = 'Inventory Settings';
        $data['material_category_options'] = $this->material_item_category->dropdown_options();
        $this->load->view('inventory/settings/index', $data);
    }

    public function save_material_item_category()
    {
        $this->load->model('material_item_category');
        $category = new Material_item_category();
        $edit = $category->load($this->input->post('category_id'));
        $category->category_name = $this->input->post('category_name');
        $category->parent_category_id = $this->input->post('parent_category_id');
        $parent = $category->parent_category();
        if ($category->parent_category_id > 0) {
            $category->parent_category_id = $category->parent_category_id;
            $category->tree_level = $parent->tree_level + 1;
            $category->project_nature_id = $parent->project_nature_id;
        } else {
            $category->parent_category_id = null;
            $category->tree_level = 1;
            $category->project_nature_id = null;
        }
        $category->description = $this->input->post('description');
        if ($category->save()) {
            if ($edit) {
                $category->update_children();
            }
            $action = $edit ? 'Material Item Category Update' : 'Material Item Category Addition';
            $description = 'Material Item Category ' . $category->category_name . ' was ' . ($edit ? 'updated' : 'added');
            system_log($action, $description);
        }
    }

    public function material_item_categories()
    {
        $this->load->model('material_item_category');
        echo $this->material_item_category->category_list();
    }

    public function delete_material_item_category()
    {
        $this->load->model('material_item_category');
        $category = new Material_item_category();
        if ($category->load($this->input->post('category_id'))) {
            $description = 'Material Item Category ' . $category->category_name . ' was deleted';
            $category->delete();
            system_log('Material Item Category Delete', $description);
        }
    }

    public function save_measurement_unit()
    {
        $this->load->model('measurement_unit');
        $unit = new Measurement_unit();
        $edit = $unit->load($this->input->post('unit_id'));
        $unit->name = $this->input->post('name');
        $unit->symbol = $this->input->post('symbol');
        $unit->description = $this->input->post('description');
        if ($unit->save()) {
            $action = $edit ? 'Measurement Unit Update' : 'Measurement Unit Addition';
            $description = 'Measurement Unit ' . $unit->name . ' was ' . ($edit ? 'updated' : 'added');
            system_log($action, $description);
        }
    }

    public function measurement_units()
    {
        $this->load->model('measurement_unit');
        $posted_params = dataTable_post_params();
        echo $this->measurement_unit->datatable_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function delete_transfer()
    {
        $this->load->model('external_material_transfer');
        $transfer = new External_material_transfer();
        if ($transfer->load($this->input->post('transfer_id'))) {
            $transfer->delete();
        }
    }

    public function delete_measurement_unit()
    {
        $this->load->model('measurement_unit');
        $unit = new Measurement_unit();
        if ($unit->load($this->input->post('unit_id'))) {
            $description = 'Measurement Unit ' . $unit->name . ' was deleted';
            $unit->delete();
            system_log('Measurement Unit Delete', $description);
        }
    }

    public function get_accessible_parent_categories_options()
    {
        $this->load->model('material_item_category');
        $category = new Material_item_category();
        $category->load($this->input->post('category_id'));
        $accessible_parents = $category->accessible_parents();

        $options = '<option value="">&nbsp;</option>';
        foreach ($accessible_parents as $parent) {
            $options .= '<option value="' . $parent->{$parent::DB_TABLE_PK} . '"  ' . ($parent->{$parent::DB_TABLE_PK} == $category->parent_category_id ? 'selected' : '') . '>' . $parent->category_name . '</option>';
        }
        echo $options;
    }

    public function load_sub_location_opening_stock_material_options()
    {
        $project_id = $this->input->post('project_id');
        $sub_location_id = $this->input->post('sub_location_id');
        $sql = 'SELECT item_name,item_id FROM material_items
                WHERE item_id NOT IN (
                  SELECT item_id FROM material_stocks
                  WHERE sub_location_id = "' . $sub_location_id . '"';

        if (trim($project_id) != '') {
            $sql .= ' AND project_id = "' . $project_id . '" ';
        } else {
            $sql .= ' AND project_id IS NULL ';
        }

        $sql .= '
                  GROUP BY sub_location_id
                )
                ';

        $options = '<option value="">&nbsp;</option>';

        $query = $this->db->query($sql);
        $results = $query->result();
        foreach ($results as $row) {
            $options .= '<option value="' . $row->item_id . '">' . $row->item_name . '</option>';
        }
        echo $options;
    }

    public function save_material_opening_stock()
    {
        $this->load->model(['material_stock', 'material_opening_stock']);
        $project_id = $this->input->post('project_id');
        $sub_location_id = $this->input->post('sub_location_id');
        $quantities = $this->input->post('quantities');
        $date = $this->input->post('date');
        $item_ids = $this->input->post('item_ids');
        $prices = $this->input->post('prices');
        $remarks = $this->input->post('remarks');
        foreach ($quantities as $index => $quantity) {
            $stock = new Material_stock();
            $stock->project_id = $project_id;
            $stock->item_id = $item_ids[$index];
            $stock->project_id = $stock->project_id != '0' && $stock->project_id != '' ? $stock->project_id : null;
            $stock->date_received = datetime(strtotime($date));
            $stock->description = $remarks[$index];
            $stock->quantity = $quantity;
            $stock->receiver_id = $this->session->userdata('employee_id');
            $stock->price = $prices[$index];
            $stock->sub_location_id = $sub_location_id;
            if ($stock->save()) {
                $stock->update_average_price();
                $opening_stock = new Material_opening_stock();
                $opening_stock->sub_location_id = $sub_location_id;
                $opening_stock->item_id = $stock->item_id;
                $opening_stock->project_id = $stock->project_id;
                $opening_stock->stock_id = $stock->{$stock::DB_TABLE_PK};
                $opening_stock->save();
            }
        }
    }

    public function sub_location_material_stock($sub_location_id)
    {
        $this->load->model('material_item');
        $posted_params = dataTable_post_params();
        echo $this->material_item->sub_location_stock($sub_location_id, $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function load_material_average_price()
    {
        $this->load->model('material_item');
        $material_item = new Material_item();
        $material_item->load($this->input->post('material_id'));
        $project_id = $this->input->post('project_id');
        $project_id = $project_id != '' && $project_id != '0' ? $project_id : null;
        echo $material_item->sub_location_average_price($this->input->post('sub_location_id'), $project_id);
    }

    public function location_material_stock($location_id)
    {
        $limit = $this->input->post('length');
        $this->load->model('material_item');
        $keyword = $this->input->post('search')['value'];
        $start = $this->input->post('start');
        $order = $this->input->post('order')[0];
        echo $this->material_item->location_stock($location_id, $limit, $start, $keyword, $order);
    }

    public function load_external_material_form_requirements()
    {
        $source_id = $this->input->post('source_id');
        $destination_id = $this->input->post('destination_id');
        $data['material_options'] = $this->external_material_transfer_material_options($source_id);
        $data['destination_options'] = $this->external_material_transfer_destination_options($source_id, $destination_id);
        echo json_encode($data);
    }

    private function external_material_transfer_material_options($source_id)
    {
        $options = '<option value="">&nbsp;</option>';
        $sql = 'SELECT * FROM
                (
                    SELECT material_items.item_id,material_items.item_name,
                    (
                        (
                            SELECT COALESCE(SUM(quantity),0)
                            FROM material_stocks
                            LEFT JOIN sub_locations ON material_stocks.sub_location_id = sub_locations.sub_location_id
                            LEFT JOIN inventory_locations ON sub_locations.location_id = inventory_locations.location_id
                            WHERE inventory_locations.location_id = "' . $source_id . '"
                            AND item_id = material_items.item_id
                        ) - (
                            SELECT COALESCE(SUM(quantity),0) FROM material_stocks
                            LEFT JOIN internal_material_transfer_items ON material_stocks.stock_id = internal_material_transfer_items.stock_id
                            LEFT JOIN sub_locations ON internal_material_transfer_items.source_sub_location_id = sub_locations.sub_location_id
                            LEFT JOIN inventory_locations ON sub_locations.location_id = inventory_locations.location_id
                            WHERE inventory_locations.location_id = "' . $source_id . '"
                            AND material_stocks.item_id = material_items.item_id
                        )
                    ) AS quantity_available
                    FROM material_items
                ) AS stock
                WHERE quantity_available > "0"
            ';
        $query = $this->db->query($sql);
        $results = $query->result();
        foreach ($results as $result) {
            $options .= '<option value="' . $result->item_id . '">' . $result->item_name . '</option>';
        }
        return $options;
    }

    private function external_material_transfer_destination_options($source_id, $destination_id = '')
    {
        $options = '<option value="">&nbsp;</option>';
        $sql = 'SELECT location_id,location_name FROM inventory_locations WHERE location_id != "' . $source_id . '"';
        $query = $this->db->query($sql);
        $results = $query->result();
        foreach ($results as $result) {
            $options .= '<option value="' . $result->location_id . '"';
            if ($result->location_id == $destination_id) {
                $options .= 'selected = "selected"';
            }
            $options .= '>' . $result->location_name . '</option>';
        }
        return $options;
    }

    public function validate_sub_store_material_quantity()
    {
        $this->load->model('material_item');
        $material_item = new Material_item();
        $sub_location_id = $this->input->post('sub_location_id');
        $material_id = $this->input->post('material_id');
        $material_item->load($material_id);
        $project_id = $this->input->post('project_id');
        $project_id = $project_id != '' ? $project_id : null;
        echo $material_item->sub_location_balance($sub_location_id, $project_id);
    }

    public function check_store_available_material_quantity()
    {
        $this->load->model(['material_item', 'inventory_location']);
        $material_item = new Material_item();
        $location = new Inventory_location();
        $location->load($this->input->post('location_id'));
        $sub_location_ids = $location->sub_location_ids_query();
        $material_id = $this->input->post('material_id');
        $material_item->load($material_id);
        $project_id = $this->input->post('project_id');
        $approval_module_id = $this->input->post('approval_module_id');
        if ($approval_module_id == '2' && $project_id != '') {
            $project_materials = $location->total_material_item_quantity($project_id, $material_item);
            $unassigned_materials = $location->total_material_item_quantity(null, $material_item);
            echo $project_materials + $unassigned_materials;
            //echo $material_item->sub_location_balance($sub_location_ids, $project_id) + $material_item->sub_location_balance($sub_location_ids, null,null,'external');
        } else if ($approval_module_id == '1') {
            echo $location->total_material_item_quantity(null, $material_item);
            //echo $material_item->sub_location_balance($sub_location_ids, null,null,'external');
        } else {
            echo 0;
        }
    }

    public function save_external_material_transfer()
    {
        $quantities = $this->input->post('quantities');

        if (!empty($quantities)) {
            $this->load->model('external_material_transfer');
            $transfer = new External_material_transfer();
            $edit = $transfer->load($this->input->post('transfer_id'));
            $transfer->destination_location_id = $this->input->post('destination_location_id');
            $transfer->source_location_id = $this->input->post('source_location_id');
            $transfer->transfer_date = $this->input->post('transfer_date');
            $transfer->comments = $this->input->post('comments');
            $transfer->sender_id = $this->session->userdata('employee_id');
            $transfer->project_id = $this->input->post('project_id');
            $transfer->vehicle_number = $this->input->post('vehicle_number');
            $transfer->driver_name = $this->input->post('driver_name');
            $transfer->project_id = $transfer->project_id != '' ? $transfer->project_id : null;
            $transfer->status = 'ON TRANSIT';
            if ($transfer->save()) {
                if ($this->input->post('is_transfer_order') != 'false') {
                    $this->load->model('transferred_transfer_order');
                    $transferred_transfer_order = new Transferred_transfer_order();
                    $transferred_transfer_order->transfer_id = $transfer->{$transfer::DB_TABLE_PK};
                    $transferred_transfer_order->requisition_approval_id = $this->input->post('requisition_approval_id');
                    $transferred_transfer_order->save();
                }

                $transfer->clear_items();
                $this->load->model(['external_material_transfer_item', 'external_transfer_asset_item', 'asset']);
                foreach ($quantities as $index => $quantity) {
                    $item_type = $this->input->post('item_types')[$index];
                    if ($item_type == 'material') {
                        if ($quantity > 0) {
                            $item = new External_material_transfer_item();
                            $item->material_item_id = $this->input->post('material_item_ids')[$index];
                            $item->quantity = $quantity;
                            $item->project_id = $transfer->project_id;
                            $item->project_id = $item->project_id != '0' && $item->project_id != '' ? $item->project_id : null;
                            $item->price = $this->input->post('prices')[$index];
                            $item->source_sub_location_id = $this->input->post('source_sub_location_ids')[$index];
                        }
                    } else {
                        $item = new External_transfer_asset_item();
                        $asset = new Asset();
                        $asset->load($this->input->post('asset_ids')[$index]);
                        $latest_history = $asset->latest_sub_location_history();
                        $item->source_sub_location_history_id = $latest_history->{$latest_history::DB_TABLE_PK};
                    }
                    $item->transfer_id = $transfer->{$transfer::DB_TABLE_PK};
                    $item->remarks = $this->input->post('remarks')[$index];
                    $item->save();
                }

                $source_location = $transfer->source();
                $destination_location = $transfer->destination();
                $action = $edit ? 'External Material Transfer Update' : 'External Material Transfer Submission';
                $description = 'An External Material Transfer from ' . $source_location->location_name . ' to ' . $destination_location->location_name . ' with number ' . $transfer->transfer_number() . ' was ';
                $description .= $edit ? ' updated ' : 'submitted';
                $project_id = $destination_location->project_id != null ? $destination_location->project_id : $source_location->project_id;
                system_log($action, $description, $project_id);
            }
        }
    }

    public function location_material_transfers($location_id = 0)
    {
        $this->load->model('external_material_transfer');
        $posted_params = dataTable_post_params();
        echo $this->external_material_transfer->location_material_transfers_list($location_id, $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function preview_external_material_transfer_sheet($id = 0)
    {
        $this->load->model('external_material_transfer');
        $transfer = new External_material_transfer();
        if ($transfer->load($id)) {
            $data['transfer'] = $transfer;

            $html = $this->load->view('inventory/documents/external_material_transfer_sheet', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            //generate the PDF!
            $pdf->WriteHTML($html);
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
            $pdf->setFooter($footercontents);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('External_material_transfer_' . $transfer->transfer_number() . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function preview_external_material_transfer_delivery_form($id = 0)
    {
        $this->load->model('external_material_transfer');
        $transfer = new External_material_transfer();
        if ($transfer->load($id)) {
            $data['transfer'] = $transfer;

            $html = $this->load->view('inventory/documents/external_material_transfer_delivery_form', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            //generate the PDF!
            $pdf->WriteHTML($html);
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
            $pdf->setFooter($footercontents);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('External_material_transfer_delivery_form' . $transfer->transfer_number() . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function receive_external_material_transfer()
    {
        $this->load->model('goods_received_note');
        $grn = new Goods_received_note();
        $grn->location_id = $this->input->post('location_id');
        $grn->receive_date = $this->input->post('receive_date');
        $grn->receiver_id = $this->session->userdata('employee_id');
        $grn->comments = $this->input->post('comments');
        if ($grn->save()) {
            $this->load->model('external_material_transfer');
            $transfer = new External_material_transfer();
            $transfer->load($this->input->post('transfer_id'));
            $transfer->status = 'RECEIVED';

            $this->load->model('external_material_transfer_grn');
            $transfer_grn = new External_material_transfer_grn();
            $transfer_grn->transfer_id = $transfer->{$transfer::DB_TABLE_PK};
            $transfer_grn->grn_id = $grn->{$grn::DB_TABLE_PK};
            $transfer_grn->save();

            //Populate the stock
            $receiving_sub_location_id = $this->input->post('receiving_sub_location_id');
            $material_ids = $this->input->post('material_ids');
            $asset_ids = $this->input->post('asset_ids');
            $this->load->model(['material_stock', 'goods_received_note_material_stock_item', 'Asset_sub_location_history', 'Grn_asset_sub_location_history', 'Asset']);
            $item_types = $this->input->post('item_types');

            foreach ($item_types as $index => $item_type) {
                if ($item_type == 'material') {
                    $stock = new Material_stock();
                    $stock->date_received = $grn->receive_date;
                    $stock->item_id = $material_ids[$index];
                    $stock->project_id = $this->input->post('project_ids')[$index];
                    $stock->project_id = intval($stock->project_id) > 0 ? $stock->project_id : null;
                    $stock->quantity = $this->input->post('quantities')[$index];
                    $stock->price = $this->input->post('prices')[$index];
                    $stock->sub_location_id = $receiving_sub_location_id;
                    $stock->description = 'Received under GRN No ' . $grn->grn_number();
                    $stock->receiver_id = $grn->receiver_id;

                    if ($stock->save()) {
                        $stock->update_average_price();
                        $grn_stock_item = new Goods_received_note_material_stock_item();
                        $grn_stock_item->grn_id = $grn->{$grn::DB_TABLE_PK};
                        $grn_stock_item->stock_id = $stock->{$stock::DB_TABLE_PK};
                        $grn_stock_item->remarks = $this->input->post('remarks')[$index];
                        $grn_stock_item->save();
                    }
                } else if ($item_type == 'asset') {
                    for ($i = 0; $i < count($asset_ids[$index]); $i++) {
                        $asset_id = $asset_ids[$index];
                        if ($asset_id != '') {
                            $asset = new Asset();
                            $asset->load($asset_id);
                            $last_history = $asset->latest_sub_location_history();
                            $asset_sub_location_history = new Asset_sub_location_history();
                            $asset_sub_location_history->received_date = $grn->receive_date;
                            $asset_sub_location_history->asset_id = $asset_id;
                            $asset_sub_location_history->book_value = $last_history->book_value;
                            $asset_sub_location_history->project_id = $this->input->post('project_ids')[$index];
                            $asset_sub_location_history->project_id = intval($transfer->project_id) > 0 ? $transfer->project_id : null;
                            $asset_sub_location_history->sub_location_id = $this->input->post('receiving_sub_location_id');
                            $asset_sub_location_history->description = 'Received under GRN No ' . $grn->grn_number();
                            $asset_sub_location_history->created_by = $this->session->userdata('employee_id');

                            if ($asset_sub_location_history->save()) {
                                $grn_asset_item = new Grn_asset_sub_location_history();
                                $grn_asset_item->asset_sub_location_history_id = $asset_sub_location_history->{$asset_sub_location_history::DB_TABLE_PK};
                                $grn_asset_item->grn_id = $transfer_grn->grn_id;
                                $grn_asset_item->save();
                            }
                        }
                    }
                }
            }

            if ($transfer->save()) {
                $source_location = $transfer->source();
                $destination_location = $transfer->destination();
                $description = 'An External Material Transfer from ' . $source_location->location_name . ' to ' . $destination_location->location_name . ' with number ' . $transfer->transfer_number() . ' was received';
                $project_id = $destination_location->project_id != null ? $destination_location->project_id : $source_location->project_id;
                system_log('External Material Transfer Receive', $description, $project_id);
                $sender = $transfer->sender();
                $message = 'The external material transfer you issued to ' . $destination_location->location_name;
                $message .= ' has been received ' . standard_datetime();
                //send_sms($sender->phone, $message);
            }
        }
    }

    public function preview_grn($grn_id = 0)
    {
        $this->load->model('goods_received_note');
        $grn = new Goods_received_note();
        if ($grn->load($grn_id)) {
            $data['grn'] = $grn;
            $data['order_grn'] = $grn->purchase_order_grn();
            $data['imprest_voucher_grn'] = $grn->imprest_voucher_retirement_grn();
            $data['is_site_grn'] = $grn->is_site_grn();
            $data['transfer_grn'] = $grn->transfer_grn();
            $data['imprest_grn'] = $grn->imprest_grn();
            $data['unprocured_grn'] = $grn->unprocured_grn();

            $html = $this->load->view('inventory/documents/goods_received_note', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
            $pdf->setFooter($footercontents);

            //generate the PDF!
            $pdf->WriteHTML($html);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('GRN_' . add_leading_zeros($grn->{$grn::DB_TABLE_PK}) . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function load_sub_location_available_material_options()
    {
        $this->load->model(['sub_location', 'material_item']);
        $sub_location = new Sub_location();
        $sub_location->load($this->input->post('source_sub_location_id'));
        $material_items = $sub_location->material_items();

        $options = '<option value="">&nbsp;</option>';
        foreach ($material_items as $material_item) {
            $options .= '<option value ="' . $material_item->{$material_item::DB_TABLE_PK} . '">' . $material_item->item_name . '</option>';
        }
        echo $options;
    }

    public function load_sub_location_material_transfer_project_options()
    {
        $material_id = $this->input->post('material_item_id');
        $sub_location_id = $this->input->post('sub_location_id');
        $options = '<option value="">&nbsp;</option>';

        $sql = 'SELECT * FROM (
                    SELECT (
                        (
                            SELECT COALESCE(SUM(quantity),0)
                            FROM material_stocks
                            WHERE sub_location_id = "' . $sub_location_id . '"
                            AND  item_id = "' . $material_id . '"
                            AND project_id IS NULL
                        ) - (
                        (
                            SELECT COALESCE(SUM(quantity),0) FROM material_stocks
                            LEFT JOIN internal_material_transfer_items ON material_stocks.stock_id = internal_material_transfer_items.stock_id
                            WHERE internal_material_transfer_items.source_sub_location_id = "' . $sub_location_id . '"
                            AND material_stocks.item_id = "' . $material_id . '"
                            AND material_stocks.project_id IS NULL
                        ) + (
                            SELECT COALESCE(SUM(quantity),0)
                            FROM external_material_transfer_items
                            LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                            WHERE source_sub_location_id = "' . $sub_location_id . '"
                            AND external_material_transfer_items.material_item_id = "' . $material_id . '"
                            AND external_material_transfer_items.project_id IS NULL
                            AND external_material_transfers.status != "CANCELLED"
                            )
                        )
                    ) AS quantity_available
                ) AS stock
                LIMIT 1
            ';

        $query = $this->db->query($sql);
        $quantity_available = $query->row()->quantity_available;

        if ($quantity_available > 0) {
            $options .= '<option value="0">UNSPECIFIED</option>';
        }

        $sql = 'SELECT * FROM
                (
                    SELECT projects.project_id,projects.project_name,
                    (
                        (
                            SELECT COALESCE(SUM(quantity),0)
                            FROM material_stocks
                            WHERE sub_location_id = "' . $sub_location_id . '"
                            AND  item_id = "' . $material_id . '"
                            AND project_id = projects.project_id
                        ) - (
                        (
                            SELECT COALESCE(SUM(quantity),0) FROM material_stocks
                            LEFT JOIN internal_material_transfer_items ON material_stocks.stock_id = internal_material_transfer_items.stock_id
                            WHERE internal_material_transfer_items.source_sub_location_id = "' . $sub_location_id . '"
                            AND material_stocks.item_id = "' . $material_id . '"
                            AND material_stocks.project_id = projects.project_id
                        ) + (
                            SELECT COALESCE(SUM(quantity),0)
                            FROM external_material_transfer_items
                            LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                            WHERE source_sub_location_id = "' . $sub_location_id . '"
                            AND external_material_transfer_items.material_item_id = "' . $material_id . '"
                            AND external_material_transfer_items.project_id = projects.project_id
                            AND external_material_transfers.status != "CANCELLED"
                            )
                        )
                    ) AS quantity_available
                    FROM projects
                ) AS stock
                WHERE quantity_available > "0"
                GROUP BY project_id
            ';

        $query = $this->db->query($sql);
        $results = $query->result();
        foreach ($results as $row) {
            $options .= '<option value="' . $row->project_id . '">' . $row->project_name . '</option>';
        }

        echo $options;
    }

    public function save_internal_material_transfer()
    {
        $this->load->model('internal_material_transfer');
        $transfer = new Internal_material_transfer();
        $transfer->location_id = $this->input->post('location_id');
        $transfer->employee_id = $this->session->userdata('employee_id');
        $transfer->transfer_date = $this->input->post('transfer_date');
        $transfer->receiver = $this->input->post('receiver');
        $transfer->comments = $this->input->post('comments');
        $transfer->project_id = $this->input->post('project_id');
        $transfer->project_id = $transfer->project_id != '' ? $transfer->project_id : null;
        if ($transfer->save()) {
            $this->load->model(['material_stock', 'internal_material_transfer_item', 'internal_transfer_asset_item', 'asset_sub_location_history']);
            $item_types = $this->input->post('item_types');

            foreach ($item_types as $index => $item_type) {
                if ($item_type == 'material') {
                    $stock = new Material_stock();
                    $transfer_item = new Internal_material_transfer_item();
                    $transfer_item->source_sub_location_id = $this->input->post('source_sub_location_ids')[$index];

                    $stock->sub_location_id = $this->input->post('destination_sub_location_ids')[$index];
                    $stock->item_id = $this->input->post('material_item_ids')[$index];
                    $material_item = $stock->material_item();
                    $stock->quantity = $this->input->post('quantities')[$index];
                    $stock->date_received = $transfer->transfer_date;
                    $stock->description = 'Received under Internal Material Transfer No ' . $transfer->transfer_number();
                    $stock->receiver_id = $this->session->userdata('employee_id');
                    $stock->project_id = $transfer->project_id;
                    $stock->price = $material_item->sub_location_average_price($transfer_item->source_sub_location_id, $stock->project_id);
                    if ($stock->save()) {
                        $stock->update_average_price();
                        $transfer_item->stock_id = $stock->{$stock::DB_TABLE_PK};
                        $transfer_item->transfer_id = $transfer->{$transfer::DB_TABLE_PK};
                        $transfer_item->remarks = $this->input->post('remarks')[$index];
                        $transfer_item->save();
                    }
                } else {
                    $asset_sub_location_history = new Asset_sub_location_history();
                    $asset_sub_location_history->sub_location_id = $this->input->post('destination_sub_location_ids')[$index];
                    $asset_sub_location_history->project_id = $transfer->project_id;
                    $asset_sub_location_history->asset_id = $this->input->post('asset_item_ids')[$index];
                    $asset_sub_location_history->book_value = $asset_sub_location_history->asset()->book_value;
                    $asset_sub_location_history->description = 'Received under Transfer ' . $transfer->transfer_number();
                    $asset_sub_location_history->received_date = $transfer->transfer_date;
                    $asset_sub_location_history->created_by = $transfer->employee_id;
                    if ($asset_sub_location_history->save()) {
                        $transfer_item = new Internal_transfer_asset_item();
                        $transfer_item->source_sub_location_id = $this->input->post('source_sub_location_ids')[$index];
                        $transfer_item->asset_sub_location_history_id = $asset_sub_location_history->{$asset_sub_location_history::DB_TABLE_PK};
                        $transfer_item->remarks = $this->input->post('remarks')[$index];
                        $transfer_item->transfer_id = $transfer->{$transfer::DB_TABLE_PK};
                        $transfer_item->save();
                    }
                }
            }

            $location = $transfer->location();
            $description = 'An Internal Material Transfer under ' . $location->location_name . ' has been done';
            system_log('Internal Material Transfer Submission', $description, $location->project_id);
        }
    }

    public function preview_internal_material_transfer($transfer_id = 0)
    {
        $this->load->model('internal_material_transfer');
        $transfer = new Internal_material_transfer();
        if ($transfer->load($transfer_id)) {
            $data['transfer'] = $transfer;
            $html = $this->load->view('inventory/documents/internal_transfer_sheet', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
            $pdf->setFooter($footercontents);
            //generate the PDF!
            $pdf->WriteHTML($html);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('Internal_transfer_' . add_leading_zeros($transfer->transfer_number()) . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function pdf_material_items()
    {
        $this->load->model('material_item');
        $category_id = $this->input->post('category_id');
        $category_id = $category_id != '' ? $category_id : null;
        $data['material_items'] = $this->material_item->items_list($category_id);
        $project_nature_id = $this->input->post('project_nature_id');
        if ($project_nature_id != '') {
            if ($project_nature_id != 'unnatured') {
                $this->load->model('project_category');
                $nature = new Project_category();
                $nature->load($project_nature_id);
                $data['project_nature_name'] = $nature->category_name;
            } else {
                $data['project_nature_name'] = 'UN-NATURED';
            }
        }

        if ($category_id != '') {
            $this->load->model('material_item_category');
            $category = new Material_item_category();
            $category->load($category_id);
            $data['category_name'] = $category->category_name;
        }

        $html = $this->load->view('inventory/documents/material_items_list', $data, true);

        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();
        $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
        $pdf->setFooter($footercontents);
        //generate the PDF!
        $pdf->WriteHTML($html);
        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
        $pdf->Output('Material_Items_List' . date('Y-m-d') . '.pdf', 'I');
    }

    public function location_transfer_orders($location_id = 0)
    {
        $this->load->model('external_material_transfer');
        $posted_params = dataTable_post_params();
        echo $this->external_material_transfer->location_transfer_orders_list($location_id, $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function location_grns($location_id = 0)
    {
        $limit = $this->input->post('length');
        $this->load->model('goods_received_note');
        $keyword = $this->input->post('search')['value'];
        $start = $this->input->post('start');

        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        switch ($order_column) {
            case 0;
                $order_column = 'receive_date';
                break;
            case 1;
                $order_column = 'grn_id';
                break;
            case 4;
                $order_column = 'comments';
                break;
            default:
                $order_column = 'receive_date';
        }

        $order_string = $order_column . ' ' . $order_dir;

        $sql = 'SELECT grn_id,receive_date,comments
                FROM goods_received_notes
                WHERE location_id = "' . $location_id . '"
            ';

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        if ($keyword != '') {
            $sql .= ' AND (grn_id LIKE "%' . $keyword . '%" OR receive_date LIKE "%' . $keyword . '%"  OR comments LIKE "%' . $keyword . '%" ) ';
        }

        $query = $this->db->query($sql);
        $records_filtered = $query->num_rows();

        $sql .= " ORDER BY " . $order_string . " LIMIT " . $limit . " OFFSET " . $start;

        $query = $this->db->query($sql);

        $results = $query->result();
        $rows = [];
        foreach ($results as $row) {
            $grn = new Goods_received_note();
            $grn->load($row->grn_id);
            $rows[] = [
                custom_standard_date($row->receive_date),
                $grn->grn_number(),
                $grn->source_name(),
                $grn->reference(),
                $grn->comments,
                anchor(
                    base_url('inventory/preview_grn/' . $row->grn_id),
                    '<i class="fa fa-eye"></i> Preview',
                    ' class="btn btn-xs btn-default pull-right" target="_blank"'
                )

            ];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);
    }



   

    private function serialize_material_item_category_ids($categories)
    {
        $ids = [];
        foreach ($categories as $category) {
            array_push($ids, $category->{$category::DB_TABLE_PK});
            $children = $category->children();
            if (sizeof($children) > 0) {
                array_push($ids, $this->serialize_material_item_category_ids($children));
            }
        }
        return $ids;
    }

    public function download_material_registration_excel_template(){
        //load our new PHPExcel library
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);

        $active_sheet = $this->excel->getActiveSheet();
        //$active_sheet->setTitle('Material Registration');
        //Protect Sheet
        $active_sheet->getProtection()->setPassword('material@registration12');
        $active_sheet->getProtection()->setSheet(true);


        $this->load->model(['material_item_category', 'measurement_unit']);
        $uom_dropdown = $this->measurement_unit->excel_dropdown_list();
        $categories = $this->material_item_category->get(0, 0, ['tree_level' => '1']);
        $hex_color = 'pink';

        $active_sheet->getStyle('A1:D202')->applyFromArray([
            'fill' => [
                'type' => Fill::FILL_SOLID,
                'color' => ['rgb' => $hex_color],
            ]
        ]);

        $style['column_title'] = [
            'fill' => [
                'type' => Fill::FILL_SOLID,
                'color' => ['rgb' => '6fa8dc'],
            ]
        ];

        $active_sheet->getStyle('A2:D2')->applyFromArray($style['column_title']);
        

        $spreadsheet = new Spreadsheet();
        $active_sheet = $spreadsheet->getActiveSheet();
        $active_sheet->setTitle('Material Registration');
        $active_sheet->setCellValue('A1', 'UNCATEGORIZED');
        $active_sheet->setCellValue('A2', 'Material Item');
        $active_sheet->setCellValue('B2', 'Measurement Unit');
        $active_sheet->setCellValue('C2', 'Part Number');
        $active_sheet->setCellValue('D2', 'Description');

        //Unprotect Editable Cells
        $active_sheet->getStyle('A3:D202')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

        //Add UOM dropdown options for uncategorized material
        for ($i = 3; $i <= 202; $i++) {

            $objValidation = $active_sheet->getCell('B' . $i)->getDataValidation();
            $objValidation->setType(DataValidation::TYPE_LIST);
            $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $objValidation->setAllowBlank(false);
            $objValidation->setShowInputMessage(true);
            $objValidation->setShowErrorMessage(true);
            $objValidation->setShowDropDown(true);
            $objValidation->setErrorTitle('Input error');
            $objValidation->setError('Value is not in list.');
            $objValidation->setPromptTitle('Pick from list');
            $objValidation->setPrompt('Please pick a value from the drop-down list.');
            $objValidation->setFormula1('"' . $uom_dropdown . '"');
        }

                $category_column_index = 'E';
                $category_ids   = $this->serialize_material_item_category_ids($categories);

                $category_ids = new RecursiveIteratorIterator(new RecursiveArrayIterator($category_ids));
        
                foreach ($category_ids as $category_id) {
                    $category_column_index++;
                    $category_start_column = $category_column_index;
                    $hex_color = '9bc4c6';
                    $category = new Material_item_category();
                    $category->load($category_id);
                    $hex_color = dechex(hexdec($hex_color) + (96 * $category->tree_level / 2));
                    $font_size = 18 - ($category->tree_level);
        
                    $active_sheet->setCellValue($category_column_index . '1', $category_id);
                    $active_sheet->setCellValue($category_column_index . '2', 'Item Name');
                    $category_column_index++;
                    $active_sheet->setCellValue($category_column_index . '1', $category->category_name);
                    $active_sheet->setCellValue($category_column_index . '2', 'Measurement Unit');

                    //Add UOM dropdown options
                for ($i = 3; $i <= 202; $i++) {

                    $objValidation = $active_sheet->getCell($category_column_index . $i)->getDataValidation();
                    $objValidation->setType(DataValidation::TYPE_LIST);
                    $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $objValidation->setAllowBlank(false);
                    $objValidation->setShowInputMessage(true);
                    $objValidation->setShowErrorMessage(true);
                    $objValidation->setShowDropDown(true);
                    $objValidation->setErrorTitle('Input error');
                    $objValidation->setError('Value is not in list.');
                    $objValidation->setPromptTitle('Pick from list');
                    $objValidation->setPrompt('Please pick a value from the drop-down list.');
                    $objValidation->setFormula1('"' . $uom_dropdown . '"');
                 }
   
                    $category_column_index++;
                    $active_sheet->setCellValue($category_column_index . '1', 'LEVEL ' . $category->tree_level);
                    $active_sheet->setCellValue($category_column_index . '2', 'Part Number');
                    $category_column_index++;
                    $active_sheet->setCellValue($category_column_index . '2', 'Description');
        
                    $category_end_column = $category_column_index;
                    //$category_column_index++;

                    $active_sheet->getStyle($category_start_column . '1:' . $category_end_column . '202')->applyFromArray([
                        'fill' => [
                         'type' => Fill::FILL_SOLID,
                         'color' => ['rgb' => $hex_color],
                            ]
                         ]);
                    $active_sheet->getStyle($category_start_column . '1:' . $category_end_column . '1')->applyFromArray([
                        'font' => [
                         'size' => $font_size
                         ]
                        ]);

                    $active_sheet->getStyle($category_start_column . '2:' . $category_end_column . '2')->applyFromArray($style['column_title']);
                    $category_column_index++;

                    //Unprotect Editable Cells
                     //$active_sheet->getStyle($category_start_column . '3:' . $category_end_column . '202');

                      //Unprotect Editable Cells
                     $active_sheet->getStyle($category_start_column . '3:' . $category_end_column . '202')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
 
                }

                for ($col_index = 'A'; $col_index !== $category_column_index; $col_index++) {
                    $active_sheet->getColumnDimension($col_index)->setAutoSize(TRUE);
                }

      
        $active_sheet->getStyle('A1:' . $category_column_index . '202')->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => Border::BORDER_THIN,
                )
            )
        ]);

        $style['column_heading'] = [
            'font' => [
                'bold'  => true,
                'italic' => true
            ]
        ];

        //Start Initial column headers
        $active_sheet->getStyle('A1:' . $category_column_index . '2')->applyFromArray($style['column_heading']);

        $sheet_dimension = $active_sheet->getHighestRowAndColumn();


        //Freeze the fixed panes
        $active_sheet->freezePane('A3');

        //Unprotect The rate column
        $active_sheet->getStyle('A:' . $category_column_index)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

        $writer = new Xlsx($spreadsheet);

        $filename = 'Material Items Registration Template';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');

    }

    public function download_material_registration_excel_template1(){
        //my trial codes
        // $this->load->library('excel');
        // $this->excel->setActiveSheetIndex(0);

        // $active_sheet = $this->excel->getActiveSheet();
        // $active_sheet->setTitle('Material Registration by me');


        $this->load->model('material_item_category');
        $categories = $this->material_item_category->get(0, 0, ['tree_level' => '1']);
        //  var_dump($categories);
        //  exit();
        $new_category = array();

        foreach ($categories as $val){

            $new_category['categories'][] =['category_name'=>$val->category_name,'description'=>$val->description];

        }
        $categories = $new_category['categories'];


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
       	$sheet->setCellValue('A1', 'category_name');
        $sheet->setCellValue('B1', 'description');
        $sheet->setCellValue('C1', 'number_of_items');

        $rows = 2;
        foreach ($categories as $val){
            // var_dump($val->category_name);
            // exit();
            $sheet->setCellValue('A' . $rows, $val['category_name']);
            $sheet->setCellValue('B' . $rows, $val['description']);
            $sheet->setCellValue('C' . $rows, 1);
            $rows++;
        }
        $writer = new Xlsx($spreadsheet);
		//$writer->save("upload/".$fileName);
		//header("Content-Type: application/vnd.ms-excel");
        //redirect(base_url()."/upload/".$fileName);
        $filename = 'accounts';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        // echo inspect_object($categories);


        // $this->load->model(['material_item_category', 'measurement_unit']);
        // $uom_dropdown = $this->measurement_unit->excel_dropdown_list();
        // $categories = $this->material_item_category->get(0, 0, ['tree_level' => '1']);

        // echo inspect_object($categories);

        // exit();

        // $spreadsheet = new Spreadsheet();
        // $sheet = $spreadsheet->getActiveSheet();
        // $sheet->setCellValue('A1', 'Hello World !');
        // $writer = new Xlsx($spreadsheet);
        // $filename = 'name-of-the-generated-file';

        // header('Content-Type: application/vnd.ms-exc$active_sheet = $this->excel->getActiveSheet();$active_sheet = $this->excel->getActiveSheet();$active_sheet = $this->excel->getActiveSheet();$active_sheet = $this->excel->getActiveSheet();el');
        // header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
        // header('Cache-Control: max-age=0');
        // $writer->save('php://output');
    }

    public function download_material_registration_excel_template2()
    {

        //load our new PHPExcel library
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);

        $active_sheet = $this->excel->getActiveSheet();
        $active_sheet->setTitle('Material Registration');

        //Protect Sheet
        $active_sheet->getProtection()->setPassword('material@registration12');
        $active_sheet->getProtection()->setSheet(true);

        $this->load->model(['material_item_category', 'measurement_unit']);
        $uom_dropdown = $this->measurement_unit->excel_dropdown_list();
        $categories = $this->material_item_category->get(0, 0, ['tree_level' => '1']);
        $hex_color = '9bc4c6';

        $active_sheet->getStyle('A1:D202')->applyFromArray([
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => $hex_color],
            ]
        ]);

        $style['column_title'] = [
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'bfbfbf'],
            ]
        ];

        $active_sheet->getStyle('A2:D2')->applyFromArray($style['column_title']);


        $active_sheet->setCellValue('A1', 'UNCATEGORIZED');
        $active_sheet->setCellValue('A2', 'Material Item');
        $active_sheet->setCellValue('B2', 'Measurement Unit');
        $active_sheet->setCellValue('C2', 'Part Number');
        $active_sheet->setCellValue('D2', 'Description');
        //Unprotect Editable Cells
        $active_sheet->getStyle('A3:D202')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

        //Add UOM dropdown options for uncategorized material
        for ($i = 3; $i <= 202; $i++) {

            $objValidation = $active_sheet->getCell('B' . $i)->getDataValidation();
            $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
            $objValidation->setAllowBlank(false);
            $objValidation->setShowInputMessage(true);
            $objValidation->setShowErrorMessage(true);
            $objValidation->setShowDropDown(true);
            $objValidation->setErrorTitle('Input error');
            $objValidation->setError('Value is not in list.');
            $objValidation->setPromptTitle('Pick from list');
            $objValidation->setPrompt('Please pick a value from the drop-down list.');
            $objValidation->setFormula1('"' . $uom_dropdown . '"');
        }


        $category_column_index = 'E';
        $category_ids   = $this->serialize_material_item_category_ids($categories);

        $category_ids = new RecursiveIteratorIterator(new RecursiveArrayIterator($category_ids));

        foreach ($category_ids as $category_id) {
            $category_column_index++;
            $category_start_column = $category_column_index;
            $hex_color = '9bc4c6';
            $category = new Material_item_category();
            $category->load($category_id);
            $hex_color = dechex(hexdec($hex_color) + (96 * $category->tree_level / 2));
            $font_size = 18 - ($category->tree_level);

            $active_sheet->setCellValue($category_column_index . '1', $category_id);
            $active_sheet->setCellValue($category_column_index . '2', 'Item Name');
            $category_column_index++;
            $active_sheet->setCellValue($category_column_index . '1', $category->category_name);
            $active_sheet->setCellValue($category_column_index . '2', 'Measurement Unit');

            //Add UOM dropdown options
            for ($i = 3; $i <= 202; $i++) {

                $objValidation = $active_sheet->getCell($category_column_index . $i)->getDataValidation();
                $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('"' . $uom_dropdown . '"');
            }


            $category_column_index++;
            $active_sheet->setCellValue($category_column_index . '1', 'LEVEL ' . $category->tree_level);
            $active_sheet->setCellValue($category_column_index . '2', 'Part Number');
            $category_column_index++;
            $active_sheet->setCellValue($category_column_index . '2', 'Description');

            $category_end_column = $category_column_index;
            $active_sheet->getStyle($category_start_column . '1:' . $category_end_column . '202')->applyFromArray([
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => $hex_color],
                ]
            ]);
            $active_sheet->getStyle($category_start_column . '1:' . $category_end_column . '1')->applyFromArray([
                'font' => [
                    'size' => $font_size
                ]
            ]);

            $active_sheet->getStyle($category_start_column . '2:' . $category_end_column . '2')->applyFromArray($style['column_title']);
            $category_column_index++;

            //Unprotect Editable Cells
            $active_sheet->getStyle($category_start_column . '3:' . $category_end_column . '202')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        }

        for ($col_index = 'A'; $col_index !== $category_column_index; $col_index++) {
            $active_sheet->getColumnDimension($col_index)->setAutoSize(true);
        }

        $active_sheet->getStyle('A1:' . $category_column_index . '202')->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        ]);

        $style['column_heading'] = [
            'font' => [
                'bold'  => true,
                'italic' => true
            ]
        ];

        //Start Initial column headers
        $active_sheet->getStyle('A1:' . $category_column_index . '2')->applyFromArray($style['column_heading']);

        $sheet_dimension = $active_sheet->getHighestRowAndColumn();

        //Freeze the fixed panes
        $active_sheet->freezePane('A3');

        //Unprotect The rate column
        $active_sheet->getStyle('A:' . $category_column_index)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);


        $filename = 'Material Items Registration Template';
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0'); //no cache
        ob_end_clean();
        $objWriter->save('php://output');
    }
    public function upload_material_registration_excel(){
        
        // print_r($_FILES['file']['name']);
        // exit();

       $upload_file = $_FILES['file']['name'];

      
        $extension = pathinfo($upload_file)['extension'];

        if($extension == 'Csv')
        {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        }
        elseif($extension=='Xls')
        {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        }
        else{
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }

        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);

        // print_r($spreadsheet);
        // exit();

        $active_sheet = $spreadsheet->getActiveSheet();

        //echo json_encode($active_sheet);
        //exit();
       // $hash = $active_sheet->getProtection()->getPassword(); // returns a hash
        //$valid = ($hash === \PhpOffice\PhpSpreadsheet\Shared\PasswordHasher::hashPassword('material@registration12'));
       //if($valid){
            $sheet_dimension = $active_sheet->getHighestRowAndColumn();

            $this->load->model(['measurement_unit', 'material_item']);
            $measurement_units = $this->measurement_unit->get();
            foreach ($measurement_units as $measurement_unit) {
                $uom_ids[$measurement_unit->symbol] = $measurement_unit->{$measurement_unit::DB_TABLE_PK};
            }

            //Uncategorized Material Items

            for ($row_index = 3; $row_index <= $sheet_dimension['row']; $row_index++) {
                $item_name = trim($active_sheet->getCell("A" . $row_index)->getFormattedValue());
                //echo $item_name;
                //exit();
                $symbol = $active_sheet->getCell("B" . $row_index)->getFormattedValue();
                $uom_id = isset($uom_ids[$symbol]) ? $uom_ids[$symbol] : null;
                if ($item_name != '' /*&& !is_null($uom_id)*/ ) {
                    $material_item = new Material_item();
                    $material_item->item_name = $item_name;
                    $material_item->unit_id = $uom_id;
                    $material_item->category_id = null;
                    $material_item->part_number = $active_sheet->getCell("C" . $row_index)->getFormattedValue();
                    $material_item->description = trim($active_sheet->getCell("D" . $row_index)->getFormattedValue());
                    $material_item->save();
                }
            }

            //Categorized material_items

            $MAX_COL_INDEX = Coordinate::columnIndexFromString($sheet_dimension['column']);

            //echo json_encode($MAX_COL_INDEX);
            //exit();
            for ($index = Coordinate::columnIndexFromString('F'); $index < $MAX_COL_INDEX; $index = $index + 5) {
                $col = Coordinate::stringFromColumnIndex($index);
                $uom_col = Coordinate::stringFromColumnIndex($index + 1);
                $part_number_col = Coordinate::stringFromColumnIndex($index + 2);
                $description_col = Coordinate::stringFromColumnIndex($index + 3);
                $category_id = $active_sheet->getCell($col . '1')->getFormattedValue();
              

                for ($row_index = 3; $row_index <= $sheet_dimension['row']; $row_index++) {
                    $item_name = trim($active_sheet->getCell($col . $row_index)->getFormattedValue());
                    $symbol = $active_sheet->getCell($uom_col . $row_index)->getFormattedValue();
                    $uom_id = isset($uom_ids[$symbol]) ? $uom_ids[$symbol] : null;
                    if ($item_name != '' && !is_null($uom_id)) {
                        $material_item = new Material_item();
                        $material_item->item_name = $item_name;
                        $material_item->unit_id = $uom_id;
                        $material_item->category_id = $category_id;
                        $material_item->part_number = $active_sheet->getCell($part_number_col . $row_index)->getFormattedValue();
                        $material_item->description = trim($active_sheet->getCell($description_col . $row_index)->getFormattedValue());
                        $material_item->save();
                    }
                }
            }
        //}  

    }

    public function upload_material_registration_excel1()
    {
        $this->load->library('excel');
        $file = $_FILES['file']['tmp_name'];
        //read file from path
        $objPHPExcel = PHPExcel_IOFactory::load($file);

        $active_sheet = $objPHPExcel->getActiveSheet();
        $hash = $active_sheet->getProtection()->getPassword(); // returns a hash
        $valid = ($hash === PHPExcel_Shared_PasswordHasher::hashPassword('material@registration12'));
        if ($valid) {
            $sheet_dimension = $active_sheet->getHighestRowAndColumn();

            $this->load->model(['measurement_unit', 'material_item']);
            $measurement_units = $this->measurement_unit->get();
            foreach ($measurement_units as $measurement_unit) {
                $uom_ids[$measurement_unit->symbol] = $measurement_unit->{$measurement_unit::DB_TABLE_PK};
            }

            //Uncategorized Material Items

            for ($row_index = 3; $row_index <= $sheet_dimension['row']; $row_index++) {
                $item_name = trim($active_sheet->getCell("A" . $row_index)->getFormattedValue());
                $symbol = $active_sheet->getCell("B" . $row_index)->getFormattedValue();
                $uom_id = isset($uom_ids[$symbol]) ? $uom_ids[$symbol] : null;
                if ($item_name != '' && !is_null($uom_id)) {
                    $material_item = new Material_item();
                    $material_item->item_name = $item_name;
                    $material_item->unit_id = $uom_id;
                    $material_item->category_id = null;
                    $material_item->part_number = $active_sheet->getCell("C" . $row_index)->getFormattedValue();
                    $material_item->description = trim($active_sheet->getCell("D" . $row_index)->getFormattedValue());
                    $material_item->save();
                }
            }

            //Categorized material_items

            $MAX_COL_INDEX = PHPExcel_Cell::columnIndexFromString($sheet_dimension['column']);

            for ($index = PHPExcel_Cell::columnIndexFromString('E'); $index < $MAX_COL_INDEX; $index = $index + 5) {
                $col = PHPExcel_Cell::stringFromColumnIndex($index);
                $uom_col = PHPExcel_Cell::stringFromColumnIndex($index + 1);
                $part_number_col = PHPExcel_Cell::stringFromColumnIndex($index + 2);
                $description_col = PHPExcel_Cell::stringFromColumnIndex($index + 3);
                $category_id = $active_sheet->getCell($col . '1')->getFormattedValue();

                for ($row_index = 3; $row_index <= $sheet_dimension['row']; $row_index++) {
                    $item_name = trim($active_sheet->getCell($col . $row_index)->getFormattedValue());
                    $symbol = $active_sheet->getCell($uom_col . $row_index)->getFormattedValue();
                    $uom_id = isset($uom_ids[$symbol]) ? $uom_ids[$symbol] : null;
                    if ($item_name != '' && !is_null($uom_id)) {
                        $material_item = new Material_item();
                        $material_item->item_name = $item_name;
                        $material_item->unit_id = $uom_id;
                        $material_item->category_id = $category_id;
                        $material_item->part_number = $active_sheet->getCell($part_number_col . $row_index)->getFormattedValue();
                        $material_item->description = trim($active_sheet->getCell($description_col . $row_index)->getFormattedValue());
                        $material_item->save();
                    }
                }
            }
        }
    }

    public function load_material_item_movement_material_options()
    {
        $this->load->model('material_item');
        $material_items = $this->material_item->location_material_items($this->input->post('location_id'));
        $options = ['' => '&nbsp;'];
        foreach ($material_items as $material_item) {
            $options[$material_item->{$material_item::DB_TABLE_PK}] = $material_item->item_name;
        }
        echo stringfy_dropdown_options($options);
    }

    public function location_reports()
    {
        ini_set('memory_limit', -1);
        set_time_limit(100000);
        $report_type = $this->input->post('report_type');
        $sub_location_id = $this->input->post('sub_location_id');
        $sub_location_id = $sub_location_id != '' ? $sub_location_id : null;
        $location_id = $this->input->post('location_id');
        $project_id = $this->input->post('project_id');
        $project_id = $project_id != '' ? $project_id : null;
        $category_id = $this->input->post('category_id');
        $category_id = $category_id != '' ? $category_id : null;
        $asset_group_id = $this->input->post('asset_group_id');
        $asset_group_id = $asset_group_id != '' ? $asset_group_id : null;
        $data['from'] = $from = $this->input->post('from');
        $data['to'] = $to = $this->input->post('to');
        $data['print'] = $print = $this->input->post('print');
        $this->load->model('project');
        if ($project_id != 'all' && $project_id != 'all_projectwise' && !is_null($project_id)) {
            $project = new Project();
            $project->load($project_id);
        }
        if ($data['print']) {
            if ($project_id != 'all' && $project_id != 'all_projectwise' && !is_null($project_id)) {
                $data['project_name'] = $project->project_name;
            } else {
                $data['project_name'] = is_null($project_id) ? 'UN-ASSIGNED' : 'ALL';
            }
        }

        $projectwise = false;
        if ($project_id == 'all_projectwise') {
            $project_id = 'all';
            $projectwise = true;
        }
        $data['projectwise'] = $projectwise;

        $sub_locationwise = false;
        if ($sub_location_id == 'all_sub_locationwise') {
            $sub_locationwise = true;
        }
        $data['sub_locationwise'] = $sub_locationwise;

        $this->load->model('material_item');
        if ($report_type == 'location_material_balance' || $report_type == 'location_material_movement') {
            $with_balance = $report_type == 'location_material_balance' ? true : false;
            $material_items = $this->material_item->location_material_items($location_id, $sub_location_id != 'all_sub_locationwise' ? $sub_location_id : null, $with_balance, $project_id, $category_id);
        }

        $this->load->model('inventory_location');
        $location = new Inventory_location();
        $location->load($location_id);
        $location_name = $location->location_name;
        $sub_location_ids = $location->sub_location_ids_query();
        $transfer_type = 'external';

        if (!is_null($sub_location_id)) {
            if ($sub_location_id != 'all_sub_locationwise') {
                $this->load->model('sub_location');
                $sub_location = new Sub_location();
                $sub_location->load($sub_location_id);
                $location_name .= ' / ' . $sub_location->sub_location_name;
                $sub_location_ids = $sub_location_id;
                $transfer_type = 'all';
            }
        }

        $data['location_name'] = $location_name;

        $opening_balance_date = new DateTime($from);
        $opening_balance_date->modify(' - 1 day');
        $opening_balance_date = $opening_balance_date->format('Y-m-d H:i');

        $table_items  = $sub_location_balances = [];
        if ($report_type == 'location_material_balance') {
            $data['allow_rates'] = check_permission('Administrative Actions');
            $table_items = [];
            inspect_object($sub_location_ids . ' - ' . $project_id . ' - ' . $to . ' - ' . $transfer_type);
            foreach ($material_items as $material_item) {
                $balance = $material_item->sub_location_balance($sub_location_ids, $project_id, $to, $transfer_type);
                if ($balance > 0 || $balance < 0) {
                    $sub_locations_array = [];
                    if ($data['allow_rates'] || $sub_location_id == 'all_sub_locationwise') {
                        if (is_null($sub_location_id) || $sub_location_id == 'all_sub_locationwise') {
                            $sub_locations = $location->sub_locations();
                            $stock_value = $stock_quantity = 0;
                            foreach ($sub_locations as $sub_location) {
                                $stock_quantity += $quantity = $material_item->sub_location_balance($sub_location->{$sub_location::DB_TABLE_PK}, $project_id);
                                $stock_average_price = $material_item->sub_location_average_price($sub_location->{$sub_location::DB_TABLE_PK}, $project_id, $to);
                                $stock_value += $quantity * $stock_average_price;
                                if ($sub_location_id == 'all_sub_locationwise' && $quantity != 0) {
                                    $sub_locations_array[] = $sub_location;
                                    $sub_location_balances[$material_item->{$material_item::DB_TABLE_PK}][$sub_location->sub_location_name] = [$sub_location->sub_location_name, $quantity];
                                }
                            }
                            $rate = $stock_quantity > 0 ? $stock_value / $stock_quantity : 0;
                        } else {
                            $rate = $material_item->sub_location_average_price($sub_location_id, $project_id);
                        }
                    } else {
                        $rate = 0;
                    }

                    $table_items[] = [
                        'item_id' => $material_item->{$material_item::DB_TABLE_PK},
                        'item_name' => $material_item->item_name,
                        'sub_location_balances' => $sub_location_balances,
                        'sub_locations_array' => $sub_locations_array,
                        'unit' => $material_item->unit()->symbol,
                        'balance' => $balance,
                        'rate' => $rate,
                    ];
                }
            }

            $data['sub_location_ids'] = $sub_location_ids;
            $data['transfer_type'] = $transfer_type;
            $data['table_items'] = array_sort($table_items, 'item_name', SORT_ASC);

            if ($print) {

                $html = $this->load->view('inventory/documents/location_material_balance_sheet', $data, true);

                //this the PDF filename that user will get to download

                //load mPDF library
                $this->load->library('m_pdf');
                //actually, you can pass mPDF parameter on this load() function
                $pdf = $this->m_pdf->load();
                $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
                $pdf->SetFooter($footercontents);
                $pdf->WriteHTML($html);
                //$this->mpdf->Output($file_name, 'D'); // download force

                $pdf->Output('material movement.pdf', 'I'); // view in the explorer

            } else {
                $this->load->view('inventory/reports/material_balance_report_table', $data);
            }
        } else if ($report_type == 'location_material_movement') {
            foreach ($material_items as $material_item) {
                $table_items[] = [
                    'received' => $material_item->sub_location_received_quantity($sub_location_ids, $project_id, $from, $to),
                    'item_name' => $material_item->item_name,
                    'unit' => $material_item->unit()->symbol,
                    'opening_balance' => $material_item->sub_location_balance($sub_location_ids, $project_id, $opening_balance_date, $transfer_type),
                    'transferred_out' => $material_item->sub_location_transferred_out_quantity($sub_location_ids, $project_id, $from, $to, $transfer_type),
                    'assigned_out' => $project_id != 'all' ? $material_item->sub_location_assigned_out_quantity($sub_location_ids, $project_id, $from, $to) : 0,
                    'disposed' => $material_item->sub_location_disposed_quantity($sub_location_ids, $project_id, $from, $to),
                    'sold' => $material_item->sub_location_sold_quantity($sub_location_ids, $project_id, $from, $to),
                    'used' => $material_item->sub_location_used_quantity($sub_location_ids, $project_id, $from, $to),
                    'balance' => $material_item->sub_location_balance($sub_location_ids, $project_id, $to, $transfer_type)
                ];
            }

            $data['table_items'] = $table_items;

            if ($print) {

                $html = $this->load->view('inventory/documents/location_material_movement_sheet', $data, true);

                //this the PDF filename that user will get to download

                //load mPDF library
                $this->load->library('m_pdf');
                //actually, you can pass mPDF parameter on this load() function
                $pdf = $this->m_pdf->load();
                $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
                $pdf->SetFooter($footercontents);
                $pdf->WriteHTML($html);
                //$this->mpdf->Output($file_name, 'D'); // download force

                $pdf->Output('material movement.pdf', 'I'); // view in the explorer

            } else {
                $this->load->view('inventory/reports/material_movement_report_table', $data);
            }
        } else if ($report_type == 'location_material_item_movement') {
            $material_item = new Material_item();
            if ($material_item->load($this->input->post('material_id'))) {
                $data['material_item'] = $material_item;
                $data['opening_balance'] = $material_item->sub_location_balance($sub_location_ids, $project_id, $opening_balance_date, $transfer_type);
                $data['transactions'] = $material_item->sub_location_item_movement_transactions($sub_location_ids, $from, $to, $project_id);
                if ($print) {

                    $html = $this->load->view('inventory/documents/location_material_item_movement_sheet', $data, true);

                    //this the PDF filename that user will get to download

                    //load mPDF library
                    $this->load->library('m_pdf');
                    //actually, you can pass mPDF parameter on this load() function
                    $pdf = $this->m_pdf->load();
                    $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
                    $pdf->SetFooter($footercontents);
                    $pdf->WriteHTML($html);
                    //$this->mpdf->Output($file_name, 'D'); // download force

                    $pdf->Output('Item movement.pdf', 'I'); // view in the explorer

                } else {
                    $this->load->view('inventory/reports/material_item_movement_report_table', $data);
                }
            } else {
                echo '<script>window.close()</script>';
            }
        } else if ($report_type == 'location_material_item_availability') {
            $material_item = new Material_item();
            if ($material_item->load($this->input->post('material_id'))) {
                $quantities = [];

                if (!is_null($sub_location_id)) {
                    $sub_locations = [$sub_location];
                } else {
                    $sub_locations = $location->sub_locations();
                }

                foreach ($sub_locations as $sub_location) {
                    $column_totals[$sub_location->{$sub_location::DB_TABLE_PK}] = 0;
                }

                $data['project_selected'] = false;
                if ($project_id != 'all' && !is_null($project_id)) {
                    $data['project_selected'] = true;
                    $row_totals[$project_id] = 0;
                    $projects = [$project];
                } else {
                    $projects = is_null($project_id) ? [] : $this->project->get();
                    $row_totals['unassigned'] = 0;
                    foreach ($projects as $proj) {
                        $row_totals[$proj->{$proj::DB_TABLE_PK}] = 0;
                    }

                    foreach ($sub_locations as $sub) {
                        $quantities['unassigned'][$sub->{$sub::DB_TABLE_PK}] = $balance = $material_item->sub_location_balance($sub->{$sub::DB_TABLE_PK}, null, $to);
                        $row_totals['unassigned'] += $balance;
                        $column_totals[$sub->{$sub::DB_TABLE_PK}] += $balance;
                    }
                }

                foreach ($projects as $proj) {
                    foreach ($sub_locations as $sub) {
                        $quantities[$proj->{$proj::DB_TABLE_PK}][$sub->{$sub::DB_TABLE_PK}] = $balance = $material_item->sub_location_balance($sub->{$sub::DB_TABLE_PK}, $proj->{$proj::DB_TABLE_PK}, $to);
                        $row_totals[$proj->{$proj::DB_TABLE_PK}] += $balance;
                        $column_totals[$sub->{$sub::DB_TABLE_PK}] += $balance;
                    }
                }

                $data['quantities'] = $quantities;
                $data['sub_locations'] = $sub_locations;
                $data['projects'] = $projects;
                $data['row_totals'] = $row_totals;
                $data['column_totals'] = $column_totals;

                if ($print) {
                    $data['material_item'] = $material_item;

                    $html = $this->load->view('inventory/documents/location_material_item_availability_sheet', $data, true);

                    //this the PDF filename that user will get to download

                    //load mPDF library
                    $this->load->library('m_pdf');
                    //actually, you can pass mPDF parameter on this load() function
                    $pdf = $this->m_pdf->load();
                    $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
                    $pdf->SetFooter($footercontents);
                    $pdf->WriteHTML($html);
                    //$this->mpdf->Output($file_name, 'D'); // download force

                    $pdf->Output('Item Availability.pdf', 'I'); // view in the explorer

                } else {
                    $this->load->view('inventory/reports/material_item_availability_table', $data);
                }
            }
        } else if ($report_type == 'location_material_disposal') {
            $this->load->model('material_disposal');
            $where = 'WHERE location_id = ' . $location_id . '';
            if ($project_id != 'all' && $project_id != 'all_projectwise' && !is_null($project_id)) {
                $where .= ' AND material_disposals.project_id = ' . $project_id . '';
            } else if (is_null($project_id)) {
                $where .= ' AND material_disposals.project_id IS NULL';
            }
            if (!is_null($sub_location_id) && $sub_location_id != 'all_sub_locationwise') {
                $where .= ' AND sub_location_id = ' . $sub_location_id . '';
            }

            $sql = 'SELECT material_disposals.* FROM material_disposals
                    LEFT JOIN material_disposal_items ON material_disposals.id = material_disposal_items.disposal_id ' . $where . '
                    GROUP BY material_disposals.id ORDER BY id DESC';

            $query = $this->db->query($sql);
            $results = $query->result();
            $table_items = [];
            foreach ($results as $row) {
                $material_disposal = new Material_disposal();
                $material_disposal->load($row->id);
                $table_items[] = [
                    'disposal_id' => $row->id,
                    'disposal_date' => $row->disposal_date,
                    'project' => isset($material_disposal->project()->project_id) ? $material_disposal->project()->project_name : "UNASSIGNED",
                    'amount' => $material_disposal->amount(),
                    'disposed_by' => $material_disposal->employee()->full_name(),
                    'datetime' => $row->created_at
                ];
            }

            $data['table_items'] = $table_items;

            if ($print) {

                $html = $this->load->view('inventory/documents/material_disposal_sheet', $data, true);

                //this the PDF filename that user will get to download

                //load mPDF library
                $this->load->library('m_pdf');
                //actually, you can pass mPDF parameter on this load() function
                $pdf = $this->m_pdf->load();
                $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
                $pdf->SetFooter($footercontents);
                $pdf->WriteHTML($html);
                //$this->mpdf->Output($file_name, 'D'); // download force

                $pdf->Output('material movement.pdf', 'I'); // view in the explorer

            } else {
                $this->load->view('inventory/reports/material_disposal_report_table', $data);
            }
        } else if ($report_type == 'location_asset_stock') {
            $this->load->model('asset_item');
            $asset_items = $this->asset_item->sub_location_assets($sub_location_ids, $project_id, $asset_group_id);
            $table_items = [];
            foreach ($asset_items as $asset_item) {
                $projects_with_this_item = $asset_item->projects_with_this_item();
                $table_items[] = [
                    'asset_name' => $asset_item->asset_name,
                    'asset_item' => $asset_item,
                    'projects' => $projects_with_this_item,
                    'asset_group_id' => $asset_group_id,
                    'sub_location_ids' => $sub_location_ids,
                    'balance' => $asset_item->sub_location_available_stock($sub_location_ids, $project_id, true, null, $asset_group_id),
                    'status' => $asset_item->sub_location_available_stock($sub_location_ids, $project_id, true, 'active', $asset_group_id) . ' Active,' . '  ' . $asset_item->sub_location_available_stock($sub_location_ids, $project_id, true, 'inactive', $asset_group_id) . ' Inactive'
                ];
            }

            $data['table_items'] = $table_items;

            if ($print) {

                $html = $this->load->view('inventory/documents/location_asset_stock_sheet', $data, true);

                //this the PDF filename that user will get to download

                //load mPDF library
                $this->load->library('m_pdf');
                //actually, you can pass mPDF parameter on this load() function
                $pdf = $this->m_pdf->load();
                $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
                $pdf->SetFooter($footercontents);
                $pdf->WriteHTML($html);
                //$this->mpdf->Output($file_name, 'D'); // download force

                $pdf->Output('material movement.pdf', 'I'); // view in the explorer

            } else {
                $this->load->view('inventory/reports/asset_stock_table', $data);
            }
        } else if ($report_type == 'location_asset_movement') {
            $this->load->model(array('asset_item', 'asset'));
            $asset_items = $this->asset_item->sub_location_asset_items($sub_location_ids, $project_id, $asset_group_id);
            foreach ($asset_items as $asset_item) {
                $table_items[] = [
                    'name' => $asset_item->asset_name,
                    'opening_balance' => $asset_item->sub_location_opening_quantity($sub_location_ids, $project_id, $from, $to),
                    'received' => $asset_item->sub_location_received_quantity($sub_location_ids, $project_id, $from, $to),
                    'assigned_out' => $asset_item->sub_location_assigned_out_quantity($sub_location_ids, $project_id, $from, $to),
                    'transferred_out' => $asset_item->sub_location_transferred_out_quantity($sub_location_ids, $project_id, $from, $to),
                    'sold' => $asset_item->sub_location_sold_quantity($sub_location_ids, $project_id, $from, $to),
                    'disposed' => $asset_item->sub_location_disposed_quantity($sub_location_ids, $project_id, $from, $to),
                    // 'used' => $asset_item->sub_location_used_quantity($sub_location_ids, $project_id, $from, $to),
                    'balance' => $asset_item->sub_location_available_stock($sub_location_ids, $project_id, true),
                    'average_price' => $asset_item->sub_location_available_stock($sub_location_ids, $project_id, true)
                ];
            }

            $data['table_items'] = $table_items;

            if ($print) {
                $html = $this->load->view('inventory/documents/asset_movement_sheet', $data, true);
                $this->load->library('m_pdf');
                $pdf = $this->m_pdf->load();
                $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
                $pdf->SetFooter($footercontents);
                $pdf->WriteHTML($html);
                $pdf->Output('Asset movement.pdf', 'I');
            } else {
                $this->load->view('inventory/reports/asset_movement_table', $data);
            }
        } else {
            echo '<script>window.close()</script>';
        }
    }

    public function load_material_last_approved_price()
    {
        $this->load->model('material_item');
        $currency_id = $this->input->post('currency_id');
        $material_item_id = $this->input->post('material_item_id');
        echo $material_item_id != '' ? $this->material_item->last_approved_price($currency_id, $material_item_id) : 0;
    }

    public function location_material_disposals($location_id = 0)
    {
        $this->load->model('Material_disposal');
        $posted_params = dataTable_post_params();
        echo $this->Material_disposal->location_material_disposal_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'], $location_id);
    }

    public function preview_transfer_order($requisition_approval_id = 0, $destination_id = 0)
    {
        $this->load->model(['external_material_transfer', 'requisition_approval', 'inventory_location']);
        $requisition_approval = new Requisition_approval();
        $Inventory_location = new Inventory_location();

        if ($requisition_approval->load($requisition_approval_id) && $Inventory_location->load($destination_id)) {

            $data['location'] = $Inventory_location;
            $data['requisition_approval'] = $requisition_approval;

            $data['transfer_order_material_items'] = $this->external_material_transfer->transfer_order_material_items($requisition_approval_id, $destination_id);
            $data['transfer_order_asset_items'] = $this->external_material_transfer->transfer_order_asset_items($requisition_approval_id, $destination_id);
            $html = $this->load->view('inventory/material/transfer_orders/transfer_order_preview', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
            $pdf->setFooter($footercontents);
            //generate the PDF!
            $pdf->WriteHTML($html);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('Material_transfer_order_Items_List' . date('Y-m-d') . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function save_material_disposal()
    {
        $quantities = $this->input->post('quantities');

        if (!empty($quantities)) {
            $this->load->model('material_disposal');
            $material_disposal = new Material_disposal();
            $edit = $material_disposal->load($this->input->post('disposal_id'));
            $material_disposal->location_id = $this->input->post('location_id');
            $material_disposal->disposal_date = $this->input->post('disposal_date');
            $material_disposal->project_id = $this->input->post('project_id');
            $material_disposal->project_id = $material_disposal->project_id != '' ? $material_disposal->project_id : null;
            $material_disposal->created_by = $this->session->userdata('employee_id');

            if ($material_disposal->save()) {

                $this->load->model(['material_disposal_item', 'stock_disposal_asset_item', 'asset']);
                foreach ($quantities as $index => $quantity) {
                    if ($this->input->post('item_types')[$index] == 'material') {
                        $disposal_item = new Material_disposal_item();
                        $disposal_item->material_item_id = $this->input->post('material_item_ids')[$index];
                        $disposal_item->quantity = $quantity;
                        $disposal_item->project_id = $material_disposal->project_id;
                        $disposal_item->project_id = $disposal_item->project_id != '0' ? $disposal_item->project_id : null;
                        $disposal_item->rate = $this->input->post('rates')[$index];
                        $disposal_item->sub_location_id = $this->input->post('source_sub_location_ids')[$index];
                    } else {
                        $disposal_item = new Stock_disposal_asset_item();
                        $asset = new Asset();
                        $asset->load($this->input->post('asset_ids')[$index]);
                        $history = $asset->latest_sub_location_history();
                        $disposal_item->asset_sub_location_history_id = $history->{$history::DB_TABLE_PK};
                    }

                    $disposal_item->disposal_id = $material_disposal->{$material_disposal::DB_TABLE_PK};
                    $disposal_item->remarks = $this->input->post('remarks')[$index];
                    $disposal_item->save();
                }
            }
        }
    }

    public function preview_material_disposal($disposal_id = 0)
    {
        $this->load->model('Material_disposal');
        $material_disposal = new Material_disposal();

        if ($material_disposal->load($disposal_id)) {

            $data['material_disposal'] = $material_disposal;


            $html = $this->load->view('inventory/material/disposals/preview_material_disposal', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
            $pdf->setFooter($footercontents);
            //generate the PDF!
            $pdf->WriteHTML($html);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('Disposed _material_Items_List' . date('Y-m-d') . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function material_cost_center_assignment($location_id = 0)
    {
        $this->load->model('material_cost_center_assignment');
        $posted_params = dataTable_post_params();
        echo $this->material_cost_center_assignment->material_cost_center_assignments($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'], $location_id);
    }

    public function save_material_cost_center_assignment()
    {
        $quantities = $this->input->post('quantities');
        if (!empty($quantities)) {
            $this->load->model('Material_cost_center_assignment');
            $material_cost_center_assignment = new Material_cost_center_assignment();
            $edit = $material_cost_center_assignment->load($this->input->post('material_cost_center_assignment_id'));
            $material_cost_center_assignment->assignment_date = $this->input->post('assignment_date');
            $material_cost_center_assignment->location_id = $this->input->post('location_id');
            $material_cost_center_assignment->source_project_id = $this->input->post('source_project_id');
            $material_cost_center_assignment->source_project_id = $material_cost_center_assignment->source_project_id != '' ? $material_cost_center_assignment->source_project_id : null;
            $material_cost_center_assignment->destination_project_id = $this->input->post('destination_project_id');
            $material_cost_center_assignment->destination_project_id = $material_cost_center_assignment->destination_project_id != '' ? $material_cost_center_assignment->destination_project_id : null;
            $material_cost_center_assignment->created_by = $this->session->userdata('employee_id');

            if ($material_cost_center_assignment->save()) {
                $this->load->model(['Material_stock', 'Material_cost_center_assignment_item']);

                foreach ($quantities as $index => $quantity) {
                    $material_stock = new Material_stock();
                    $material_stock->item_id = $this->input->post('item_ids')[$index];
                    $material_stock->quantity = $quantity;
                    $material_stock->date_received = $material_cost_center_assignment->assignment_date;
                    $material_stock->project_id = $material_cost_center_assignment->destination_project_id;
                    $material_stock->sub_location_id = $this->input->post('sub_location_ids')[$index];
                    $material_stock->price = $this->input->post('prices')[$index];
                    $material_stock->receiver_id = $this->session->userdata('employee_id');
                    $material_stock->description = $this->input->post('descriptions')[$index];
                    if ($material_stock->save()) {
                        $cost_center_assignment_item  = new Material_cost_center_assignment_item();
                        $cost_center_assignment_item->stock_id = $material_stock->{$material_stock::DB_TABLE_PK};
                        $cost_center_assignment_item->material_cost_center_assignment_id = $material_cost_center_assignment->{$material_cost_center_assignment::DB_TABLE_PK};
                        if ($cost_center_assignment_item->save()) {
                            $material_stock->update_average_price();
                        }
                    }
                }
            }
        }
    }

    public function preview_material_cost_center_assignment($cost_center_assignment_id = 0)
    {

        $this->load->model(['Material_cost_center_assignment', 'Material_cost_center_assignment_item']);
        $cost_center_assignment = new Material_cost_center_assignment();
        if ($cost_center_assignment->load($cost_center_assignment_id)) {

            $data['assignment_type'] = "material";
            $data['cost_center_assignment'] = $cost_center_assignment;
            $data['cost_center_assigned_items'] = $cost_center_assignment->cost_center_assignment_items();

            $html = $this->load->view('inventory/cost_center_assignment/preview_cost_center_assignment', $data, true);
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $footercontents = '
						<div>
							<div style="text-align: left; float: left; width: 50%">
								<strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
							</div>
							<div>
								<strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
							</div>
							<div style="text-align: center">
							{PAGENO}
							</div>
						</div>';
            $pdf->setFooter($footercontents);
            //generate the PDF!
            $pdf->WriteHTML($html);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('Cost_center_Assignment' . date('Y-m-d') . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function save_location_sales()
    {
        $this->load->model(['stock_sale', 'stock_sales_asset_item', 'stock_sales_material_item', 'asset']);
        $stock_sale = new Stock_sale();
        $edit = $stock_sale->load($this->input->post('stock_sale_id'));
        $quantities = $this->input->post('quantities');
        if (!empty($quantities)) {
            $stock_sale->sale_date = $this->input->post('sale_date');
            $stock_sale->stakeholder_id = $this->input->post('stakeholder_id');
            $stock_sale->location_id = $this->input->post('location_id');
            $project_id = $this->input->post('project_id');
            $stock_sale->reference = $this->input->post('reference');
            $stock_sale->project_id = $project_id != '' ? $project_id : null;
            $stock_sale->comments = $this->input->post('comments');
            $stock_sale->currency_id = $this->input->post('currency_id');
            $stock_sale->created_by = $this->session->userdata('employee_id');
            if ($stock_sale->save()) {
                $stock_sale->clear_items();
                $item_types = $this->input->post('item_types');
                foreach ($quantities as $index => $quantity) {
                    if ($item_types[$index] == 'material') {
                        /*saving material sales*/
                        $stock_sales_material_item = new Stock_sales_material_item();
                        $stock_sales_material_item->stock_sale_id = $stock_sale->{$stock_sale::DB_TABLE_PK};
                        $stock_sales_material_item->material_item_id = $this->input->post('material_item_ids')[$index]; //Array
                        $stock_sales_material_item->source_sub_location_id = $this->input->post('source_sub_location_ids')[$index]; //Array
                        $stock_sales_material_item->quantity = $quantity;
                        $stock_sales_material_item->price = $this->input->post('prices')[$index]; //Array
                        $stock_sales_material_item->remarks = $this->input->post('remarks')[$index]; //Array
                        $stock_sales_material_item->save();
                    } else {
                        /*saving Asset sales*/
                        $stock_sales_asset_item = new Stock_sales_asset_item();
                        $stock_sales_asset_item->stock_sale_id = $stock_sale->{$stock_sale::DB_TABLE_PK};
                        $asset_id = $this->input->post('asset_ids')[$index];
                        $asset = new  Asset();
                        $asset->load($asset_id);
                        $latest_history = $asset->latest_sub_location_history();
                        $stock_sales_asset_item->asset_sub_location_history_id = $latest_history->{$latest_history::DB_TABLE_PK};
                        $stock_sales_asset_item->price = $this->input->post('prices')[$index]; //Array
                        $stock_sales_asset_item->remarks = $this->input->post('remarks')[$index]; //Array
                        $stock_sales_asset_item->save();
                    }
                }
            }
        }
    }

    public function delete_location_sales()
    {
        $this->load->model('Stock_sale');
        $delete_sales = new Stock_sale();
        $delete_sales->load($this->input->post('sale_id'));
        $delete_sales->delete();
    }

    public function preview_stock_sale($document_type, $sales_number = 0)
    {
        $this->load->model(['stock_sale']);
        $stock_sale = new Stock_sale();

        if ($stock_sale->load($sales_number)) {
            if ($document_type == 'stock_sales_sheet') {
                $document_view = 'stock_sales_sheet';
            } else if ($document_type == 'delivery_form') {
                $document_view = 'stock_sale_delivery_form';
            } else {
                redirect(base_url());
            }
            $data['stock_sale'] = $stock_sale;
            $html = $this->load->view('inventory/documents/' . $document_view, $data, true);
            $this->load->library('m_pdf');
            $pdf = $this->m_pdf->load();
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
            $pdf->setFooter($footercontents);
            //generating the PDF!
            $pdf->WriteHTML($html);
            $pdf->Output('Stock_sales' . date('Y-m-d') . '.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function stock_sales_list($for, $id = 0)
    {
        $this->load->model('Stock_sale');
        $datatable = dataTable_post_params();
        echo $this->Stock_sale->stock_sales_list($for, $id, $datatable['limit'], $datatable['start'], $datatable['keyword'], $datatable['order']);
    }

    public function delete_material_disposal()
    {
        $this->load->model('Material_disposal');
        $material_disposal = new Material_disposal();
        $material_disposal->load($this->input->post('disposal_id'));
        $material_disposal->delete();
    }

    public function save_stock_sales_invoice()
    {
        $this->load->model('Stock_sale_invoice');
        $stock_sale_invoice = new Stock_sale_invoice();
        $stock_sale_invoice->stock_sale_id = $this->input->post('sales_id');
        $stock_sale_invoice->invoice_date = $this->input->post('sales_invoiced_date');
        $stock_sale_invoice->vat_percentage = $this->input->post('vat_percentage');
        $stock_sale_invoice->remarks = $this->input->post('remarks');
        $stock_sale_invoice->created_by = $this->session->userdata('employee_id');
        $stock_sale_invoice->save();
    }

    public function locations_with_particular_item()
    {
        $this->load->model(['material_item', 'inventory_location', 'asset_item', 'project']);
        $item_type =  $this->input->post('item_type');
        $data['locations_options'] = $this->inventory_location->dropdown_options(null, true);
        $data['projects'] = $this->project->get();
        if ($item_type == 'material') {
            $this->domain_name = $this->config->item('domain_name');
            $domain_name = $this->domain_name != '' ? $this->domain_name : null;
            $this->dermstore_url = $this->config->item('dermstore_url');
            $url = $this->dermstore_url != '' ? $this->dermstore_url : null;
            $material_item = new Material_item();
            $material_item_id = $this->input->post('item_id');
            $material_item->load($material_item_id);
            //            if(!is_null($url) && !is_null($domain_name)) {
            //                $this->load->library('MY_Curl');
            //                $curl = new MY_Curl();
            //                $curl->setPost(
            //                    array(
            //                        'material_item_id' => $material_item_id,
            //                        'item_type' => "material",
            //                        'date' => date('Y-m-d')
            //                    )
            //                );
            //
            //                $curl->setUserAgent($this->input->user_agent());
            //                $curl->createCurl($this->dermstore_url . 'inventory/respond_to_curl');
            //                $response = json_decode($curl->__tostring());
            //
            //                $data['curl_response'] = $response;
            //            } else {
            //                $data['curl_response'] = false;
            //            }
            $data['curl_response'] = false;
            $data['material_item'] = $material_item;
            $ret_val['table_view'] = $this->load->view('requisitions/requisitions_list/requisition_material_items_availability_table', $data, true);
        } else {

            $asset_item = new Asset_item();
            $asset_item_id = $this->input->post('item_id');
            $asset_item->load($asset_item_id);
            $this->dermstore_url = $this->config->item('dermstore_url');
            $url = $this->dermstore_url != '' ? $this->dermstore_url : null;
            if (!is_null($url)) {
                $this->load->library('MY_Curl');
                $curl = new MY_Curl();
                $curl->setPost(
                    array(
                        'asset_item_id' => $asset_item_id,
                        'item_type' => "asset",
                        'date' => date('Y-m-d')
                    )
                );

                $curl->setUserAgent($this->input->user_agent());
                $curl->createCurl($this->dermstore_url . 'inventory/respond_to_curl');
                $response = json_decode($curl->__tostring());

                $data['curl_response'] = $response;
            } else {
                $data['curl_response'] = false;
            }
            $data['asset_item'] = $asset_item;
            $ret_val['table_view'] = $this->load->view('requisitions/requisitions_list/requisition_asset_items_availability_table', $data, true);
        }

        echo json_encode($ret_val);
    }

    public function inventory_reports($selected_report = null)
    {
        $this->load->model(['project', 'sub_location', 'inventory_location', 'material_item', 'stakeholder']);

        $report_type = $this->input->post('report_type');
        $print = $this->input->post('print');
        $report_type = $report_type != '' ? $report_type : null;
        $project_id = $this->input->post('project_id');
        $project_id = $project_id != '' ? $project_id : null;
        $project = false;
        if ($project_id != 'all' && !is_null($project_id)) {
            $project = new Project();
            $project->load($project_id);
        }
        $material_id = $this->input->post('material_id');
        $material_id = $material_id != '' ? $material_id : null;
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $from = $from != '' ? $from : null;
        $to = $to != '' ? $to : null;
        $data['from'] = $from;
        $data['to'] = $to;
        if (!is_null($report_type) && !is_null($from) && !is_null($to)) {
            if ($report_type == 'material_item_availability') {
                $material_item = new Material_item();
                if ($material_item->load($material_id)) {
                    $data['material_item'] = $material_item;
                    $data['project_selected'] = false;
                    $data['project'] = null;
                    $data['print'] = $print;
                    if ($project_id != 'all' && !is_null($project_id)) {
                        $data['project_selected'] = true;
                        $data['project'] = $project;
                    }

                    $data['inventory_locations'] =  $this->inventory_location->get(0, 0, [], 'location_name ASC');

                    if ($print) {

                        $html = $this->load->view('inventory/reports/all_locations_material_item_availability_sheet', $data, true);

                        //this the PDF filename that user will get to download

                        //load mPDF library
                        $this->load->library('m_pdf');
                        //actually, you can pass mPDF parameter on this load() function
                        $pdf = $this->m_pdf->load();
                        $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                        </div>
                        <div style="text-align: center">
                        {PAGENO}
                        </div>
                    </div>';
                        $pdf->SetFooter($footercontents);
                        $pdf->WriteHTML($html);
                        //$this->mpdf->Output($file_name, 'D'); // download force

                        $pdf->Output('Item Availability.pdf', 'I'); // view in the explorer

                    } else {
                        echo $this->load->view('inventory/reports/all_locations_material_item_availability_table', $data);
                    }
                }
            } else if ($report_type == 'inventory_sales') {
                $data['print'] = $print;
                $location_id = $this->input->post('location_id');
                $location_id = $location_id != '' ? $location_id : null;
                $sub_location_id = $this->input->post('sub_location_id');
                $sub_location_id = $sub_location_id != '' ? $sub_location_id : null;
                $stakeholder_id = $this->input->post('stakeholder_id');
                $stakeholder_id = $stakeholder_id != '' ? $stakeholder_id : null;
                $location = false;
                if (!is_null($location_id)) {
                    $location = new Inventory_location();
                    $location->load($location_id);
                }

                $sub_location = false;
                if (!is_null($sub_location_id)) {
                    $sub_location = new Sub_location();
                    $sub_location->load($sub_location_id);
                }

                $data['location'] = $location;
                $data['sub_location'] = $sub_location;
                $data['project'] = $project;
                $data['inventory_location_sales'] = $this->inventory_location->inventory_sales($project_id, $location_id, $sub_location_id, $stakeholder_id, $from, $to);
                if ($print) {

                    $html = $this->load->view('inventory/reports/inventory_location_sales_sheet', $data, true);

                    //this the PDF filename that user will get to download

                    //load mPDF library
                    $this->load->library('m_pdf');
                    //actually, you can pass mPDF parameter on this load() function
                    $pdf = $this->m_pdf->load();
                    $footercontents = '
            <div>
                <div style="text-align: left; float: left; width: 50%">
                    <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                </div>
                <div>
                    <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                </div>
                <div style="text-align: center">
                {PAGENO}
                </div>
            </div>';
                    $pdf->SetFooter($footercontents);
                    $pdf->WriteHTML($html);
                    //$this->mpdf->Output($file_name, 'D'); // download force

                    $pdf->Output('Inventory Sales.pdf', 'I'); // view in the explorer

                } else {
                    echo $this->load->view('inventory/reports/inventory_location_sales_table', $data);
                }
            } else if ($report_type == 'cost_center_assignements') {
                $data['print'] = $print;
                $source_id = $this->input->post('source_id');
                $source_id = $source_id != '' ? $source_id : null;
                $destination_id = $this->input->post('destination_id');
                $destination_id = $destination_id != '' ? $destination_id : null;
                $source = false;
                if (!is_null($source_id) && $source_id != 'all') {
                    $source = new Project();
                    $source->load($source_id);
                }

                $destination = false;
                if (!is_null($destination_id) && $destination_id != 'all') {
                    $destination = new Project();
                    $destination->load($destination_id);
                }

                $data['source'] = $source;
                $data['destination'] = $destination;
                $data['cost_center_assignments'] = $this->inventory_location->cost_center_assignments($source_id, $destination_id, $from, $to);
                if ($print) {

                    $html = $this->load->view('inventory/reports/material_cost_center_assignments_sheet', $data, true);

                    //this the PDF filename that user will get to download

                    //load mPDF library
                    $this->load->library('m_pdf');
                    //actually, you can pass mPDF parameter on this load() function
                    $pdf = $this->m_pdf->load();
                    $footercontents = '
            <div>
                <div style="text-align: left; float: left; width: 50%">
                    <strong>Printed By:</strong> <span>' . $this->session->userdata('employee_name') . '</span>
                </div>
                <div>
                    <strong>Date And Time:</strong> <span>' . strftime('%d/%m/%Y %H:%M:%S') . '</span>
                </div>
                <div style="text-align: center">
                {PAGENO}
                </div>
            </div>';
                    $pdf->SetFooter($footercontents);
                    $pdf->WriteHTML($html);
                    //$this->mpdf->Output($file_name, 'D'); // download force

                    $pdf->Output('Inventory Sales.pdf', 'I'); // view in the explorer

                } else {
                    echo $this->load->view('inventory/reports/material_cost_center_assignments_table', $data);
                }
            }
        } else {
            $data['title'] = 'Inventory | Reports';
            $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
            $data['project_options'] = $this->project->project_dropdown_options(false, true);
            $data['material_options'] = $this->material_item->dropdown_options('all');
            $data['location_options'] = $this->inventory_location->dropdown_options();
            $data['selected_report'] = $selected_report;
            $this->load->view('inventory/reports/index', $data);
        }
    }

    public function material_values()
    {
        set_time_limit(86400);
        ini_set('memory_limit', -1);
        if ($this->session->userdata('dashboard_data')) {
            $data = $this->session->userdata('dashboard_data');
        } else {

            $this->load->model('inventory_location');
            $locations = $this->inventory_location->get();
            $data['locations'] = [];
            foreach ($locations as $location) {
                $material_value = $location->total_material_balance_value();
                if ($material_value > 0) {
                    if ($location->{$location::DB_TABLE_PK} == 1) {
                        $sliced = $selected = true;
                    } else {
                        $sliced = $selected = false;
                    }
                    $data['locations'][] = [
                        'name' => $location->location_name,
                        'y' => $material_value,
                        'formated_value' => number_format($material_value, 2),
                        'sliced' => $sliced,
                        'selected' => $selected,
                        'key' => $location->{$location::DB_TABLE_PK}
                    ];
                }
            }

            $dashboard_data = [
                'dashboard_data' => $data
            ];

            $this->load->library('session');
            $this->session->set_userdata($dashboard_data);
        }
        echo json_encode($data);
    }

    public function respond_to_curl()
    {
        $this->load->model(['inventory_location', 'material_item', 'asset_item']);
        $locations = $this->inventory_location->dropdown_options(null, true);
        $material_item_id = $this->input->post('material_item_id');
        $asset_item_id = $this->input->post('asset_item_id');
        $item_type = $this->input->post('item_type');
        $date = $this->input->post('date');

        if ($item_type == "material" && $date) {
            $material_item = new Material_item();
            $material_item->load($material_item_id);
            $material_quantity = 0;
            foreach ($locations as $location) {
                $project_materials = $location->total_material_item_quantity('all', $material_item);
                $unassigned_materials = $location->total_material_item_quantity(null, $material_item);
                $material_quantity += $project_materials + $unassigned_materials;
            }

            $json['location_name'] = 'DERM STORE';
            $json['available_quantity'] = $material_quantity;
            echo json_encode($json);
        } else {

            $this->load->model('material_item');
            $asset_item = new Asset_item();
            $asset_item->load($asset_item_id);
            $asset_quantity = 0;
            foreach ($locations as $location) {
                $project_assets = $location->total_asset_item_quantity('all', $asset_item);
                $unassigned_asset = $location->total_asset_item_quantity(null, $asset_item);
                $asset_quantity = $project_assets + $unassigned_asset;
            }

            $json['location_name'] = 'DERM STORE';
            $json['available_quantity'] = $asset_quantity;
            echo json_encode($json);
        }
    }

    public function deactivate_sub_location()
    {
        $this->load->model('sub_location');
        if ($this->input->post('sub_location_id')) {
            $sub_location = new Sub_location();
            $sub_location->load($this->input->post('sub_location_id'));
            $sub_location->status = 'INACTIVE';
            $sub_location->save();
        }
    }
}
