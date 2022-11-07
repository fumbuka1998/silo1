<?php

require 'vendor/autoload.php';


class Budgets extends CI_Controller{

    public function __construct() {
        parent::__construct();
        check_login();
        $this->load->model('project');
    }

    public function budget_material_options(){
        $cost_center_level = $this->input->post('cost_center_level');
        $cost_center_id = $this->input->post('cost_center_id');
        $this->load->model('material_item');
        echo $this->material_item->budget_material_options($cost_center_level,$cost_center_id);
    }

    public function budget_expense_account_options(){
        $cost_center_level = $this->input->post('cost_center_level');
        $cost_center_id = $this->input->post('cost_center_id');
        $this->load->model('account');
        echo $this->account->budget_expense_account_options($cost_center_level,$cost_center_id);
    }

    public function budget_equipment_type_options()
    {
        $cost_center_level = $this->input->post('cost_center_level');
        $cost_center_id = $this->input->post('cost_center_id');
        $ownership = $this->input->post('ownership');
        $this->load->model('equipment_type');
        echo $this->equipment_type->budget_equipment_type_options($cost_center_level,$cost_center_id,$ownership);
    }

    public function budget_tool_type_options(){
        $cost_center_level = $this->input->post('cost_center_level');
        $cost_center_id = $this->input->post('cost_center_id');
        $this->load->model('tool_type');
        echo $this->tool_type->budget_tool_type_options($cost_center_level,$cost_center_id);
    }

    public function budget_job_position_options(){
        $cost_center_level = $this->input->post('cost_center_level');
        $cost_center_id = $this->input->post('cost_center_id');
        $rate_mode = $this->input->post('rate_mode');
        $this->load->model('job_position');
        echo $this->job_position->budget_job_position_options($cost_center_level,$cost_center_id,$rate_mode);
    }

    public function save_material_budget_item(){
        $this->load->model('material_budget');
        $item = new Material_budget();
        $edit = $item->load($this->input->post('item_id'));
        $item->material_item_id = $this->input->post('material_item_id');
        $item->project_id = $this->input->post('project_id');
        $item->task_id = $this->input->post('cost_center_id');
        $item->task_id = trim($item->task_id) != '' ? $item->task_id : null;
        $item->quantity = $this->input->post('quantity');
        $item->rate = $this->input->post('rate');
        $item->description = $this->input->post('description');
        $item->employee_id = $this->session->userdata('employee_id');
        if($item->save()){
            //Audit Trail Log will be here
        }
    }

    public function save_miscellaneous_budget_item(){
        $this->load->model('miscellaneous_budget');
        $item = new Miscellaneous_budget();
        $edit = $item->load($this->input->post('item_id'));
        $item->project_id = $this->input->post('project_id');
        $item->task_id = $this->input->post('cost_center_id');
        $item->task_id = trim($item->task_id) != '' ? $item->task_id : null;
        $item->expense_account_id = $this->input->post('expense_account_id');
        $item->amount = $this->input->post('amount');
        $item->description = $this->input->post('description');
        $item->employee_id = $this->session->userdata('employee_id');
        if($item->save()){
            $project = $item->project();
            $action = $edit ? 'Miscellaneous Budget Item Addition' : 'Miscellaneous Budget Item Update';
            $description = 'Miscellaneous Budget Item  was '.($edit ? 'Updated' : 'Added');
            $description .= 'Project: '.$project->project_name.', Budget Name: '.$item->budget_name;
        }
    }

     public function equipment_budget_list($project_id=0){
            $this->load->model('Equipment_budget');
            $posted_params = dataTable_post_params();
            echo $this->Equipment_budget->equipment_budget_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'],$project_id);
        }

    public function save_equipment_budget_item()
    {
        //cost_center_id,project_id,task_id,rate_mode,rate,duration,quantity,description
        $this->load->model('equipment_budget');
        $equipment_budget = new Equipment_budget();
        $edit = $equipment_budget->load($this->input->post('equipment_budget_id'));
        $equipment_budget->asset_item_id = $this->input->post('asset_item_id');
        $equipment_budget->project_id = $this->input->post('project_id');
        $equipment_budget->task_id = $this->input->post('cost_center_id');
        $equipment_budget->task_id = $equipment_budget->task_id != '' ? $equipment_budget->task_id : null;
        $equipment_budget->quantity = $this->input->post('quantity');
        $equipment_budget->rate_mode = $this->input->post('rate_mode');
        $equipment_budget->rate = $this->input->post('rate');
        $equipment_budget->duration = $this->input->post('duration');
        $equipment_budget->quantity = $this->input->post('quantity');
        $equipment_budget->description = $this->input->post('description');
        $equipment_budget->created_by = $this->session->userdata('employee_id');
        $equipment_budget->save();
    }

    public function delete_equipment_budget(){

        $this->load->model('Equipment_budget');
        $Equipment_budget=new Equipment_budget();
        if($Equipment_budget->load($this->input->post('equipment_budget_id'))){
            $Equipment_budget->delete();
        }

    }

    public function save_tools_budget_item(){
        $this->load->model('tools_budget');
        $item = new Tools_budget();
        $edit = $item->load($this->input->post('item_id'));
        $item->tool_type_id = $this->input->post('tool_type_id');
        $item->project_id = $this->input->post('project_id');
        $item->task_id = $this->input->post('cost_center_id');
        $item->task_id = trim($item->task_id) != '' ? $item->task_id : null;
        $item->quantity = $this->input->post('quantity');
        $item->rate = $this->input->post('rate');
        $item->description = $this->input->post('description');
        $item->employee_id = $this->session->userdata('employee_id');
        if($item->save()){
            //Call back statement will go here
        }
    }

    public function save_permanent_labour_budget_item(){
        $this->load->model('permanent_labour_budget');
        $item = new Permanent_labour_budget();
        $edit = $item->load($this->input->post('item_id'));
        $item->job_position_id = $this->input->post('job_position_id');
        $item->project_id = $this->input->post('project_id');
        $item->task_id = $this->input->post('cost_center_id');
        $item->task_id = trim($item->task_id) != '' ? $item->task_id : null;
        $item->allowance_rate = $this->input->post('allowance_rate');
        $item->salary_rate = $this->input->post('salary_rate');
        $item->rate_mode = $this->input->post('rate_mode');
        $item->duration = $this->input->post('duration');
        $item->no_of_staff = $this->input->post('no_of_staff');
        $item->description = $this->input->post('description');
        $item->employee_id = $this->session->userdata('employee_id');
        if($item->save()){
            //
        }
    }

    public function budget_casual_labour_type_options(){
        $cost_center_level = $this->input->post('cost_center_level');
        $cost_center_id = $this->input->post('cost_center_id');
        $rate_mode = $this->input->post('rate_mode');
        $this->load->model('casual_labour_type');
        echo $this->casual_labour_type->budget_casual_labour_type_options($cost_center_level,$cost_center_id,$rate_mode);
    }

    public function save_casual_labour_budget_item()
    {
        $this->load->model('casual_labour_budget');
        $item = new Casual_labour_budget();
        $edit = $item->load($this->input->post('item_id'));
        $item->casual_labour_type_id = $this->input->post('casual_labour_type_id');
        $item->no_of_workers = $this->input->post('no_of_workers');
        $item->duration = $this->input->post('duration');
        $item->rate = $this->input->post('rate');
        $item->rate_mode = $this->input->post('rate_mode');
        $item->project_id = $this->input->post('project_id');
        $item->task_id = $this->input->post('cost_center_id');
        $item->task_id = trim($item->task_id) != '' ? $item->task_id : null;
        $item->description = $this->input->post('description');
        $item->employee_id = $this->session->userdata('employee_id');
        if($item->save()){
            //
        }
    }

    public function budget_items_list($cost_center_level, $cost_center_id){
        $model = $this->input->post('budget_type').'_budget';
        $this->load->model($model);
        $posted_params = dataTable_post_params();
        echo $this->$model->budget_items_list($cost_center_level, $cost_center_id, $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function budget_item_delete(){
        $item_id = $this->input->post('item_id');
        $model = $this->input->post('budget_type').'_budget';
        $this->load->model($model);
        $class_name = ucfirst($model);
        $item = new $class_name();
        if($item->load($item_id)){
            $item->delete();
        }
    }

    public function download_excel_material_budget_template($id = 0){
        $project = new Project();
        if($project->load($id)) {

            $datavalidation =  \PhpOffice\PhpSpreadsheet\Cell\DataValidation::class;
            $style_fill = \PhpOffice\PhpSpreadsheet\Style\Fill::class;
            $style_border = \PhpOffice\PhpSpreadsheet\Style\Border::class;
            $style_protection = \PhpOffice\PhpSpreadsheet\Style\Protection::class;


            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            $spreadsheet->setActiveSheetIndex(0);


            //Define styles
            $style['splitter_column'] = [
                'fill' => [
                    'fillType' => $style_fill::FILL_SOLID,
                    'color' => ['argb' => '00000000']
                ]
            ];

            $style['activity_details'] = [
                'fill' => [
                    'fillType' => $style_fill::FILL_SOLID,
                    'color' => ['rgb' => '8497b0']
                ],
                'font' => [
                    'bold'  => true,
                    'size' => 15
                ]
            ];

            $style['task_details'] = [
                'fill' => [
                    'fillType' => $style_fill::FILL_SOLID,
                    'color' => ['argb' => '00d6dce4']
                ],
                'font' => [
                    'bold'  => true,
                    'size' => 13,
                    'italic' => true
                ]
            ];

            $style['column_heading'] = [
                'fill' => [
                    'fillType' => $style_fill::FILL_SOLID,
                    'color' => ['rgb' => 'bfbfbfbf'],
                ],
                'borders' => [
                    'allborders' => ['style' => $style_border::BORDER_THIN]
                ],
                'font' => [
                    'bold'  => true
                ]
            ];



            $active_sheet = $spreadsheet->getActiveSheet();
            $active_sheet->setTitle(substr($project->project_name, 0, 28));

            //Protect Sheet
            $active_sheet->getProtection()->setPassword('budgetepm@123');
            $active_sheet->getProtection()->setSheet(true);

            //Start Initial column headers
            $active_sheet->getStyle('A4:H4')->applyFromArray($style['column_heading']);

            //List materials in rows from row5
            $this->load->model(['material_item_category','material_item']);
            $categories = $this->material_item_category->get(0,0,' project_nature_id = '.$project->category_id.' OR project_nature_id IS NULL ');
            $categorized_material = [];
            foreach($categories as $category){
                $category_name = $category->category_name;
                $categorized_material[$category_name] = $category->material_items();
            }
            $uncategorized_material_items = $this->material_item->get(0, 0, ' category_id IS NULL');

            $material_index = 5;
            foreach ($categorized_material as $category_name => $material_items) {
                foreach($material_items as $material) {
                    $active_sheet->setCellValue('A' . $material_index, $material->{$material::DB_TABLE_PK});
                    $active_sheet->setCellValue('B' . $material_index, $category_name);
                    $active_sheet->setCellValue('C' . $material_index, $material->item_name);
                    $active_sheet->setCellValue('D' . $material_index, $material->unit()->symbol);
                    $material_index++;
                }
            }


            foreach ($uncategorized_material_items as $material) {
                $active_sheet->setCellValue('A' . $material_index, $material->{$material::DB_TABLE_PK});
                $active_sheet->setCellValue('B' . $material_index, 'NOT DEFINED');
                $active_sheet->setCellValue('C' . $material_index, $material->item_name);
                $active_sheet->setCellValue('D' . $material_index, $material->unit()->symbol);
                $material_index++;
            }

            //Tighten the material index, By removing the trailing row
            $material_index--;


            $active_sheet->getStyle('F2:F'.$material_index)->applyFromArray($style['splitter_column']);


            //General Budgeting
            $active_sheet->setCellValue('G2', $project->{$project::DB_TABLE_PK});
            $active_sheet->setCellValue('H2', 'General Budgeting');

            $active_sheet->setCellValue('G4', 'Quantity');
            $active_sheet->setCellValue('H4', 'Description');

            //Style Upper Cells For General Budgeting
            $active_sheet->getStyle('G2:H2')->applyFromArray($style['activity_details']);
            //Style Lower Cells For General Budgeting
            $active_sheet->getStyle('G3:H3')->applyFromArray($style['task_details']);

            //Unprotect Genereal Budgeting Fields
            $active_sheet->getStyle('G5:H'.$material_index)->getProtection()->setLocked($style_protection::PROTECTION_UNPROTECTED);


            $activity_separator_column = 'I';
            $active_sheet->getStyle($activity_separator_column.'2:'.$activity_separator_column.$material_index)->applyFromArray($style['splitter_column']);

            //Deal with project activities and tasks
            $activities = $project->activities();
            $activity_column = $task_column = $task_end_column = 'J';


            foreach ($activities as $activity){
                $activity_start_column = $activity_column;
                $active_sheet->setCellValue($activity_column.'2', $activity->{$activity::DB_TABLE_PK});
                $activity_column++;
                $active_sheet->setCellValue($activity_column.'2', $activity->activity_name);

                $tasks = $activity->tasks();

                foreach ($tasks as $task){
                    $task_start_column = $task_column;
                    $active_sheet->setCellValue($task_column.'3', $task->{$task::DB_TABLE_PK});
                    $active_sheet->setCellValue($task_column.'4', 'Quantity');
                    $task_column++;
                    $active_sheet->setCellValue($task_column.'3', $task->task_name);
                    $active_sheet->setCellValue($task_column.'4', 'Description');

                    //Style Task Details
                    $active_sheet->getStyle($task_start_column.'3:'.$task_column.'3')->applyFromArray($style['task_details']);
                    //Style Column Heading
                    $active_sheet->getStyle($task_start_column.'4:'.$task_column.'4')->applyFromArray($style['column_heading']);

                    $task_end_column = $task_column;

                    //Unprotect Editable cells
                    $active_sheet->getStyle($task_start_column.'5:'.$task_end_column.$material_index)->getProtection()->setLocked($style_protection::PROTECTION_UNPROTECTED);

                    $task_column++;
                    $activity_separator_column = $task_column;

                    //Style Task Separator
                    $active_sheet->getStyle($task_column.'3:'.$task_column.$material_index)->applyFromArray($style['splitter_column']);
                    $task_column++;
                }

                //Style Activity Details
                $active_sheet->getStyle($activity_start_column.'2:'.$task_end_column.'2')->applyFromArray($style['activity_details']);

                //Style Activity Separator
                $active_sheet->getStyle($activity_separator_column.'2')->applyFromArray($style['splitter_column']);
                $activity_column = $task_column;

            }

            for($col = 'A'; $col !== $activity_column; $col++) {
                $active_sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $active_sheet->setCellValue('A4', 'ID');
            $active_sheet->setCellValue('B4', 'Category');
            $active_sheet->setCellValue('C4', 'Material Name');
            $active_sheet->setCellValue('D4', 'Unit');
            $active_sheet->setCellValue('E4', 'Rate Per Unit');

            //Freeze the fixed panes
            $active_sheet->freezePane('F5');

            //Unprotect The rate column
            $active_sheet->getStyle('E5:E'.$material_index)->getProtection()->setLocked($style_protection::PROTECTION_UNPROTECTED);

            //Make rate with commas
            $active_sheet->getStyle('E5:E'.$material_index)->getNumberFormat()->setFormatCode('#,##0');

            $filename = $project->project_name.' Budget.xlsx'; //save our workbook as this file name

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);


            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache

            ob_end_clean();
            // We'll be outputting an excel file
            $writer->save('php://output');
        }
    }

    public function download_excel_material_budget_template_scrapped($id = 0){
        $project = new Project();
        if($project->load($id)) {

            //load our new PHPExcel library
            $this->load->library('excel');
            $this->excel->setActiveSheetIndex(0);

            //Define styles
            $style['splitter_column'] = [
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => '000000']
                ]
            ];

            $style['activity_details'] = [
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => '8497b0']
                ],
                'font' => [
                    'bold'  => true,
                    'size' => 15
                ]
            ];

            $style['task_details'] = [
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => 'd6dce4']
                ],
                'font' => [
                    'bold'  => true,
                    'size' => 13,
                    'italic' => true
                ]
            ];

            $style['column_heading'] = [
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => 'bfbfbf'],
                ],
                'borders' => [
                    'allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]
                ],
                'font' => [
                    'bold'  => true
                ]
            ];


            $active_sheet = $this->excel->getActiveSheet();
            $active_sheet->setTitle(substr($project->project_name, 0, 28));

            //Protect Sheet
            $active_sheet->getProtection()->setPassword('budgetepm@123');
            $active_sheet->getProtection()->setSheet(true);

            //Start Initial column headers
            $active_sheet->getStyle('A4:H4')->applyFromArray($style['column_heading']);

            //List materials in rows from row5
            $this->load->model(['material_item_category','material_item']);
            $categories = $this->material_item_category->get(0,0,' project_nature_id = '.$project->category_id.' OR project_nature_id IS NULL ');
            $categorized_material = [];
            foreach($categories as $category){
                $category_name = $category->category_name;
                $categorized_material[$category_name] = $category->material_items();
            }
            $uncategorized_material_items = $this->material_item->get(0, 0, ' category_id IS NULL');

            $material_index = 5;
            foreach ($categorized_material as $category_name => $material_items) {
                foreach($material_items as $material) {
                    $active_sheet->setCellValue('A' . $material_index, $material->{$material::DB_TABLE_PK});
                    $active_sheet->setCellValue('B' . $material_index, $category_name);
                    $active_sheet->setCellValue('C' . $material_index, $material->item_name);
                    $active_sheet->setCellValue('D' . $material_index, $material->unit()->symbol);
                    $material_index++;
                }
            }


            foreach ($uncategorized_material_items as $material) {
                $active_sheet->setCellValue('A' . $material_index, $material->{$material::DB_TABLE_PK});
                $active_sheet->setCellValue('B' . $material_index, 'NOT DEFINED');
                $active_sheet->setCellValue('C' . $material_index, $material->item_name);
                $active_sheet->setCellValue('D' . $material_index, $material->unit()->symbol);
                $material_index++;
            }

            //Tighten the material index, By removing the trailing row
            $material_index--;


            $active_sheet->getStyle('F2:F'.$material_index)->applyFromArray($style['splitter_column']);

            //load our new PHPExcel library
            $this->load->library('excel');
            $this->excel->setActiveSheetIndex(0);

            //General Budgeting
            $active_sheet->setCellValue('G2', $project->{$project::DB_TABLE_PK});
            $active_sheet->setCellValue('H2', 'General Budgeting');

            $active_sheet->setCellValue('G4', 'Quantity');
            $active_sheet->setCellValue('H4', 'Description');

            //Style Upper Cells For General Budgeting
            $active_sheet->getStyle('G2:H2')->applyFromArray($style['activity_details']);
            //Style Lower Cells For General Budgeting
            $active_sheet->getStyle('G3:H3')->applyFromArray($style['task_details']);

            //Unprotect Genereal Budgeting Fields
            $active_sheet->getStyle('G5:H'.$material_index)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);


            $activity_separator_column = 'I';
            $active_sheet->getStyle($activity_separator_column.'2:'.$activity_separator_column.$material_index)->applyFromArray($style['splitter_column']);

            //Deal with project activities and tasks
            $activities = $project->activities();
            $activity_column = $task_column = $task_end_column = 'J';


            foreach ($activities as $activity){
                $activity_start_column = $activity_column;
                $active_sheet->setCellValue($activity_column.'2', $activity->{$activity::DB_TABLE_PK});
                $activity_column++;
                $active_sheet->setCellValue($activity_column.'2', $activity->activity_name);

                $tasks = $activity->tasks();

                foreach ($tasks as $task){
                    $task_start_column = $task_column;
                    $active_sheet->setCellValue($task_column.'3', $task->{$task::DB_TABLE_PK});
                    $active_sheet->setCellValue($task_column.'4', 'Quantity');
                    $task_column++;
                    $active_sheet->setCellValue($task_column.'3', $task->task_name);
                    $active_sheet->setCellValue($task_column.'4', 'Description');

                    //Style Task Details
                    $active_sheet->getStyle($task_start_column.'3:'.$task_column.'3')->applyFromArray($style['task_details']);
                    //Style Column Heading
                    $active_sheet->getStyle($task_start_column.'4:'.$task_column.'4')->applyFromArray($style['column_heading']);

                    $task_end_column = $task_column;

                    //Unprotect Editable cells
                    $active_sheet->getStyle($task_start_column.'5:'.$task_end_column.$material_index)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

                    $task_column++;
                    $activity_separator_column = $task_column;

                    //Style Task Separator
                    $active_sheet->getStyle($task_column.'3:'.$task_column.$material_index)->applyFromArray($style['splitter_column']);
                    $task_column++;
                }

                //Style Activity Details
                $active_sheet->getStyle($activity_start_column.'2:'.$task_end_column.'2')->applyFromArray($style['activity_details']);

                //Style Activity Separator
                $active_sheet->getStyle($activity_separator_column.'2')->applyFromArray($style['splitter_column']);
                $activity_column = $task_column;

            }

            for($col = 'A'; $col !== $activity_column; $col++) {
                $active_sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $active_sheet->setCellValue('A4', 'ID');
            $active_sheet->setCellValue('B4', 'Category');
            $active_sheet->setCellValue('C4', 'Material Name');
            $active_sheet->setCellValue('D4', 'Unit');
            $active_sheet->setCellValue('E4', 'Rate Per Unit');

            //Freeze the fixed panes
            $active_sheet->freezePane('F5');

            //Unprotect The rate column
            $active_sheet->getStyle('E5:E'.$material_index)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

            //Make rate with commas
            $active_sheet->getStyle('E5:E'.$material_index)->getNumberFormat()->setFormatCode('#,##0');

            $filename = $project->project_name.' Budget.xlsx'; //save our workbook as this file name
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

    public function download_excel_material_budget_template_bk($id = 0){
        $this->load->model(['activity','task','measurement_unit','material_item','asset_item']);
        $project = new Project();
        if($project->load($id)) {

            //load our new PHPExcel library
            $this->load->library('excel');
            $this->excel->setActiveSheetIndex(0);

           //Define styles
//            $style['splitter_column'] = [
//                'fill' => [
//                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                    'color' => ['rgb' => '000000']
//                ]
//            ];

            $style['activity_row'] = [
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => 'AF002A'],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '2f2f2f'],
                ]
            ];

            $style['header_row'] = [
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => 'E6FFE6'],
                ],
                'font' => [
                    'color' => ['rgb' => '2f2f2f']
                ]
            ];

            $style['task_row'] = [
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => 'F0F8FF'],
                ],
                'font' => [
                    'color' => ['rgb' => '2f2f2f']
                ]
            ];

            $style['activity_details'] = [
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => '8497b0']
                ],
                'font' => [
                    'bold'  => true,
                    'size' => 15
                ]
            ];

            $style['task_details'] = [
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => 'd6dce4']
                ],
                'font' => [
                    'bold'  => true,
                    'size' => 13,
                    'italic' => true
                ]
            ];

            $style['column_heading'] = [
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => 'bfbfbf'],
                ],
                'borders' => [
                    'allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]
                ],
                'font' => [
                    'bold'  => true
                ]
            ];


            $active_sheet = $this->excel->getActiveSheet();
            $active_sheet->setTitle(substr($project->project_name, 0, 28));

            //Preparing measurement unit
            $uom_columns = array('I','N','S','X','AC');
            foreach($uom_columns as $uom_column) {
                $uom_dropdown = $this->measurement_unit->excel_dropdown_list();
                for ($index = 10; $index <= 1000; $index++) {
                    $objValidation = $active_sheet->getCell($uom_column . $index)->getDataValidation();
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
            }

            //Preparing material dropdown options
            $matl_items_dropdown = $this->material_item->excel_dropdown_list();
            for ($index = 10; $index <= 1000; $index++) {
                $objValidation = $active_sheet->getCell('G' . $index)->getDataValidation();
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
                $objValidation->setFormula1('"' . $matl_items_dropdown . '"');
            }

            //Preparing equipment dropdown options
            $asset_item_dropdown = $this->asset_item->excel_dropdown_list();
            for ($index = 10; $index <= 1000; $index++) {
                $objValidation = $active_sheet->getCell('L' . $index)->getDataValidation();
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
                $objValidation->setFormula1('"' . $asset_item_dropdown . '"');
            }

            $amt_summation_columns = array('K','P','U','Z','AE');
            foreach($amt_summation_columns as $amt_summation_column){
                for ($index = 10; $index <= 1000; $index++) {
                    $active_sheet->setCellValue($amt_summation_column . $index, '='.chr(ord($amt_summation_column)-3).$index.'*'.chr(ord($amt_summation_column)-1).$index);
                }
            }

            //Protect Sheet
            $active_sheet->getProtection()->setPassword('budgetepm@123');
            $active_sheet->getProtection()->setSheet(true);

            for ($index = 10; $index <= 1000; $index++) {
                $active_sheet->getStyle('G'.$index.':AE'.$index)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
            }

            //Start Initial column headers
            $active_sheet->getStyle('A7:AE7')->applyFromArray($style['column_heading']);
            $active_sheet->getStyle('A8:AE8')->applyFromArray($style['column_heading']);
            $active_sheet->mergeCells('B7:F7');
            $active_sheet->mergeCells('G7:K7');
            $active_sheet->mergeCells('L7:P7');
            $active_sheet->mergeCells('Q7:U7');
            $active_sheet->mergeCells('V7:Z7');
            $active_sheet->mergeCells('AA7:AE7');
            $active_sheet->freezePane('G9');
            $active_sheet->getColumnDimension('A')->setWidth(3);
            $active_sheet->getColumnDimension('B')->setWidth(10);
            $active_sheet->getColumnDimension('C')->setWidth(20);
            $active_sheet->getColumnDimension('D')->setWidth(30);
            $active_sheet->getColumnDimension('E')->setWidth(30);
            $active_sheet->getColumnDimension('G')->setWidth(20);
            $active_sheet->getColumnDimension('L')->setWidth(20);
            $active_sheet->getStyle('C9:C1000')->getAlignment()->setWrapText(true);
            $active_sheet->getStyle('D9:D1000')->getAlignment()->setWrapText(true);
            $active_sheet->getStyle('E9:E1000')->getAlignment()->setWrapText(true);

            for ($row_index = 1; $row_index <= 6; $row_index++) {
                $active_sheet->getStyle('A'.$row_index.':AE'.$row_index)->applyFromArray($style['header_row']);
                $active_sheet->mergeCells('B'.$row_index.':F'.$row_index);
            }
            $column = 0;
            $first_header_items = array('S/N','Description','Material Budget','Equipment Budget','Labour Budget','Miscellaneous Budget','Sub Contract Budget');
            foreach($first_header_items as $header_item){
                $active_sheet->setCellValueByColumnAndRow($column,7,$header_item);
                if($column == 0){
                    $column++;
                } else if($column == 1){
                    $active_sheet->setCellValueByColumnAndRow($column,2,$project->project_name.'Budget');
                    $active_sheet->setCellValueByColumnAndRow($column,3,'Project ID :'.$project->generated_project_id());
                    $active_sheet->setCellValueByColumnAndRow($column,4,'Date :'. set_date(date('Y-m-d')));
                    $second_header_items = array('ID','Activity','Task','Sub Task','Location');
                    foreach($second_header_items as $header2_item){
                        $active_sheet->setCellValueByColumnAndRow($column,8,$header2_item);
                        $column++;
                    }
                } else {
                    $third_header_items = array('Item','Quantity','UOM','Rate','Amount');
                    foreach($third_header_items as $header3_item){
                        $active_sheet->setCellValueByColumnAndRow($column,8,$header3_item);
                        $column++;
                    }
                }
            }

            $activities = $this->activity->get(0,0,['project_id'=>$id]);
            if (!empty($activities)) {
                $sn = 0;
                $index = 9;
                foreach ($activities as $activity) {
                    $sn++;
                    $active_sheet->getStyle('A' . $index . ':AE' . $index)->applyFromArray($style['activity_row']);
                    $active_sheet->setCellValue('A' . $index, $sn);
                    $active_sheet->setCellValue('B' . $index, $activity->activity_id);
                    $active_sheet->setCellValue('C' . $index, $activity->activity_name);
                    $active_sheet->setCellValue('D' . $index, '');
                    $active_sheet->setCellValue('E' . $index, '');
                    $active_sheet->setCellValue('F' . $index, '');
                    $index++;

                    $tasks = $this->task->get(0, 0, ['activity_id' => $activity->{$activity::DB_TABLE_PK}]);
                    foreach ($tasks as $task) {
//                        $active_sheet->getStyle('A' . $index . ':AE' . $index)->applyFromArray($style['task_row']);
                        $active_sheet->setCellValue('D' . $index, $task->task_name);
                        $active_sheet->setCellValue('E' . $index, 'Sub task if there is one');
                        $active_sheet->setCellValue('F' . $index, 'Location');
                        $index++;
                    }
                }
            }

            $filename = $project->project_name.' Budget.xlsx'; //save our workbook as this file name
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

    public function upload_material_budget_excel(){
        $file = $_FILES['file']['tmp_name'];

        $project_id = intval($this->input->post('project_id'));

        $this->load->model(['task','material_budget']);
        //read file from path
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $coordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::class;

        $active_sheet = $objPHPExcel->getActiveSheet();
        $hash = $active_sheet->getProtection()->getPassword(); // returns a hash
        $valid = ($hash === \PhpOffice\PhpSpreadsheet\Shared\PasswordHasher::hashPassword('budgetepm@123'));

        $project_cost_center_id = $active_sheet->getCell("G2")->getFormattedValue();

        $project = new Project();

        if($valid && $project_cost_center_id == $project_id && $project->load($project_id)) {
            $sheet_dimension = $active_sheet->getHighestRowAndColumn();

            //General Material Budgeting

            for ($row_index = 5; $row_index <= $sheet_dimension['row']; $row_index++) {
                $quantity = floatval($active_sheet->getCell("G" . $row_index)->getFormattedValue());
                if ($quantity > 0) {
                    $material_budget = new Material_budget();
                    $material_budget->project_id = $project_id;
                    $material_budget->material_item_id = $active_sheet->getCell("A" . $row_index)->getFormattedValue();
                    $material_budget->load($project->material_budget_item_id($material_budget->material_item_id));
                    $material_budget->quantity = $quantity;
                    $material_budget->rate = remove_commas($active_sheet->getCell("E" . $row_index)->getFormattedValue());
                    $material_budget->description = $active_sheet->getCell("H" . $row_index)->getFormattedValue();
                    $material_budget->employee_id = $this->session->userdata('employee_id');
                    $material_budget->save();
                }
            }

            //Task wise Material Budgeting

            $this->load->model('task');
            $MAX_COL_INDEX = $coordinate::columnIndexFromString($sheet_dimension['column']);



            for($index = $coordinate::columnIndexFromString('J'); $index < $MAX_COL_INDEX; $index = $index+3){
                $col = $coordinate::stringFromColumnIndex($index);
                $description_column = $coordinate::stringFromColumnIndex($index+1);
                $task_id = $active_sheet->getCell($col. '3')->getFormattedValue();
                $task = new Task();
                $task->load($task_id);

                for ($row_index = 5; $row_index <= $sheet_dimension['row']; $row_index++) {
                   $quantity = floatval($active_sheet->getCell($col . $row_index)->getFormattedValue());


                    if ($quantity > 0) {
                        $material_budget = new Material_budget();
                        $material_budget->project_id = $project_id;
                        $material_budget->task_id = $task_id;
                        $material_budget->material_item_id = $active_sheet->getCell("A" . $row_index)->getFormattedValue();
                        $material_budget->load($task->material_budget_item_id($material_budget->material_item_id));
                        $material_budget->quantity = $quantity;
                        $material_budget->rate = remove_commas($active_sheet->getCell("E" . $row_index)->getFormattedValue());
                        $material_budget->description = $active_sheet->getCell($description_column . $row_index)->getFormattedValue();
                        $material_budget->employee_id = $this->session->userdata('employee_id');
                        $material_budget->save();
                    }
                }
            }
        }
    }

    public function upload_material_budget_excel_bk(){
        $file = $_FILES['file']['tmp_name'];

        $project_id = intval($this->input->post('project_id'));

        $this->load->library('excel');
        $this->load->model(['task','material_budget']);
        //read file from path
        $objPHPExcel = PHPExcel_IOFactory::load($file);

        $active_sheet = $objPHPExcel->getActiveSheet();
        $hash = $active_sheet->getProtection()->getPassword(); // returns a hash
        echo $valid = ($hash === PHPExcel_Shared_PasswordHasher::hashPassword('budgetepm@123'));

        $project_cost_center_id = $active_sheet->getCell("G2")->getFormattedValue();

        $project = new Project();

        if($valid && $project_cost_center_id == $project_id && $project->load($project_id)) {
            $sheet_dimension = $active_sheet->getHighestRowAndColumn();

            //General Material Budgeting

            for ($row_index = 5; $row_index <= $sheet_dimension['row']; $row_index++) {
                $quantity = floatval($active_sheet->getCell("G" . $row_index)->getFormattedValue());
                if ($quantity > 0) {
                    $material_budget = new Material_budget();
                    $material_budget->project_id = $project_id;
                    $material_budget->material_item_id = $active_sheet->getCell("A" . $row_index)->getFormattedValue();
                    $material_budget->load($project->material_budget_item_id($material_budget->material_item_id));
                    $material_budget->quantity = $quantity;
                    $material_budget->rate = remove_commas($active_sheet->getCell("E" . $row_index)->getFormattedValue());
                    $material_budget->description = $active_sheet->getCell("H" . $row_index)->getFormattedValue();
                    $material_budget->employee_id = $this->session->userdata('employee_id');
                    $material_budget->save();
                }
            }

            //Task wise Material Budgeting

            $this->load->model('task');
            $MAX_COL_INDEX = PHPExcel_Cell::columnIndexFromString($sheet_dimension['column']);

            for($index = PHPExcel_Cell::columnIndexFromString('I'); $index < $MAX_COL_INDEX; $index = $index+3){
                $col = PHPExcel_Cell::stringFromColumnIndex($index);
                $description_column = PHPExcel_Cell::stringFromColumnIndex($index+1);
                $task_id = $active_sheet->getCell($col. '3')->getFormattedValue();
                $task = new Task();
                $task->load($task_id);

                for ($row_index = 5; $row_index <= $sheet_dimension['row']; $row_index++) {
                    $quantity = floatval($active_sheet->getCell($col . $row_index)->getFormattedValue());

                    if ($quantity > 0) {
                        $material_budget = new Material_budget();
                        $material_budget->project_id = $project_id;
                        $material_budget->task_id = $task_id;
                        $material_budget->material_item_id = $active_sheet->getCell("A" . $row_index)->getFormattedValue();
                        $material_budget->load($task->material_budget_item_id($material_budget->material_item_id));
                        $material_budget->quantity = $quantity;
                        $material_budget->rate = remove_commas($active_sheet->getCell("E" . $row_index)->getFormattedValue());
                        $material_budget->description = $active_sheet->getCell($description_column . $row_index)->getFormattedValue();
                        $material_budget->employee_id = $this->session->userdata('employee_id');
                        $material_budget->save();
                    }
                }
            }
        }
    }

    public function save_sub_contract_budget(){
        $this->load->model('sub_contract_budget');
        $item = new Sub_contract_budget();
        $edit = $item->load($this->input->post('budget_item_id'));
        $item->project_id = $this->input->post('project_id');
        $item->task_id = $this->input->post('cost_center_id');
        $item->task_id = trim($item->task_id) != '' ? $item->task_id : null;
        $item->amount = $this->input->post('amount');
        $item->description = $this->input->post('description');
        $item->created_by= $this->session->userdata('employee_id');
        $item->save();

    }
}

