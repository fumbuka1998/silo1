<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 30/01/2018
 * Time: 14:43
 */

class Assets extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_login();
    }

    public function index(){
        if(check_permission('Inventory',true) || check_permission('Administrative Actions',true)){
            $this->load->view('assets/index',['title' => 'Assets']);
        };
    }

    public function settings(){
        if(check_privilege('Assets Settings') || check_permission('Administrative Actions',true)) {
            $this->load->model(['asset_group']);
            $data = [
                'title' => 'Assets | Settings',
                'asset_group_options' => $this->asset_group->dropdown_options()
            ];

            $this->load->view('assets/settings/index', $data);
        }
    }

    public function asset_group_list(){

        $this->load->model('asset_group');
        $posted_params = dataTable_post_params();
        echo $this->asset_group->asset_group_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);

    }

    public function asset_items_list(){
        $this->load->model('asset_item');
        $posted_params = dataTable_post_params();
        echo $this->asset_item->asset_items_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function save_asset_group(){

        $this->load->model('asset_group');
        $group = new Asset_group();
        $edit = $group->load($this->input->post('group_id'));
        $group->description = $this->input->post('description');
        $group->created_at = datetime();
        $group->created_by = $this->session->userdata('employee_id');
        $group->group_name = $this->input->post('group_name');
        $group->parent_id = $this->input->post('parent_id');
        $group->parent_id = $group->parent_id != '' ? $group->parent_id : null;
        $parent = $group->parent();
        $group->level = $parent->level+1;
        $group->project_nature_id = $parent->project_nature_id;
        $group->save();

    }

    public function delete_asset_group(){
        $this->load->model('asset_group');
        $group = new Asset_group();
        if($group->load($this->input->post('group_id'))){
            $group->delete();
        }
    }

    public function save_asset_item()
    {
        $this->load->model('asset_item');
        $asset_item = new Asset_item();
        $edit = $asset_item->load($this->input->post('asset_item_id'));
        $asset_item->asset_name = $this->input->post('asset_name');
        $asset_item->asset_group_id = $this->input->post('asset_group_id');
        $asset_item->part_number = $this->input->post('part_number');
        $asset_item->description = $this->input->post('description');
        $asset_item->created_by = $this->session->userdata('employee_id');
        $asset_item->save();
    }

    public function save_multiple_assets(){
        $this->load->model('asset_item');

        $asset_names = $this->input->post('asset_names');

        foreach($asset_names as $index => $asset_name) {
            $asset_item = new Asset_item();
            $asset_item->asset_name = $this->input->post('asset_names')[$index];
            $asset_item->asset_group_id = $this->input->post('asset_group_ids')[$index];
            $asset_item->part_number = $this->input->post('part_numbers')[$index];
            $asset_item->description = $this->input->post('descriptions')[$index];
            $asset_item->created_by = $this->session->userdata('employee_id');
            $asset_item ->save();
        }
    }

    public function delete_asset_item(){
        $this->load->model('asset_item');
        $asset_item = new Asset_item();
        if($asset_item->load($this->input->post('asset_item_id'))){
            $asset_item->delete();
        }
    }

    private function serialize_asset_group_ids($groups)
    {
        $ids = [];
        foreach ($groups as $group) {
            array_push($ids,$group->{$group::DB_TABLE_PK});
            $children = $group->children_groups();
            if (sizeof($children) > 0) {
                array_push($ids,$this->serialize_asset_group_ids($children));
            }
        }
        return $ids;
    }

    public function download_asset_items_registration_excel_template()
    {
        //load our new PHPExcel library
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);

        $active_sheet = $this->excel->getActiveSheet();
        $active_sheet->setTitle('Asset Registration');

        //Protect Sheet
        $active_sheet->getProtection()->setPassword('asset@registration12');
        $active_sheet->getProtection()->setSheet(true);

        $this->load->model('asset_group');
        $groups = $this->asset_group->get(0, 0, ['level' => '1']);
        $hex_color = '9bc4c6';

        $active_sheet->getStyle('A1:C202')->applyFromArray([
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

        $active_sheet->getStyle('A2:C2')->applyFromArray($style['column_title']);


        $active_sheet->setCellValue('A1', 'UNCATEGORIZED');
        $active_sheet->setCellValue('A2', 'Item Name');
        $active_sheet->setCellValue('B2', 'Part Number');
        $active_sheet->setCellValue('C2', 'Description');
        //Unprotect Editable Cells
        $active_sheet->getStyle('A3:C202')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);



        $group_column_index = 'D';
        $asset_group_ids   = $this->serialize_asset_group_ids($groups);

        $asset_group_ids = new RecursiveIteratorIterator(new RecursiveArrayIterator($asset_group_ids));

        foreach ($asset_group_ids as $asset_group_id) {
            $group_column_index++;
            $group_start_column = $group_column_index;
            $hex_color = '9bc4c6';
            $group = new Asset_group();
            $group->load($asset_group_id);
            $hex_color = dechex(hexdec($hex_color)+(96*$group->level/2));
            $font_size = 18-($group->level);

            $active_sheet->setCellValue($group_column_index . '1', $asset_group_id);
            $active_sheet->setCellValue($group_column_index . '2', 'Item Name');
            $group_column_index++;
            $active_sheet->setCellValue($group_column_index . '1', $group->group_name);
            $active_sheet->setCellValue($group_column_index . '2', 'Part Number');
            $group_column_index++;
            $active_sheet->setCellValue($group_column_index . '1', 'LEVEL '.$group->level);
            $active_sheet->setCellValue($group_column_index . '2', 'Description');

            $group_end_column = $group_column_index;
            $active_sheet->getStyle($group_start_column.'1:'.$group_end_column.'202')->applyFromArray([
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => $hex_color],
                ]
            ]);
            $active_sheet->getStyle($group_start_column.'1:'.$group_end_column.'1')->applyFromArray([
                'font' => [
                    'size' => $font_size
                ]
            ]);

            $active_sheet->getStyle($group_start_column.'2:'.$group_end_column.'2')->applyFromArray($style['column_title']);
            $group_column_index++;

            //Unprotect Editable Cells
            $active_sheet->getStyle($group_start_column.'3:'.$group_end_column.'202')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

        }

        for($col_index = 'A'; $col_index !== $group_column_index; $col_index++) {
            $active_sheet->getColumnDimension($col_index)->setAutoSize(true);
        }

        $active_sheet->getStyle('A1:'.$group_column_index.'202')->applyFromArray([
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
        $active_sheet->getStyle('A1:'.$group_column_index.'2')->applyFromArray($style['column_heading']);

        $sheet_dimension = $active_sheet->getHighestRowAndColumn();

        //Freeze the fixed panes
        $active_sheet->freezePane('A3');

        //Unprotect The rate column
        $active_sheet->getStyle('A:'.$group_column_index)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);


        $filename = 'Assets Registration Template';
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0'); //no cache
        ob_end_clean();
        $objWriter->save('php://output');

    }

    public function upload_asset_registration_excel(){
        $this->load->library('excel');
        $file = $_FILES['file']['tmp_name'];
        //read file from path
        $objPHPExcel = PHPExcel_IOFactory::load($file);

        $active_sheet = $objPHPExcel->getActiveSheet();

        $hash = $active_sheet->getProtection()->getPassword(); // returns a hash
        $valid = ($hash === PHPExcel_Shared_PasswordHasher::hashPassword('asset@registration12'));
        if($valid){
            $sheet_dimension = $active_sheet->getHighestRowAndColumn();

            $this->load->model('asset_item');

            //Uncategorized Asset Items

            for ($row_index = 3; $row_index <= $sheet_dimension['row']; $row_index++) {
                echo  $asset_name = trim($active_sheet->getCell("A" . $row_index)->getFormattedValue());
                if ($asset_name != '') {
                    $asset_item = new Asset_item();
                    $asset_item->asset_name = $asset_name;
                    $asset_item->asset_group_id = null;
                    $asset_item->part_number = $active_sheet->getCell("B" . $row_index)->getFormattedValue();
                    $asset_item->description = trim($active_sheet->getCell("C" . $row_index)->getFormattedValue());
                    $asset_item->created_by= $this->session->userdata('employee_id');
                    $asset_item->save();

                }
            }

            //Categorized asset_items

            $MAX_COL_INDEX = PHPExcel_Cell::columnIndexFromString($sheet_dimension['column']);

            for($index = PHPExcel_Cell::columnIndexFromString('D'); $index < $MAX_COL_INDEX; $index = $index+4){
                $col = PHPExcel_Cell::stringFromColumnIndex($index);
                $part_number_col = PHPExcel_Cell::stringFromColumnIndex($index+1);
                $description_col = PHPExcel_Cell::stringFromColumnIndex($index+2);
                $asset_group_id = $active_sheet->getCell($col. '1')->getFormattedValue();

                for ($row_index = 3; $row_index <= $sheet_dimension['row']; $row_index++) {
                    $asset_name = trim($active_sheet->getCell($col . $row_index)->getFormattedValue());
                    if ($asset_name != '') {
                        $asset_item = new Asset_item();
                        $asset_item->asset_name = $asset_name;
                        $asset_item->asset_group_id = $asset_group_id;
                        $asset_item->part_number = $active_sheet->getCell($part_number_col . $row_index)->getFormattedValue();
                        $asset_item->description = trim($active_sheet->getCell($description_col . $row_index)->getFormattedValue());
                        $asset_item->created_by= $this->session->userdata('employee_id');
                        $asset_item->save();
                    }
                }

            }


        }
    }

    public function save_asset_registrations()
    {
        $this->load->model(['asset','asset_sub_location_history']);
        $asset_item_ids = $this->input->post('asset_item_ids');
        foreach($asset_item_ids as $index => $asset_item_id){
            $asset = new Asset();
            $asset->asset_item_id = $this->input->post('asset_item_ids')[$index];
            $asset->book_value = $this->input->post('book_values')[$index];
            $asset->salvage_value = $this->input->post('salvage_values')[$index];
            $asset->status = $this->input->post('statuses')[$index];
            $asset->registration_date = $this->input->post('registration_dates')[$index];
            $asset->asset_code = $this->input->post('asset_codes')[$index];
            $asset->description = $this->input->post('descriptions')[$index];
            $asset->ownership = $this->input->post('ownerships')[$index];
            $asset->created_by = $this->session->userdata('employee_id');
            $asset->save();

            //asset sub_location_history start

            $asset_sub_location_history = new Asset_sub_location_history();
            $asset_sub_location_history->received_date = $asset->registration_date;
            $asset_sub_location_history->asset_id= $asset->{$asset::DB_TABLE_PK};
            $asset_sub_location_history->sub_location_id= $this->input->post('sub_location_id');
            $asset_sub_location_history->book_value = $asset->book_value;
            $asset_sub_location_history->description="First Time Registration";
            $asset_sub_location_history->created_by= $this->session->userdata('employee_id');
            $asset_sub_location_history->save();

        }

    }

    public function location_assets_datatable($level, $id){
        $this->load->model('asset');
        $params= dataTable_post_params();
        echo $this->asset->location_assets_datatable($level,$id, $params['limit'], $params['start'], $params['keyword'], $params['order']);
    }

    public function edit_asset_details()
    {
        $this->load->model('asset');
        $asset = new Asset();
        $asset->load($this->input->post('asset_id'));
        $asset->book_value = $this->input->post('book_value');
        $asset->salvage_value = $this->input->post('salvage_value');
        $asset->registration_date = $this->input->post('registration_date');
        $asset->asset_code = $this->input->post('asset_code');
        $asset->status = $this->input->post('status');
        $asset->save();
    }

    public function location_available_stock_options()
    {
        $this->load->model('asset');
        echo stringfy_dropdown_options(
            $this->asset->location_asset_options(
                $this->input->post('level'),
                $this->input->post('id'),
                $this->input->post('project_id')
            )
        );
    }

    public function save_handover_asset(){

        $asset_ids = $this->input->post('asset_ids');
        if (!empty($asset_ids)) {
            $this->load->model('Asset_handover');
            $asset_handover = new Asset_handover();
            $asset_handover->load($this->input->post('handover_id'));
            $asset_handover->location_id = $this->input->post('location_id');
            $asset_handover->handover_date = $this->input->post('handover_date');
            $asset_handover->handler_id = $this->input->post('employee_id');
            $asset_handover->comments = $this->input->post('comment');
            $asset_handover->created_by = $this->session->userdata('employee_id');

            if ($asset_handover->save()) {
                $asset_handover->clear_items();

                $this->load->model('Asset_handover_item');
                foreach ($asset_ids as $index => $asset_id) {
                    $this->load->model(['asset_handover_item','asset']);
                    $asset_handover_item = new Asset_handover_item();
                    $asset_handover_item->asset_handover_id = $asset_handover->{$asset_handover::DB_TABLE_PK};
                    $asset = new Asset();
                    $asset->load($asset_id);
                    $latest_history = $asset->latest_sub_location_history();
                    $asset_handover_item->asset_sub_location_history_id = $latest_history->{$latest_history::DB_TABLE_PK};
                    $asset_handover_item->remarks= $this->input->post('remarks')[$index];
                    $asset_handover_item->save();
                }
            }

        }
    }

    public function delete_handover_asset(){
        $this->load->model('Asset_handover');
        $asset_handover = new Asset_handover();
        $asset_handover->load($this->input->post('handover_id'));
        $asset_handover->delete();
    }

    public function assets_handover_list($location_id = 0){
        $this->load->model('Asset_handover');
        $posted_params = dataTable_post_params();
        echo $this->Asset_handover->assets_handover_list($location_id,$posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function preview_assets_handover($handover_number = 0)
    {
        $this->load->model('Asset_handover');
        $asset_handover = new Asset_handover();
        if($asset_handover->load($handover_number)) {

            $data['asset_handover'] = $asset_handover;
            $html = $this->load->view('inventory/documents/preview_assets_handover',$data,true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            $pdf->setFooter('<div style="text-align: center">Page {PAGENO} of {nb}</div>');
            //generate the PDF!
            $pdf->WriteHTML($html);
            $pdf->Output('Asset_handover_list' . date('Y-m-d') . '.pdf', 'I');
        }
        else {
            redirect(base_url());
        }
    }

    public function check_store_available_asset_item_quantity(){
        $this->load->model(['asset_item','inventory_location']);
        $asset_item = new Asset_item();
        $location = new Inventory_location();
        $location->load($this->input->post('location_id'));
        $sub_location_ids = $location->sub_location_ids_query();
        $asset_item->load($this->input->post('asset_item_id'));
        $project_id = $this->input->post('project_id');
        $approval_module_id = $this->input->post('approval_module_id');
        if($approval_module_id == '2' && $project_id != ''){
            echo $asset_item->sub_location_available_stock($sub_location_ids, $project_id,true) + $asset_item->sub_location_available_stock($sub_location_ids, null,true);
        } else {
            echo $asset_item->sub_location_available_stock($sub_location_ids, null,true);
        }
    }

    public function get_asset_item_sub_location_assets_options()
    {
        $this->load->model('asset_item');
        $asset_item = new Asset_item();
        $asset_item->load($this->input->post('asset_item_id'));
        $project_id = $this->input->post('project_id');
        $project_id = $project_id != '' ? $project_id : null;
        $assets = $asset_item->sub_location_available_stock($this->input->post('sub_location_id'),$project_id) + $asset_item->sub_location_available_stock($this->input->post('sub_location_id'));
        $options[''] = '&nbsp;';
        foreach ($assets as $asset){
            $options[$asset->{$asset::DB_TABLE_PK}] = $asset->asset_code();
        }
        echo stringfy_dropdown_options($options);
    }

    public function load_sub_location_available_project_assets(){
        $sub_location_id = $this->input->post('source_sub_location_id');
        $project_id = $this->input->post('project_id');
        $source_project_id = $project_id != '' ? $project_id : null;
        $this->load->model('asset');
        if($sub_location_id){
            echo stringfy_dropdown_options($this->asset->location_asset_options("sub_location", $sub_location_id, $source_project_id));
        }
    }

    public function save_asset_cost_center_assignment(){
        $this->load->model(['asset_cost_center_assignment','asset','asset_cost_center_assignment_item']);
        $asset_ca = new Asset_cost_center_assignment();
        $asset_ca->assignment_date = $this->input->post('assignment_date');
        $asset_ca->location_id = $this->input->post('location_id');
        $asset_ca->source_project_id = $this->input->post('source_project_id');
        $asset_ca->source_project_id = $asset_ca->source_project_id != '' ? $asset_ca->source_project_id : null;
        $asset_ca->destination_project_id = $this->input->post('destination_project_id');
        $asset_ca->destination_project_id = $asset_ca->destination_project_id != '' ? $asset_ca->destination_project_id : null;
        $asset_ca->created_by = $this->session->userdata('employee_id');
        if($asset_ca->save()){
            $item_ids = $this->input->post('item_ids');
            if(!empty($item_ids)){
                foreach($item_ids as $index=>$item_id){
                    $asset = new Asset();
                    $asset->load($item_id);
                    $last_history = $asset->latest_sub_location_history();
                    $asset_sub_location_history = new Asset_sub_location_history();
                    $asset_sub_location_history->received_date = $asset_ca->assignment_date;
                    $asset_sub_location_history->asset_id = $item_id;
                    $asset_sub_location_history->book_value = $last_history->book_value;
                    $asset_sub_location_history->project_id = intval($asset_ca->destination_project_id) > 0 ? $asset_ca->destination_project_id : null;
                    $asset_sub_location_history->sub_location_id = $this->input->post('sub_location_ids')[$index];
                    $description = $this->input->post('descriptions')[$index];
                    $asset_sub_location_history->description = $description != '' ? 'As assigned by ACA No ' . $asset_ca->assignment_number().': '.$description.'' : 'As assigned by ACA No ' . $asset_ca->assignment_number();
                    $asset_sub_location_history->created_by = $this->session->userdata('employee_id');
                    if ($asset_sub_location_history->save()) {
                        $asset_ca_item = new Asset_cost_center_assignment_item();
                        $asset_ca_item->asset_sub_location_history_id = $asset_sub_location_history->{$asset_sub_location_history::DB_TABLE_PK};
                        $asset_ca_item->asset_cost_center_assignment_id = $asset_ca->{$asset_ca::DB_TABLE_PK};
                        $asset_ca_item->save();
                    }
                }
            }

        }
    }

    public function reports($selected_report = null){
        if(check_privilege('Assets Reports') || check_permission('Administrative Actions',true)) {
            $report_type = $this->input->post('report_type');
            $report_type = $report_type != '' ? $report_type : null;
            $location_id = $this->input->post('location_id');
            $location_id = $location_id != '' ? $location_id : null;
            $asset_group_id = $this->input->post('asset_group_id');
            $asset_group_id = $asset_group_id != '' ? $asset_group_id : null;
            $asset_item_id = $this->input->post('asset_item_id');
            $asset_item_id = $asset_item_id != '' ? $asset_item_id : null;
            $data['from'] = $from = $this->input->post('from');
            $data['to'] = $to = $this->input->post('to');
            $data['print'] = $print = $this->input->post('print');
            $data['title'] = 'Assets | Reports';

            if (!is_null($report_type)) {

                if ($report_type == 'asset_item_availability') {
                    if (!is_null($asset_item_id)) {
                        $data['filtered'] = true;

                        $sql = 'SELECT asset_id, main_table.sub_location_id, sub_locations.location_id, location_name, 1 AS quantity
                            FROM asset_sub_location_histories AS main_table
                            LEFT JOIN assets ON main_table.asset_id = assets.id
                            LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id
                            LEFT JOIN sub_locations ON main_table.sub_location_id = sub_locations.sub_location_id
                            LEFT JOIN inventory_locations ON sub_locations.location_id = inventory_locations.location_id
                            WHERE asset_item_id = ' . $asset_item_id . '
                            AND main_table.asset_id NOT IN (
                                SELECT asset_id
                                FROM asset_sub_location_histories AS sub_table
                                WHERE main_table.asset_id = sub_table.asset_id
                                AND sub_table.id > main_table.id
                            )';

                    } else {

                        $data['filtered'] = false;
                        $sql = 'SELECT
                            asset_items.id,
                            asset_name, inventory_locations.location_id, location_name,
                            COUNT(assets.id) AS quantity
                            FROM asset_sub_location_histories AS main_table
                            LEFT JOIN assets ON main_table.asset_id = assets.id
                            LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id
                            LEFT JOIN sub_locations ON main_table.sub_location_id = sub_locations.sub_location_id
                            LEFT JOIN inventory_locations ON sub_locations.location_id = inventory_locations.location_id
                            WHERE main_table.asset_id NOT IN (
                                SELECT asset_id
                                FROM asset_sub_location_histories AS sub_table
                                WHERE main_table.asset_id = sub_table.asset_id
                                AND sub_table.id > main_table.id
                            )';
                    }

                    if (!is_null($location_id)) {
                        $sql .= ' AND inventory_locations.location_id = "' . $location_id . '"';
                    }

                    if (!is_null($asset_group_id)) {
                        $sql .= ' AND asset_group_id = "' . $asset_group_id . '"';
                    }

                    $sql .= ' AND received_date >="' . $from . '"
                          AND received_date <="' . $to . '" ';

                    if (is_null($asset_item_id)) {
                        $sql .= 'GROUP BY asset_item_id,sub_locations.location_id';
                    }

                    $query = $this->db->query($sql);
                    $results = $query->result();

                    $table_items = [];
                    if (!is_null($asset_item_id)) {
                        $this->load->model(['asset', 'asset_item']);
                        $asset_item = new Asset_item();
                        $asset_item->load($asset_item_id);
                        $data['asset_item'] = $asset_item;

                        foreach ($results as $result) {
                            $asset = new Asset();
                            $asset->load($result->asset_id);
                            $table_items[] = [
                                'asset_name' => $asset->asset_code(),
                                'location_name' => !isset($print) ? anchor(base_url("inventory/location_profile/" . $result->location_id), $result->location_name, 'target="_blank"') : $result->location_name,
                                'quantity' => $result->quantity
                            ];
                        }

                    } else {
                        foreach ($results as $result) {
                            $table_items[] = [
                                'asset_name' => $result->asset_name,
                                'location_name' => !isset($print) ? anchor(base_url("inventory/location_profile/" . $result->location_id), $result->location_name, 'target="_blank"') : $result->location_name,
                                'quantity' => $result->quantity
                            ];
                        }
                    }

                    $data['table_items'] = $table_items;

                    if ($print) {

                        $html = $this->load->view('assets/reports/assets_availability_sheet', $data, true);

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

                        $pdf->Output('Assets Availability.pdf', 'I'); // view in the explorer

                    } else {
                        echo $this->load->view('assets/reports/assets_availability_table', $data);
                    }
                }

            } else {
                $this->load->model([
                    'asset_group',
                    'asset_item',
                    'inventory_location'
                ]);
                $data['project_options'] = projects_dropdown_options();
                $data['asset_options'] = $this->asset_item->dropdown_options();
                $data['asset_group_options'] = $this->asset_group->dropdown_options();
                $data['location_options'] = $this->inventory_location->dropdown_options();
                $data['selected_report'] = $selected_report;
                $this->load->view('assets/reports/index', $data);
            }
        }
    }

    public function hired_assets($type = null)
    {
        $this->load->model([
            'project','asset_group',
            'vendor',
            'hired_asset',
            'asset_item',
            'asset_sub_location_history',
            'client'
        ]);
        if((check_permission('Inventory',true) || check_permission('Administrative Actions',true)) && !is_null($type)) {
            $limit = $this->input->post('length');
            if ($limit) {
                $posted_params = dataTable_post_params();
                echo $this->hired_asset->hired_assets($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'], $type);
            } else {
                $data['project_options'] = $this->project->project_dropdown_options();
                $data['asset_group_options'] = $this->asset_group->dropdown_options();
                $data['asset_item_options'] = $this->asset_item->dropdown_options();
                $data['vendor_options'] = $this->vendor->vendor_options();
                $data['client_options'] = $this->client->clients_options();
                $data['asset_options'] = $this->asset_sub_location_history->unassigned_assets_options();
                $data['list_type'] = $type;
                $this->load->view('assets/hired_assets/index', $data);
            }
        }
    }

    public function load_project_sub_locations()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->input->post('project_id'));
        echo stringfy_dropdown_options( $project->project_sub_locations_dropdown_options());
    }

    public function save_hired_assets()
    {
        $this->load->model(['asset','hired_asset']);
        $edit = $this->input->post('hired_asset_id');
        $type = $this->input->post('type');
        $item_id = $this->input->post('item_id');
        $asset = new Asset();
        $junction = new Hired_asset();
        if($type == "SUPPLIERS"){
            if($edit){
                $junction->load($this->input->post('hired_asset_id'));
                $junction_asset = $junction->asset();
                $asset->load($junction_asset->{$junction_asset::DB_TABLE_PK});
            }
            $asset->asset_item_id = $item_id;
            $asset->asset_code = '';
            $asset->book_value = $this->input->post('hiring_cost');
            $asset->status = 'active';
            $asset->description = $this->input->post('description');
            $asset->ownership = 'HIRED';
            $asset->created_by = $this->session->userdata('employee_id');
            $asset->save();
        }

        if(($type == "SUPPLIERS" && !empty($asset)) || $type == "CLIENTS"){
            $junction->project_id = $this->input->post('project_id');
            $junction->sub_location_id = $this->input->post('sub_location_id');
            if($type == "CLIENTS"){
                $junction->client_id = $this->input->post('other_end_id');
            } else {
                $junction->vendor_id = $this->input->post('other_end_id');
            }
            $junction->asset_id = $item_id;
            $junction->hiring_cost = $this->input->post('hiring_cost');
            $junction->type = $this->input->post('type');
            $junction->hired_date = $this->input->post('hired_date');
            $junction->dead_line = $this->input->post('dead_line');
            $junction->status = 'ACTIVE';
            $junction->save();
        }
    }

    public function deactivate_hired_asset(){
        $hired_asset_id = $this->input->post('hired_asset_id');
        $this->load->model(['asset', 'hired_asset']);
        $h_asset = new Hired_asset();
        $h_asset->load($hired_asset_id);
        $h_asset->status = 'INACTIVE';
        if($h_asset->save()){
            $asset = new Asset();
            $asset->load($h_asset->asset_id);
            $asset->status = 'inactive';
            $asset->save();
        }
    }

    public function activate_hired_asset(){
        $hired_asset_id = $this->input->post('hired_asset_id');
        $this->load->model(['asset', 'hired_asset']);
        $h_asset = new Hired_asset();
        $h_asset->load($hired_asset_id);
        $h_asset->status = 'ACTIVE';
        if($h_asset->save()){
            $asset = new Asset();
            $asset->load($h_asset->asset_id);
            $asset->status = 'active';
            $asset->save();
        }
    }

    public function preview_asset_cost_center_assignment($cost_center_assignment_id){
        $this->load->model(['asset_cost_center_assignment','asset_cost_center_assignment_item']);
        $cost_center_assignment= new Asset_cost_center_assignment();
        if ($cost_center_assignment->load($cost_center_assignment_id) ){

            $data['assignment_type'] = "asset";
            $data['cost_center_assignment'] = $cost_center_assignment;
            $data['cost_center_assigned_items'] = $cost_center_assignment->cost_center_assignment_items();

            $html = $this->load->view('inventory/cost_center_assignment/preview_cost_center_assignment', $data, true);
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            $pdf->setFooter('<div style="text-align: center">Page {PAGENO} of {nb}</div>');
            //generate the PDF!
            $pdf->WriteHTML($html);
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output('Cost_center_Assignment' . date('Y-m-d') . '.pdf', 'I');

        }else{
            redirect(base_url());
        }
    }

}
