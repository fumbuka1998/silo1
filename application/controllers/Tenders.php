<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/26/2018
 * Time: 8:00 PM
 */

class Tenders extends CI_Controller{

    public function __construct() {
        parent::__construct();
        check_login();
    }

    public function index(){
        $this->load->model('project_category');
        $data['categories'] = $this->project_category->get();
        $data['title'] = 'Tenders';
        $data['number_of_tender'] = $this->db->count_all('tenders');
        $this->load->view('tenders/index', $data);
    }
    
    public function tenders_list()
    {
        check_permission('Tenders', true);
        $this->load->model('tender');
        $limit = $this->input->post('length');
        if ($limit != '') {
            $posted_params = dataTable_post_params();
            echo $this->tender->tenders_list(null,null,$posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
        } else {
            $this->load->model(['currency', 'client', 'project_category', 'employee']);
            $data['project_categories'] = $this->project_category->category_options();
            $data['supervisors_options'] = employee_options();
            $data['currency_options'] = $this->currency->dropdown_options();
            $data['client_options'] = $this->client->clients_options();
            $data['title'] = 'Tenders';
            $this->load->view('tenders/tenders_list',$data);
        }
    }

    public function tenders_categorized($level=0,$id=0)
    {
        check_permission('Tenders', true);
        $this->load->model('tender');
        $limit = $this->input->post('length');
        if ($limit != '') {
            $posted_params = dataTable_post_params();
            echo $this->tender->tenders_list($level,$id,$posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
        } else {
            $this->load->model('project_category');
            $project_category = new Project_category();
            $project_category->load($id);
            $data['category'] = $project_category;
            $data['title'] = 'Tenders Categories';
            $this->load->view('tenders/tenders_categorized_list',$data);
        }
    }
    public function delete_tender()
    {
        $this->load->model('tender');
        $tender = new Tender();
        $tender->load($this->input->post('tender_id'));
        $description = 'Tender' . $tender->tender_name . ' was deleted!';
        system_log($description);
        $tender->delete();
        
    }

    public function delete_tender_component()
    {
        $this->load->model('tender_component');
        $tender_component = new Tender_component();
        $tender_component->load($this->input->post('component_id'));
        $tender_component->delete();
    }
    
    public function tender_profile($tender_no=0){
        check_permission('Tenders', true);
        $this->load->model('tender');
        $tender = new Tender();
        if ($tender->load($tender_no)) {
            $this->load->model(['currency', 'client', 'project_category', 'employee','tender_requirement_type']);
            $data['project_categories'] = $this->project_category->category_options();
            $data['supervisors_options'] = employee_options();
            $data['currency_options'] = $this->currency->dropdown_options();
            $data['client_options'] = $this->client->clients_options();
            $data['requirement_type_options'] = $this->tender_requirement_type->dropdown_options();
            $data['title'] = $tender->tender_name;
            $data['tender'] = $tender;
            $data['material_dropdown_options'] = material_item_dropdown_options($tender->project_category_id);
            $this->load->view('tenders/profile/tender_profile', $data);
        }else{
            redirect(base_url());
        }

    }

    public function components_list(){
        $this->load->model('tender');
        $tender = new Tender();
        $tender->load($this->input->post('tender_id'));
        $data['tender'] = $tender;
        $data['material_dropdown_options'] = material_item_dropdown_options($tender->project_category_id);
        $data['tender_components'] = $tender->get_components($this->input->post('keyword'));
        $this->load->view('tenders/profile/components/component_list', $data);
    }

    public function save_tenders(){
        $this->load->model('tender');
        $tender = new Tender();
        $tender->project_category_id = $this->input->post('project_category_id');
        $tender->client_id = $this->input->post('client_id');
        $tender->tender_name = $this->input->post('tender_name');
        $tender->date_announced = $this->input->post('date_announced');
        $tender->submission_deadline = $this->input->post('submission_deadline');
        $tender->date_procured = $this->input->post('date_procured');
        $tender->procurement_cost = $this->input->post('procurement_cost');
        $tender->procurement_currency_id = $this->input->post('procurement_currency_id');
        $tender->supervisor_id = $this->input->post('supervisor_id');
        $tender->created_by = $this->session->userdata('employee_id');
        $tender->save();

    }

    public function save_tender_component(){
        $this->load->model('tender_component');
        $component = new Tender_component();
        $component->load($this->input->post('tender_component_id'));
        $component->component_name = $this->input->post('component_name');
        $component->tender_id = $this->input->post('tender_id');
        $component->lumpsum_price = $this->input->post('lumpsum_price');
        $component->created_by = $this->session->userdata('employee_id');
        $component->save();
    }

    public function settings(){
        check_permission('Tenders',true);
        $data['title'] = 'Tenders | Settings';
        $this->load->view('tenders/settings/index',$data);
    }

    public function save_requirement(){
        $this->load->model('tender_requirement');
        $tender_requirement = new Tender_requirement();
        $tender_requirement->tender_requirement_type_id = $this->input->post('tender_requirement_id');
        $tender_requirement->tender_id = $this->input->post('tender_id');
        $tender_requirement->description = $this->input->post('description');
        $tender_requirement->created_by = $this->session->userdata('employee_id');
        $tender_requirement->save();
    }

    public function save_requirement_type(){
        $this->load->model('tender_requirement_type');
        $tender_requirement_type = new Tender_requirement_type();
        $tender_requirement_type->load($this->input->post('requirement_type_id'));
        $tender_requirement_type->requirement_name = $this->input->post('requirement_name');
        $tender_requirement_type->description = $this->input->post('description');
        $tender_requirement_type->created_by = $this->session->userdata('employee_id');
        $tender_requirement_type->save();
    }

    public function delete_requirement_type(){
        $this->load->model('tender_requirement_type');
        $tender_requirement_type = new Tender_requirement_type();
        $tender_requirement_type->load($this->input->post('requirement_type_number'));
        $tender_requirement_type->delete();
    }

    public function requirement_type_list(){
        $this->load->model('tender_requirement_type');
        $posted_params = dataTable_post_params();
        echo $this->tender_requirement_type->requirement_type_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function requirement_list(){
        $this->load->model('Tender_requirement');
        $posted_params = dataTable_post_params();
        echo $this->Tender_requirement->requirement_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function tender_sub_components($tender_component_id){
        $this->load->model('tender_sub_component');
        $posted_params = dataTable_post_params();
        echo $this->tender_sub_component->tender_sub_components_list($tender_component_id,$posted_params['limit'],$posted_params['start'],$posted_params['keyword'],$posted_params['order']);
    }

    public function save_tender_sub_components(){
        $this->load->model('tender_sub_component');
        $tender_sub_component = new Tender_sub_component();
        $tender_sub_component->load($this->input->post('sub_component_id'));
        $tender_sub_component->tender_component_id = $this->input->post('tender_component_id');
        $tender_sub_component->sub_component_name = $this->input->post('sub_component_name');
        $tender_sub_component->lumpsum_price = $this->input->post('lumpsum_price');
        $tender_sub_component->created_by = $this->session->userdata('employee_id');
        $tender_sub_component->save();
    }

    public function delete_tender_sub_component(){
        $this->load->model('tender_sub_component');
        $tender_sub_component = new Tender_sub_component();
        $tender_sub_component->load($this->input->post('sub_component_id'));
        $tender_sub_component->delete();
    }

    public function save_material_price(){
        $material_ids = $this->input->post('material_ids');
        if ($material_ids !='') {
            foreach ($material_ids as $index => $material_id) {
                $this->load->model('Tender_material_price');
                $tender_material_price = new Tender_material_price();
                $tender_material_price->material_item_id = $material_id;
                $tender_material_price->quantity = $this->input->post('quantities')[$index];
                $tender_material_price->price = $this->input->post('prices')[$index];
                $tender_material_price->description = $this->input->post('remarks')[$index];
                $tender_material_price->created_by = $this->session->userdata('employee_id');
                if($tender_material_price->save()){
                    $this->load->model('tender_component_material_price');
                    $tender_component_material_price = new Tender_component_material_price();
                    $tender_component_material_price->tender_component_id = $this->input->post('tender_component_id');
                    $tender_component_material_price->tender_material_price_id = $tender_material_price->{$tender_material_price::DB_TABLE_PK};
                    $tender_component_material_price->save();
                }

            }
        }
    }

    public function save_edit_material_price(){
        $this->load->model('Tender_material_price');
        $tender_material_price = new Tender_material_price();
        $tender_material_price->load($this->input->post('material_id'));
        $tender_material_price->quantity = $this->input->post('quantity');
        $tender_material_price->price = $this->input->post('price');
        $tender_material_price->description = $this->input->post('description');
        $tender_material_price->created_by = $this->session->userdata('employee_id');
        $tender_material_price->save();
    }

    public function delete_material_price(){
        $this->load->model('Tender_material_price');
        $tender_material_price = new Tender_material_price();
        $tender_material_price->load($this->input->post('material_price_id'));
        $tender_material_price->delete();
    }

    public function material_price_list($tender_component_id = 0){
        $this->load->model('Tender_material_price');
        $posted_params = dataTable_post_params();
        echo $this->Tender_material_price->material_price_list($tender_component_id,$posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function save_lumpsum_price(){

        $this->load->model('Tender_lumpsum_price');
        $amount = $this->input->post('amount');
        foreach ($amount as $index => $amount) {
            $tender_lumpsum_price = new Tender_lumpsum_price();
            $tender_lumpsum_price->description = $this->input->post('description')[$index];
            $tender_lumpsum_price->amount = $this->input->post('amount')[$index];
            $tender_lumpsum_price->created_by = $this->session->userdata('employee_id');
            if($tender_lumpsum_price->save()){
                $this->load->model('tender_component_lumpsum_price');
                $tender_component_lumpsum_price = new Tender_component_lumpsum_price();
                $tender_component_lumpsum_price->tender_component_id = $this->input->post('tender_component_id');
                $tender_component_lumpsum_price->tender_lumpsum_price_id = $tender_lumpsum_price->{$tender_lumpsum_price::DB_TABLE_PK};
                $tender_component_lumpsum_price->save();
            }

        }
    }

    public function save_edit_lumpsum_price(){
        $this->load->model('Tender_lumpsum_price');
        $tender_lumpsum_price = new Tender_lumpsum_price();
        $tender_lumpsum_price->load($this->input->post('tender_lumpsum_price_id'));
        $tender_lumpsum_price->description = $this->input->post('description');
        $tender_lumpsum_price->amount = $this->input->post('amount');
        $tender_lumpsum_price->created_by = $this->session->userdata('employee_id');
        $tender_lumpsum_price->save();
    }

    public function delete_lumpsum_price(){
        $this->load->model('Tender_lumpsum_price');
        $tender_lumpsum_price = new Tender_lumpsum_price();
        $tender_lumpsum_price->load($this->input->post('lumpsum_price_number'));
        $tender_lumpsum_price->delete();
    }

    public function lumpsum_price_list($tender_component_id = 0){
        $this->load->model('Tender_lumpsum_price');
        $posted_params = dataTable_post_params();
        echo $this->Tender_lumpsum_price->lumpsum_price_list($tender_component_id,$posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function export_tender_to_excel($id = 0)
    {
        $this->load->model('tender');
        $tender = new Tender();
        if($tender->load($id)) {


            //load our new PHPExcel library
            $this->load->library('excel');
            $this->excel->setActiveSheetIndex(0);

            $style['splitter_column'] = [
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => '000000']
                ]
            ];

            $style['component_details'] = [
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => 'd6dce4']
                ],
                'font' => [
                    'bold' => true,
                    'size' => 13,
                    'italic' => true
                ]
            ];

            $style['bordered'] = [
                'borders' => [
                    'allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]
                ],

            ];
            $style['column_heading'] = [
                'font' => [
                    'bold' => true,
                    'underline' => true,
                ]
            ];

            //Freeze the fixed panes
            $active_sheet = $this->excel->getActiveSheet();
            $active_sheet->freezePane('AB5');
//set cell A1 content with some text
            $active_sheet->setCellValue('A1',$tender->tender_name);
//change the font size
            $active_sheet->getStyle('A4:F4')->getFont()->setSize(13);
            $active_sheet->getStyle('A1')->getFont()->setSize(14);
            //make the font become bold
            $active_sheet->getStyle('A4:F4')->getFont()->setBold(true);
            $active_sheet->getStyle('A1')->getFont()->setBold(true);
            $active_sheet->getStyle('A3')->applyFromArray($style['column_heading']);


//merge cell A1 to F1
            $active_sheet->mergeCells('A1:F1');
            $active_sheet->mergeCells('A3:F3');
            $active_sheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $active_sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


            $active_sheet->getStyle('A4:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $active_sheet->setCellValue('A4','No');
            $active_sheet->setCellValue('B4','Description');
            $active_sheet->setCellValue('C4','Unit');
            $active_sheet->setCellValue('D4','Quantity');
            $active_sheet->setCellValue('E4','Rate');
            $active_sheet->setCellValue('F4','Amount');

            $components = $tender->get_components();
            $row_index = 5;
            $sn = 1;
            foreach ($components as $component){
                $lumpsum_prices = $component->lumpsum_prices();
                $active_sheet->setCellValue('A3','LUMPSUM PRICING');
                $active_sheet->setCellValue('B'.$row_index,$component->component_name);
                $active_sheet->setCellValue('F'.$row_index,$component->lumpsum_price);
                $active_sheet->getStyle('B'.$row_index)->getFont()->setBold(true);
                $active_sheet->getStyle('A'.$row_index.':F'.$row_index)->applyFromArray($style['bordered']);
                $row_index++;
                foreach ($lumpsum_prices as $lumpsum_price){
                    $active_sheet->setCellValue('A'.$row_index,$sn);
                    $active_sheet->setCellValue('B'.$row_index,$lumpsum_price->description);
                    $active_sheet->setCellValue('C'.$row_index,'Item');
                    $active_sheet->setCellValue('D'.$row_index,1);
                    $active_sheet->setCellValue('E'.$row_index,$lumpsum_price->amount);
                    $active_sheet->setCellValue('F'.$row_index,$lumpsum_price->amount);
                    $active_sheet->getStyle('A'.$row_index.':F'.$row_index)->applyFromArray($style['bordered']);
                    $row_index++;
                    $sn++;
                }
                $active_sheet->getStyle('F'.$row_index)->applyFromArray($style['bordered']);


            }

            $active_sheet->setCellValue('F'.$row_index,'=SUM(F5:F'.($row_index-1).')');
            $active_sheet->getStyle('F'.$row_index)->getFont()->setBold(true);


            $components = $tender->get_components();

            $sn = 1;
            $row_index = $row_index+3;
            $active_sheet->setCellValue('A'.($row_index-1),'MATERIAL PRICING');
            $active_sheet->getStyle('A'.($row_index-1))->applyFromArray($style['column_heading']);
            $active_sheet->mergeCells('A'.($row_index-1).':F'.($row_index-1).'');
            $active_sheet->getStyle('A'.($row_index-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $material_start = $row_index;

            foreach ($components as $component){
                $this->load->model('tender_component');
                $material_prices = $component->material_prices();
                $active_sheet->setCellValue('B'.$row_index,$component->component_name);
                $active_sheet->getStyle('B'.$row_index)->getFont()->setBold(true);
                $active_sheet->getStyle('A'.$row_index.':F'.$row_index)->applyFromArray($style['bordered']);

                $row_index++;
                foreach ($material_prices as $material_price){
                    $material_item = $material_price->material_item();
                    $active_sheet->setCellValue('A'.$row_index,$sn);
                    $active_sheet->setCellValue('B'.$row_index,$material_item->item_name);
                    $active_sheet->setCellValue('C'.$row_index,$material_item->unit()->symbol);
                    $active_sheet->setCellValue('D'.$row_index,$material_price->quantity);
                    $active_sheet->setCellValue('E'.$row_index,$material_price->price);
                    $active_sheet->setCellValue('F'.$row_index,'=(D'.$row_index.'*E'.$row_index.')');
                    $active_sheet->getStyle('A'.$row_index.':F'.$row_index)->applyFromArray($style['bordered']);
                    $row_index++;
                    $sn++;

                }
                $active_sheet->getStyle('F'.$row_index)->applyFromArray($style['bordered']);

            }


            $active_sheet->setCellValue('F'.$row_index,'=SUM(F'.$material_start.':F'.($row_index-1).')');
            $active_sheet->getStyle('F'.$row_index)->getFont()->setBold(true);
            $active_sheet->getStyle('E'.$row_index)->getFont()->setBold(true);





            //Define styles

            $active_sheet->setTitle(substr($tender->tender_name, 0, 28));


            //Start Initial column headers
            $active_sheet->getStyle('A4:F4')->applyFromArray($style['column_heading']);


            //load our new PHPExcel library
            $this->load->library('excel');
            $this->excel->setActiveSheetIndex(0);



            $active_sheet->getStyle('E5:F'.$row_index)->getNumberFormat()->setFormatCode('#,##0');

            $filename = $tender->tender_name . ' .xlsx'; //save our workbook as this file name
            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache

            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
            ob_end_clean();
            // We'll be outputting an excel file
            $objWriter->save('php://output');
        }
    }




}