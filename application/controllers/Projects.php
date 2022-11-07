<?php

require 'vendor/autoload.php';

class Projects extends CI_Controller{

    public function __construct() {
        parent::__construct();
        check_login();
        $this->load->model('project');
     }

    public function index(){
        $data['title'] = 'Projects';
        //$data['number_of_projects'] = $this->project->count_rows();
        $this->load->model('project_category');
        $data['project_categories'] = $this->project_category->get();
        $this->load->view('projects/index',$data);
    }

    public function projects_list($category_id = null){
        if(!$this->session->userdata('has_project')){
            check_permission('Projects',true);
        }
        $limit = $this->input->post('length');
        $category_id = $category_id != '' ? $category_id : null;
        if($limit != '') {
            $posted_params = dataTable_post_params();
            echo $this->project->projects_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'],$category_id);
        } else {
            $this->load->model(['project_category','stakeholder']);
            $data['title'] = 'Projects List';
            $data['project'] = new Project();
            $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
            $data['category_options'] = $this->project_category->category_options();
            $data['project_category_id'] = $category_id;

            if(!is_null($category_id)) {
                $data['category_options'] = [
                    $category_id => $data['category_options'][$category_id]
                ];
                $data['project_category_name'] = $data['category_options'][$category_id];
            }
            $this->load->view('projects/projects_list', $data);
        }
    }

    public function save($id = 0){
        $project = new Project();
        $edit = $project->load($id);
        $project->project_name = $this->input->post('project_name');
        $project->category_id = $this->input->post('category_id');
        $project->description = $this->input->post('description');
        $project->currency_id = 1;
        $project->site_location = $this->input->post('site_location');
        $project->reference_number = $this->input->post('reference_number');
        $project->stakeholder_id = $this->input->post('stakeholder_id') != '' ? $this->input->post('stakeholder_id') : null;
        $project->start_date = $this->input->post('start_date') != '' ? $this->input->post('start_date') : null;
        $project->end_date = $this->input->post('end_date') != '' ? $this->input->post('end_date') : null;
        $project->created_by = $this->session->userdata('employee_id');

        if($project->save()){
            if(!$edit){
                $this->load->model(['inventory_location','account','project_account']);
                $location = new Inventory_location();
                $location->location_name = $project->project_name;
                $location->project_id = $project->{$project::DB_TABLE_PK};
                $location->description = 'Site Location/Store For '.$project->project_name;
                if($location->save()){
                    $this->load->model('sub_location');
                    $sub_location = new Sub_location();
                    $sub_location->sub_location_name = 'Default Sub-location';
                    $sub_location->location_id = $location->{$location::DB_TABLE_PK};
                    $sub_location->description = 'This is a default Sub-location created during location creation';
                    $sub_location->status = 'ACTIVE';
                    $sub_location->save();


                    //Save Account For Project
                    $account = new Account();
                    $account->account_name = $project->project_name.' Cash Book';
                    $account->account_group_id = 19;
                    $account->currency_id = $project->currency_id;
                    $account->description = 'Cash book account for '.$project->project_name;
                    $account->opening_balance = 0;
                    if($account->save()){
                        $project_account = new Project_account();
                        $project_account->project_id = $project->{$project::DB_TABLE_PK};
                        $project_account->account_id = $account->{$account::DB_TABLE_PK};
                        $project_account->save();
                    }

                }
            }
            $description = $project->project_name.' project was ';
            $description .= $edit ? 'updated' : 'registered';
            $action = $edit ? 'Project Update' : 'Project Registration';
            system_log($action,$description,$project->{$project::DB_TABLE_PK});
            redirect(base_url('projects/profile/'.$project->{$project::DB_TABLE_PK}));
        } else {
            redirect(base_url());
        }
    }

    public function profile($project_id = 0){
        $this->load->model([
            'Project',
            'project_category',
            'activity',
            'stakeholder',
            'job_position',
            'project_team_member'
            ,'asset_group',
            'Equipment_budget',
            'Owned_equipment_cost',
            'material_item_category',
            'asset',
            'asset_item',
            'approval_module',
            'stakeholder',
            'hired_asset'
        ]);

        $approval_modules = $this->approval_module->get(1,0,' module_name LIKE "%Project%" ');
        $approval_module = array_shift($approval_modules);
        $data['forward_to_dropdown'] = $approval_module->forwarding_to_employee_options();
        $project = new Project();
        if($project->load($project_id)) {
            if(!$project->allowed_access()){
                redirect(base_url());
            }
            $data['title'] = $project->project_name;
            $data['project_objects_dropdown_options'] = $project->project_objects_dropdown_options();
            $data['project'] = $project;
            $location = $project->location();
            $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
            $data['category_options'] = $this->project_category->category_options();
            $data['cost_center_type_options'] = ['task' => 'Task'];
            $data['employee_options'] = employee_options();
            $data['cost_center_options'] = $project->cost_center_options();
            $data['plan_cost_center_options'] = $project->plan_cost_center_options();
            $data['job_position_options'] = $this->job_position->job_position_options();
            $data['include_gantt_chart'] = true;
            $data['team_member_options'] = employee_options();
            $data['summary_cost_center_options'] = ['Overall' => ['project_overall' => 'Project Overall']] + $data['cost_center_options'];
            $data['store_sub_location_options'] = $location->sub_location_options();
            $data['equipment_sub_location_options'] = $location->equipment_sub_location_options();
            $data['location_options'] = $location->dropdown_options();
            $data['asset_group_options'] = $this->asset_group->dropdown_options();
            $data['asset_item_options'] = $this->asset_item->dropdown_options();
            $data['material_item_category_options'] = $this->material_item_category->dropdown_options();
            $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
            $data['asset_options'] = array();
            $data['location_asset_options'] = $this->asset->location_asset_options('location', null, $project_id) + $this->hired_asset->dropdown_options($project_id);
            $data['material_options'] = material_item_dropdown_options(null,true);
            $data['sub_contract_options'] = $this->stakeholder->sub_contract_options($project->{$project::DB_TABLE_PK});
            $data['currency_options'] = currency_dropdown_options();
            $data['expense_debit_account_options'] = account_dropdown_options(['DIRECT EXPENSES','INDIRECT EXPENSES']);
            $data['credit_account_options'] = $project->project_account_options();
            $data['project_accounts'] = $project->project_accounts();
            $data['project_status'] = $project->status();
            $data['activities_options'] = $this->activity->dropdown_options($project->{$project::DB_TABLE_PK});
            $this->load->view('projects/profile',$data);
        } else {
            redirect(base_url());
        }
    }

    public function load_project_material_options(){
        $project = new Project();
        $project->load($this->input->post('project_id'));
        echo stringfy_dropdown_options(material_item_dropdown_options($project->category_id));
    }

    public function load_assets_options(){
        $this->load->model('asset_register/Asset_group');
       $Asset_group= new Asset_group();
       if($this->input->post('asset_group_id')!='') {
           $Asset_group->load($this->input->post('asset_group_id'));
           $return['asset_dropdown_options'] = $Asset_group->asset_dropdown_options();
       }else{

           $return['asset_dropdown_options']='';
       }
        echo json_encode($return);
    }

    public function load_equipments_options(){
        $this->load->model('asset_register/Asset_group');
       $Asset_group= new Asset_group();
       if($this->input->post('asset_group_id')!='') {
           $Asset_group->load($this->input->post('asset_group_id'));
           $return['asset_dropdown_options'] = $Asset_group->equipment_dropdown_options();
       }else{

           $return['asset_dropdown_options']='';
       }
        echo json_encode($return);
    }

    public function project_summary(){
        $project = new Project();
        $project->load($this->input->post('project_id'));
        $this->load->view('projects/project_details/project_summary',['project' => $project]);
    }

    public function project_team_member_employees_options(){
        $this->load->model('employee');
        echo $this->employee->project_team_member_employees_options($this->input->post('project_id'));
    }

    public function save_project_team_member(){
        $this->load->model('project_team_member');
        $project_team_member = new Project_team_member();
        $edit = $project_team_member->load($this->input->post('member_id'));
        $project_team_member->assignor_id = $this->session->userdata('employee_id');
        $project_team_member->project_id = $this->input->post('project_id');
        $project_team_member->manager_access = $this->input->post('manager_access');
        $project_team_member->employee_id = $this->input->post('employee_id');
        $project_team_member->date_assigned = $this->input->post('date_assigned');
        $project_team_member->job_position_id = $this->input->post('job_position_id');
        $project_team_member->remarks = $this->input->post('remarks');
        if($project_team_member->save()){
            $employee = $project_team_member->employee();
            $project = $project_team_member->project();
            $description = 'Project Team Member Assignment for '.$project->project_name.' with Employee '.$employee->full_name().' was ';
            $action = $edit ? 'Project Team Member Update' : 'Project Team Member Assignment';
            $description .= $edit ? 'updated' : 'made';
            system_log($action,$description,$project_team_member->project_id);
        }
    }

    public function delete_project_team_member(){
        $this->load->model('project_team_member');
        $member = new Project_team_member();
        if($member->load($this->input->post('member_id'))){
            $employee = $member->employee();
            $action = 'Project Team Member Delete';
            $description = $employee->full_name().' was removed from the list of team members in '.$member->project()->project_name;
            system_log($action,$description,$member->project_id);
            $member->delete();
        }
    }

    public function project_team_members(){
        $this->load->model('project_team_member');
        if(!$this->session->userdata('has_project')) {
            check_permission('Projects', true);
        }
        $project_id = $this->input->post('project_id');
        $posted_params = dataTable_post_params();
        echo $this->project_team_member->team_members_list($project_id,$posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function save_activity(){
        $this->load->model('activity');
        $activity = new Activity();
        $edit = $activity->load($this->input->post('activity_id'));
        $activity->activity_name = $this->input->post('activity_name');
        $activity->project_id = $this->input->post('project_id');
        $activity->description = $this->input->post('description');
        if($activity->save()){
            $project = $activity->project();
            $description = 'Activity '.$activity->activity_name.' for '.$project->project_name.' was ';
            $action = $edit ? 'Activity Update' : 'Activity Registration';
            $description .= $edit ? 'updated' : 'registered';
            system_log($action,$description,$project->{$project::DB_TABLE_PK});
        }
    }

    public function project_activities_list(){
        $this->load->model('task');
        $project = new Project();
        $project->load($this->input->post('project_id'));
        $data['project'] = $project;
        $data['activities'] = $project->activities($this->input->post('keyword'));
        $data['project_status'] = $project->status();
        $this->load->view('projects/activities/activities_list',$data);
    }

    public function activity_summary(){
        $this->load->model('activity');
        $activity = new Activity();
        $activity->load($this->input->post('activity_id'));
        $this->load->view('projects/activities/activity_summary_tab',['activity' => $activity]);
    }

    public function delete_activity(){
        $this->load->model('activity');
        $activity = new Activity();
        if($activity->load($this->input->post('activity_id'))){
            $project = $activity->project();
            $description = 'Activity '.$activity->activity_name.' for project '.$project->project_name.' was deleted';
            system_log('Activity Delete',$description,$project->{$project::DB_TABLE_PK});
            $activity->delete();
        }
    }

    public function project_store(){
        $project = new Project();
        if($project->load($this->input->post('project_id'))){
            $this->load->model(['currency','asset','asset_item','stakeholder','asset_group','material_item_category']);
            $data['project'] = $project;
            $data['currency_options'] = $this->currency->dropdown_options();
            $data['project_options'] = projects_dropdown_options();
            $data['asset_items_options'] = $this->asset_item->dropdown_options();
            $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
            $data['location'] = $project->location();
            $data['employee_options'] = employee_options();
            $data['asset_group_options'] = $this->asset_group->dropdown_options();
            $data['material_item_category_options'] = $this->material_item_category->dropdown_options();
            $data['asset_stock_options'] = $this->asset->location_asset_options('location',$data['location']->{$data['location']::DB_TABLE_PK});
            $data['sub_location_options'] = $data['location']->sub_location_options();
            $this->load->view('projects/store/project_store_tab',$data);
        }
    }

    public function download_activities_excel($project_id)
    {
        $project = new Project();
        if ($project->load($project_id)) {
            $object = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $fill = \PhpOffice\PhpSpreadsheet\Style\Fill::class;
            $alignment = \PhpOffice\PhpSpreadsheet\Style\Alignment::class;
            $datavalidation = \PhpOffice\PhpSpreadsheet\Cell\DataValidation::class;

            $object->setActiveSheetIndex(0);
            $object->getProperties()->setCreator($this->session->userdata('employee_name'));
            $object->getProperties()->setTitle(substr(''.$project->project_name.' Project Activities',0,20));
            $object->getActiveSheet()->setTitle(substr(''.$project->project_name.' Project Activities',0,20));
            $object->getActiveSheet()->setPrintGridlines(TRUE);

            $active_sheet = $object->getActiveSheet();
            $active_sheet->setTitle(substr(''.$project->project_name.' Project Activities',0,20));

            $this->load->model(['activity', 'task', 'measurement_unit']);
            $uom_dropdown = $this->measurement_unit->excel_dropdown_list();
            $activities = $this->activity->get(0, 0, ['project_id' => $project_id]);

            $active_sheet->getStyle('A1:H202')->applyFromArray(
                [
                    'font' => [
                        'name' => 'Verdana',
                        'color' => ['rgb' => '000000'],
                        'size' => 11
                    ],

                    'borders' => [
                        'allborders' => [
                            'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'AAAAAA']
                        ]
                    ]
                ]
            );

            $style['activity_row'] = [
                'fill' => [
                    'fillType' => $fill::FILL_SOLID,
                    'color' => ['rgb' => 'bfbfbf'],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '2f2f2f'],
                ]
            ];

            $style['rate_column'] = [
                'alignment' => [
                    'horizontal' => $alignment::HORIZONTAL_RIGHT,
                ]
            ];

            $style['column_heading'] = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '2f2f2f'],
                ],

                'fill' => [
                    'fillType' => $fill::FILL_SOLID,
                    'color' => ['rgb' => 'accbe1'],
                ],
                'alignment' => [
                    'horizontal' => $alignment::HORIZONTAL_CENTER,
                    'vertical' => $alignment::VERTICAL_CENTER
                ]
            ];

            $active_sheet->getStyle('A1:I1')->applyFromArray($style['column_heading']);
            $active_sheet->getStyle('E3:E202')->applyFromArray($style['rate_column']);

            for ($col_index = 'A'; $col_index !== 'J'; $col_index++) {
                $active_sheet->getColumnDimension($col_index)->setAutoSize(true);
            }

            $active_sheet->setCellValue('A1', 'ID');
            $active_sheet->setCellValue('B1', 'WORK DESCRIPTION');
            $active_sheet->setCellValue('C1', 'Measurement Unit');
            $active_sheet->setCellValue('D1', 'Quantity');
            $active_sheet->setCellValue('E1', 'Rate');
            $active_sheet->setCellValue('F1', 'Amount');
            $active_sheet->setCellValue('G1', 'Start Date');
            $active_sheet->setCellValue('H1', 'End Date');
            $active_sheet->setCellValue('I1', 'Description');

            for ($index = 2; $index <= 202; $index++) {
                $objValidation = $active_sheet->getCell('C' . $index)->getDataValidation();
                $objValidation->setType($datavalidation::TYPE_LIST);
                $objValidation->setErrorStyle($datavalidation::STYLE_INFORMATION);
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


            if (!empty($activities)) {
                $index = 2;
                foreach ($activities as $activity) {
                    $active_sheet->getStyle('A' . $index . ':I' . $index)->applyFromArray($style['activity_row']);
                    $active_sheet->setCellValue('A' . $index, $activity->activity_id);
                    $active_sheet->setCellValue('B' . $index, $activity->activity_name);
                    $active_sheet->setCellValue('C' . $index, '');
                    $active_sheet->setCellValue('D' . $index, '');
                    $active_sheet->setCellValue('E' . $index, '');
                    $active_sheet->setCellValue('F' . $index, '');
                    $active_sheet->setCellValue('G' . $index, '');
                    $active_sheet->setCellValue('H' . $index, '');
                    $active_sheet->setCellValue('I' . $index, $activity->description);
                    $index++;

                    $tasks = $this->task->get(0, 0, ['activity_id' => $activity->{$activity::DB_TABLE_PK}]);
                    foreach ($tasks as $task) {
                        $active_sheet->setCellValue('A' . $index, $task->task_id);
                        $active_sheet->setCellValue('B' . $index, $task->task_name);
                        $active_sheet->setCellValue('C' . $index, $task->measurement_unit()->symbol);
                        $active_sheet->setCellValue('D' . $index, $task->quantity);
                        $active_sheet->setCellValue('E' . $index, $task->rate);
                        $active_sheet->setCellValue('F' . $index, '=D'.$index.'*E'.$index);
                        $active_sheet->setCellValue('G' . $index, $task->start_date);
                        $active_sheet->setCellValue('H' . $index, $task->end_date);
                        $active_sheet->setCellValue('I' . $index, $task->description);
                        $index++;
                    }
                }
            } else {
                $k = 1;
                for ($n = 2; $n <= 5; $n++) {
                    $active_sheet->getStyle('A' . $n . ':I' . $n)->applyFromArray($style['activity_row']);
                    $active_sheet->setCellValue('A' . $n, '');
                    $active_sheet->setCellValue('B' . $n, 'Sample Activity Name' . $k);
                    $active_sheet->setCellValue('C' . $n, '');
                    $active_sheet->setCellValue('D' . $n, '');
                    $active_sheet->setCellValue('E' . $n, '');
                    $active_sheet->setCellValue('F' . $n, '');
                    $active_sheet->setCellValue('G' . $n, '');
                    $active_sheet->setCellValue('H' . $n, '');
                    $active_sheet->setCellValue('I' . $n, '');
                    $n++;
                    if ($k == 3) {
                        break;
                    }
                    if ($k == 1) {
                        $j = 1;
                        for ($i = $n; $i <= 7; $i++) {
                            $active_sheet->setCellValue('A' . $i, '');
                            $active_sheet->setCellValue('B' . $i, 'Sample Task Name' . $j);
                            $active_sheet->setCellValue('C' . $i, '');
                            $active_sheet->setCellValue('D' . $i, '');
                            $active_sheet->setCellValue('E' . $i, '');
                            $active_sheet->setCellValue('F' . $i, '');
                            $active_sheet->setCellValue('G' . $i, '');
                            $active_sheet->setCellValue('H' . $i, '');
                            $active_sheet->setCellValue('I' . $i, '');
                            if ($j == 2) {
                                break;
                            } else {
                                $j++;
                            }
                        }
                        $k++;
                        $n = $i;
                    } else {
                        $j = 3;
                        for ($i = $n; $i <= 7; $i++) {
                            $active_sheet->setCellValue('A' . $i, '');
                            $active_sheet->setCellValue('B' . $i, 'Sample Task Name' . $j);
                            $active_sheet->setCellValue('C' . $i, '');
                            $active_sheet->setCellValue('D' . $i, '');
                            $active_sheet->setCellValue('E' . $i, '');
                            $active_sheet->setCellValue('F' . $i, '');
                            $active_sheet->setCellValue('G' . $i, '');
                            $active_sheet->setCellValue('H' . $i, '');
                            $active_sheet->setCellValue('I' . $i, '');
                            if ($j == 4) {
                                break;
                            } else {
                                $j++;
                            }
                        }
                        $k++;
                        $n = $i;
                    }
                }
            }

            $filename = ''.$project->project_name.' Project Activities Template.xlsx';
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($object);


            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache

            ob_end_clean();
            // We'll be outputting an excel file
            $writer->save('php://output');
        }
    }

    public function download_activities_excel_bk($project_id)
    {
        $project = new Project();
        if ($project->load($project_id)) {
            //load our new PHPExcel library
            $this->load->library('excel');
            $object = new PHPExcel();

            $object->setActiveSheetIndex(0);
            $this->excel->getProperties()->setCreator($this->session->userdata('employee_name'));
            $this->excel->getProperties()->setTitle(substr(''.$project->project_name.' Project Activities',0,20));
            $object->getActiveSheet()->setTitle(substr(''.$project->project_name.' Project Activities',0,20));
            $object->getActiveSheet()->setPrintGridlines(TRUE);

            $active_sheet = $object->getActiveSheet();
            $active_sheet->setTitle(substr(''.$project->project_name.' Project Activities',0,20));

            $this->load->model(['activity', 'task', 'measurement_unit']);
            $uom_dropdown = $this->measurement_unit->excel_dropdown_list();
            $activities = $this->activity->get(0, 0, ['project_id' => $project_id]);

            $active_sheet->getStyle('A1:H202')->applyFromArray(
                [
                    'font' => [
                        'name' => 'Verdana',
                        'color' => ['rgb' => '000000'],
                        'size' => 11
                    ],

                    'borders' => [
                        'allborders' => [
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => ['rgb' => 'AAAAAA']
                        ]
                    ]
                ]
            );

            $style['activity_row'] = [
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => 'bfbfbf'],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '2f2f2f'],
                ]
            ];

            $style['rate_column'] = [
                'alignment' => [
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                ]
            ];

            $style['column_heading'] = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '2f2f2f'],
                ],

                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => 'accbe1'],
                ],
                'alignment' => [
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ]
            ];

            $active_sheet->getStyle('A1:I1')->applyFromArray($style['column_heading']);
            $active_sheet->getStyle('E3:E202')->applyFromArray($style['rate_column']);

            for ($col_index = 'A'; $col_index !== 'J'; $col_index++) {
                $active_sheet->getColumnDimension($col_index)->setAutoSize(true);
            }

            $active_sheet->setCellValue('A1', 'ID');
            $active_sheet->setCellValue('B1', 'WORK DESCRIPTION');
            $active_sheet->setCellValue('C1', 'Measurement Unit');
            $active_sheet->setCellValue('D1', 'Quantity');
            $active_sheet->setCellValue('E1', 'Rate');
            $active_sheet->setCellValue('F1', 'Amount');
            $active_sheet->setCellValue('G1', 'Start Date');
            $active_sheet->setCellValue('H1', 'End Date');
            $active_sheet->setCellValue('I1', 'Description');

            for ($index = 2; $index <= 202; $index++) {
                $objValidation = $active_sheet->getCell('C' . $index)->getDataValidation();
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


            if (!empty($activities)) {
                $index = 2;
                foreach ($activities as $activity) {
                    $active_sheet->getStyle('A' . $index . ':I' . $index)->applyFromArray($style['activity_row']);
                    $active_sheet->setCellValue('A' . $index, $activity->activity_id);
                    $active_sheet->setCellValue('B' . $index, $activity->activity_name);
                    $active_sheet->setCellValue('C' . $index, '');
                    $active_sheet->setCellValue('D' . $index, '');
                    $active_sheet->setCellValue('E' . $index, '');
                    $active_sheet->setCellValue('F' . $index, '');
                    $active_sheet->setCellValue('G' . $index, '');
                    $active_sheet->setCellValue('H' . $index, '');
                    $active_sheet->setCellValue('I' . $index, $activity->description);
                    $index++;

                    $tasks = $this->task->get(0, 0, ['activity_id' => $activity->{$activity::DB_TABLE_PK}]);
                    foreach ($tasks as $task) {
                        $active_sheet->setCellValue('A' . $index, $task->task_id);
                        $active_sheet->setCellValue('B' . $index, $task->task_name);
                        $active_sheet->setCellValue('C' . $index, $task->measurement_unit()->symbol);
                        $active_sheet->setCellValue('D' . $index, $task->quantity);
                        $active_sheet->setCellValue('E' . $index, $task->rate);
                        $active_sheet->setCellValue('F' . $index, '=D'.$index.'*E'.$index);
                        $active_sheet->setCellValue('G' . $index, $task->start_date);
                        $active_sheet->setCellValue('H' . $index, $task->end_date);
                        $active_sheet->setCellValue('I' . $index, $task->description);
                        $index++;
                    }
                }
            } else {
                $k = 1;
                for ($n = 2; $n <= 5; $n++) {
                    $active_sheet->getStyle('A' . $n . ':I' . $n)->applyFromArray($style['activity_row']);
                    $active_sheet->setCellValue('A' . $n, '');
                    $active_sheet->setCellValue('B' . $n, 'Sample Activity Name' . $k);
                    $active_sheet->setCellValue('C' . $n, '');
                    $active_sheet->setCellValue('D' . $n, '');
                    $active_sheet->setCellValue('E' . $n, '');
                    $active_sheet->setCellValue('F' . $n, '');
                    $active_sheet->setCellValue('G' . $n, '');
                    $active_sheet->setCellValue('H' . $n, '');
                    $active_sheet->setCellValue('I' . $n, '');
                    $n++;
                    if ($k == 3) {
                        break;
                    }
                    if ($k == 1) {
                        $j = 1;
                        for ($i = $n; $i <= 7; $i++) {
                            $active_sheet->setCellValue('A' . $i, '');
                            $active_sheet->setCellValue('B' . $i, 'Sample Task Name' . $j);
                            $active_sheet->setCellValue('C' . $i, '');
                            $active_sheet->setCellValue('D' . $i, '');
                            $active_sheet->setCellValue('E' . $i, '');
                            $active_sheet->setCellValue('F' . $i, '');
                            $active_sheet->setCellValue('G' . $i, '');
                            $active_sheet->setCellValue('H' . $i, '');
                            $active_sheet->setCellValue('I' . $i, '');
                            if ($j == 2) {
                                break;
                            } else {
                                $j++;
                            }
                        }
                        $k++;
                        $n = $i;
                    } else {
                        $j = 3;
                        for ($i = $n; $i <= 7; $i++) {
                            $active_sheet->setCellValue('A' . $i, '');
                            $active_sheet->setCellValue('B' . $i, 'Sample Task Name' . $j);
                            $active_sheet->setCellValue('C' . $i, '');
                            $active_sheet->setCellValue('D' . $i, '');
                            $active_sheet->setCellValue('E' . $i, '');
                            $active_sheet->setCellValue('F' . $i, '');
                            $active_sheet->setCellValue('G' . $i, '');
                            $active_sheet->setCellValue('H' . $i, '');
                            $active_sheet->setCellValue('I' . $i, '');
                            if ($j == 4) {
                                break;
                            } else {
                                $j++;
                            }
                        }
                        $k++;
                        $n = $i;
                    }
                }
            }

            $filename = ''.$project->project_name.' Project Activities Template';
            $objWriter = PHPExcel_IOFactory::createWriter($object, 'Excel5');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
            ob_end_clean();
            $objWriter->save('php://output');
        }
    }

    public function upload_activities_excel(){
        $file = $_FILES['file']['tmp_name'];

        $project_id = $this->input->post('project_id');


        //read file from path
        $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        //get only the Cell Collection
        $active_sheet = $objPHPExcel->getActiveSheet();
        $sheet_dimension = $active_sheet->getHighestRowAndColumn();
        $i = 2;
        $this->load->model(['activity','task','measurement_unit']);
        while($i <= $sheet_dimension['row']){
            if ($active_sheet->getStyle("B" . $i)->getFont()->getBold() && $active_sheet->getCell("B" . $i)->getFormattedValue() !='') {
                $activity = new Activity();
                $activity->load($active_sheet->getCell("A" . $i)->getFormattedValue());
                $activity->activity_name = trim($active_sheet->getCell("B" . $i)->getFormattedValue());
                $activity->weight_percentage = 0;
                $activity->description = $active_sheet->getCell("I" . $i)->getFormattedValue();
                $activity->project_id = $project_id;
                if($activity->activity_name != '') {
                    $activity->save();
                    $activity_id = $activity->{$activity::DB_TABLE_PK};
                }
            } else {
                if (isset($activity_id)) {
                    $task = new Task();
                    $task->load($active_sheet->getCell("A" . $i)->getFormattedValue());
                    $task->activity_id = $activity_id;
                    $task->task_name = trim($active_sheet->getCell("B" . $i)->getFormattedValue());
                    $unit_symbol = trim($active_sheet->getCell("C" . $i)->getFormattedValue());

                    if($task->task_name != '' && trim($unit_symbol) != '' && !is_int($unit_symbol) && !is_float($unit_symbol)) {
                        $units = $this->measurement_unit->get(1, 0, ['symbol' => $unit_symbol]);

                        if (!empty($units)) {
                            $unit = array_shift($units);
                        } else {
                            $unit = new Measurement_unit();
                            $unit->symbol = $unit_symbol;
                            $unit->name = $unit_symbol;
                            $unit->description = 'As updated from BOQ';
                            $unit->save();
                        }

                        $task->measurement_unit_id = $unit->{$unit::DB_TABLE_PK};
                        $task->quantity = $active_sheet->getCell("D" . $i)->getFormattedValue();
                        $task->rate = remove_commas($active_sheet->getCell("E" . $i)->getFormattedValue());
                        $date_to_start = $active_sheet->getCell("G" . $i)->getFormattedValue();
                        $start_date = $date_to_start != '' ? $date_to_start : null;
                        $task->start_date = $start_date;
                        $date_to_end = $active_sheet->getCell("H" . $i)->getFormattedValue();
                        $end_date = $date_to_end != '' ? $date_to_end : null;
                        $task->end_date = $end_date;
                        $task->description = $active_sheet->getCell("I" . $i)->getFormattedValue();
                        if($task->task_name != '' && $task->measurement_unit_id != '') {
                            $task->save();
                        }
                    }
                }
            }
            $i++;
        }
        echo isset($activity_id) ? $activity_id : 0;
    }

    public function upload_activities_excel_bk(){
    $file = $_FILES['file']['tmp_name'];

    $project_id = $this->input->post('project_id');

    $this->load->library('excel');
    //read file from path
    $objPHPExcel = PHPExcel_IOFactory::load($file);
    //get only the Cell Collection
    $active_sheet = $objPHPExcel->getActiveSheet();
    $sheet_dimension = $active_sheet->getHighestRowAndColumn();
    $i = 2;
    $this->load->model(['activity','task','measurement_unit']);
    while($i <= $sheet_dimension['row']){
        if ($active_sheet->getStyle("B" . $i)->getFont()->getBold() && $active_sheet->getCell("B" . $i)->getFormattedValue() !='') {
            $activity = new Activity();
            $activity->load($active_sheet->getCell("A" . $i)->getFormattedValue());
            $activity->activity_name = trim($active_sheet->getCell("B" . $i)->getFormattedValue());
            $activity->weight_percentage = 0;
            $activity->description = $active_sheet->getCell("I" . $i)->getFormattedValue();
            $activity->project_id = $project_id;
            if($activity->activity_name != '') {
                $activity->save();
                $activity_id = $activity->{$activity::DB_TABLE_PK};
            }
        } else {
            if (isset($activity_id)) {
                $task = new Task();
                $task->load($active_sheet->getCell("A" . $i)->getFormattedValue());
                $task->activity_id = $activity_id;
                $task->task_name = trim($active_sheet->getCell("B" . $i)->getFormattedValue());
                $unit_symbol = trim($active_sheet->getCell("C" . $i)->getFormattedValue());

                if($task->task_name != '' && trim($unit_symbol) != '' && !is_int($unit_symbol) && !is_float($unit_symbol)) {
                    $units = $this->measurement_unit->get(1, 0, ['symbol' => $unit_symbol]);

                    if (!empty($units)) {
                        $unit = array_shift($units);
                    } else {
                        $unit = new Measurement_unit();
                        $unit->symbol = $unit_symbol;
                        $unit->name = $unit_symbol;
                        $unit->description = 'As updated from BOQ';
                        $unit->save();
                    }

                    $task->measurement_unit_id = $unit->{$unit::DB_TABLE_PK};
                    $task->quantity = $active_sheet->getCell("D" . $i)->getFormattedValue();
                    $task->rate = remove_commas($active_sheet->getCell("E" . $i)->getFormattedValue());
                    $date_to_start = $active_sheet->getCell("G" . $i)->getFormattedValue();
                    $start_date = $date_to_start != '' ? $date_to_start : null;
                    $task->start_date = $start_date;
                    $date_to_end = $active_sheet->getCell("H" . $i)->getFormattedValue();
                    $end_date = $date_to_end != '' ? $date_to_end : null;
                    $task->end_date = $end_date;
                    $task->description = $active_sheet->getCell("I" . $i)->getFormattedValue();
                    if($task->task_name != '' && $task->measurement_unit_id != '') {
                        $task->save();
                    }
                }
            }
        }
        $i++;
    }
    echo isset($activity_id) ? $activity_id : 0;
}

    public function settings(){
        $data['title'] = 'Settings';
        $this->load->view('projects/settings/index',$data);
    }

    public function project_categories_list(){
        $this->load->model('project_category');
        $posted_params = dataTable_post_params();
        echo $this->project_category->project_categories_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function save_project_category(){
        $this->load->model('project_category');
        $category = new Project_category();
        $edit = $category->load($this->input->post('category_id'));
        $category->category_name = $this->input->post('category_name');
        $category->description = $this->input->post('description');
        if($category->save()){
            //Callback
            if(!$edit) {
                $this->load->model(['material_item_category','asset_group']);
                $material_item_category = new Material_item_category();
                $asset_group = new Asset_group();
                $asset_group->project_nature_id = $material_item_category->project_nature_id = $category->{$category::DB_TABLE_PK};
                $asset_group->group_name = $material_item_category->category_name = $category->category_name;
                $asset_group->level = $material_item_category->tree_level = 1;
                $asset_group->description = $material_item_category->description = $category->description;
                $material_item_category->save();
                $asset_group->created_by = $this->session->userdata('employee_id');
                $asset_group->save();
            }
        }
    }

    public function delete_project_category(){
        $this->load->model('project_category');
        $category = new Project_category();
        if($category->load($this->input->post('category_id'))){
            $category->delete();
        }
    }

    public function load_project_gantt_chart($project_id){
        $project = new Project();
        $project->load($project_id);
        $activities = $project->activities();
        $gantt_tasks = [];
        foreach ($activities as $activity){
            $tasks = $activity->tasks();
            $start_date = $activity->start_date();
            $end_date = $activity->end_date();
            if($start_date && $end_date) {
                $gantt_tasks[] = [
                    'id' => 'activity_' . $activity->{$activity::DB_TABLE_PK},
                    'name' => $activity->activity_name,
                    'progress' => $activity->completion_percentage(),
                    'progressByWorklog' => false,
                    'relevance' => 0,
                    'type' => '',
                    'typeId' => '',
                    'description' => '',
                    'code' => '',
                    'level' => 0,
                    'status' => 'STATUS_ACTIVE',
                    'depends' => '',
                    'canWrite' => false,
                    'start' => strtotime($start_date) * 1000,
                    'end' => strtotime($end_date) * 1000,
                    "duration" => $activity->duration(),
                    "startIsMilestone" => false,
                    "endIsMilestone" => false,
                    "collapsed" => true,
                    "assigs" => [],
                    "hasChild" => true

                ];

                foreach ($tasks as $task) {
                    $gantt_tasks[] = [
                        'id' => $task->{$task::DB_TABLE_PK},
                        'name' => $task->task_name,
                        'progress' => $task->completion_percentage(),
                        'progressByWorklog' => false,
                        'relevance' => 0,
                        'type' => '',
                        'typeId' => '',
                        'description' => '',
                        'code' => '',
                        'level' => 1,
                        'status' => 'STATUS_ACTIVE',
                        'depends' => '',
                        'canWrite' => true,
                        'start' => strtotime($task->start_date) * 1000,
                        'end' => strtotime($task->end_date) * 1000,
                        "duration" => $task->duration(),
                        "startIsMilestone" => false,
                        "endIsMilestone" => false,
                        "collapsed" => true,
                        "assigs" => [],
                        "hasChild" => false
                    ];
                }
            }
        }

        $ret['project'] = [
            'tasks' => $gantt_tasks,
            'resources' => [],
            'roles' => [],
            "canWrite" => true,
            "canWriteOnParent" => true,
            "zoom" => "m2"
        ];

        echo json_encode($ret);

    }

    public function load_project_dropdown_options(){
        echo projects_dropdown_options(false,false,true);
    }

    public function load_project_cost_center_options(){
        $project = new Project();
        if($project->load($this->input->post('project_id'))){
            echo $project->cost_center_options(true);
        }
    }

    public function close_project(){
        $this->load->model('project_closure');
        $project_closure = new Project_closure();
        $project_closure->project_id = $this->input->post('project_id');
        $project_closure->closure_date = $this->input->post('closure_date');
        $project_closure->remarks = $this->input->post('remarks');
        $project_closure->created_by = $this->session->userdata('employee_id');
        if($project_closure->save()){
            redirect(base_url('projects/profile/'.$project_closure->project_id));
        }
    }

    //Planning and executions
    public function save_project_plan(){
        $this->load->model(['project_plan','currency']);
        $currency_id = $this->input->post('currency_id');
        $currency = new Currency();
        $currency->load($currency_id);
        $project_plan = new Project_plan();
        $project_plan->load($this->input->post('project_plan_id'));
        $project_plan->project_id = $this->input->post('project_id');
        $project_plan->start_date = $this->input->post('start_date');
        $project_plan->end_date = $this->input->post('end_date');
        $project_plan->equipment_n_material_budget =  $this->input->post('equipment_n_material_budget');
        $project_plan->labour_budget =  $this->input->post('labour_budget');
        $project_plan->currency_id = $currency_id;
        $project_plan->exchange_rate = $currency->rate_to_native();
        $project_plan->title = $this->input->post('title');
        $project_plan->created_by = $this->session->userdata('employee_id');
        if($project_plan->save()){
            redirect(base_url('projects/profile/'.$project_plan->project_id));
        }
    }

    public function delete_project_plan(){
        $this->load->model('project_plan');
        $project_plan = new Project_plan();
        if($project_plan->load($this->input->post('project_plan_id'))){
            $project_plan->delete();
        };
    }

    public function project_plans_list($level,$project_id){
        $this->load->model('project_plan');
        $datatable_params = dataTable_post_params();
        echo $this->project_plan->project_plans_list($datatable_params["limit"],$datatable_params["start"],$datatable_params["keyword"],$datatable_params["order"],$level,$project_id);
    }

    public function save_project_plan_task(){
        $this->load->model('project_plan_task');
        $plan_task = new Project_plan_task();
        if($this->input->post('project_plan_task_id')){
            $plan_task->load($this->input->post('project_plan_task_id'));
        }
        $plan_task->project_plan_id = $this->input->post('project_plan_id');
        $plan_task->task_id = $this->input->post('task_id');
        $plan_task->quantity = $this->input->post('quantity');
        $plan_task->output_per_day = $this->input->post('output_per_day');
        $plan_task->created_by = $this->session->userdata('employee_id');
        $plan_task->save();
    }

    public function project_plan_tasks_list($project_plan_id){
        $this->load->model('project_plan_task');
        $datatable_params = dataTable_post_params();
        echo $this->project_plan_task->project_plan_tasks_list($datatable_params["limit"],$datatable_params["start"],$datatable_params["keyword"],$datatable_params["order"],$project_plan_id);
    }

    public function delete_project_plan_task(){
        $this->load->model('project_plan_task');
        $project_plan_task = new Project_plan_task();
        if($project_plan_task->load($this->input->post('project_plan_task_id'))){
            $project_plan_task->delete();
        }
    }

    public function save_project_plan_material_budget(){
        $this->load->model('project_plan_task_material_budget');
        $plan_task_material = new Project_plan_task_material_budget();
        $plan_task_material->material_item_id = $this->input->post('material_item_id');
        $plan_task_material->project_plan_task_id = $this->input->post('project_plan_task_id');
        $plan_task_material->rate = $this->input->post('rate');
        $plan_task_material->quantity = $this->input->post('quantity');
        $plan_task_material->created_by = $this->session->userdata('employee_id');
        $plan_task_material->save();
    }

    public function delete_plan_task_material_budget(){
        $this->load->model('project_plan_task_material_budget');
        $plan_task_material = new Project_plan_task_material_budget();
        if($plan_task_material->load($this->input->post('plan_material_budget_id'))){
            $plan_task_material->delete();
        }
    }

    public function plan_material_budget_list($project_plan_id){
        $this->load->model('project_plan_task_material_budget');
        $datatable_params = dataTable_post_params();
        echo $this->project_plan_task_material_budget->plan_material_budget_list($datatable_params["limit"],$datatable_params["start"],$datatable_params["keyword"],$datatable_params["order"],$project_plan_id);
    }

    public function save_project_plan_task_equipment_budget(){
        $this->load->model('project_plan_task_equipment_budget');
        $plan_task_equipment = new Project_plan_task_equipment_budget();
        $plan_task_equipment->asset_id = $this->input->post('asset_id');
        $plan_task_equipment->project_plan_task_id = $this->input->post('project_plan_task_id');
        $plan_task_equipment->rate_mode = $this->input->post('rate_mode');
        $plan_task_equipment->rate = $this->input->post('rate');
        $plan_task_equipment->duration = $this->input->post('duration');
        $plan_task_equipment->quantity = $this->input->post('quantity');
        $plan_task_equipment->description = $this->input->post('description');
        $plan_task_equipment->created_by = $this->session->userdata('employee_id');
        $plan_task_equipment->save();
    }

    public function load_project_plan_tasks(){
        $this->load->model('project_plan');
        $project_plan = new Project_plan();
        $project_id = $this->input->post('project_id');
        $project_plan_id = $this->input->post('project_plan_id');
        $task_type = $this->input->post('task_type');
        $task_type = !is_null($task_type) ? $task_type : null;
        if($project_plan->load($project_plan_id)){
            if(!is_null($task_type) && $task_type == 'executed' ) {
                echo stringfy_dropdown_options($project_plan->project_plan_tasks_options($project_id,$project_plan_id,false,true));
            } else {
                echo stringfy_dropdown_options($project_plan->project_plan_tasks_options($project_id,$project_plan_id,false));
            }
        }
    }

    public function load_project_plan_executed_tasks(){
        $this->load->model('project_plan');
        $project_plan = new Project_plan();
        $project_id = $this->input->post('project_id');
        $project_plan_id = $this->input->post('project_plan_id');
        if($project_plan->load($project_plan_id)){
            echo stringfy_dropdown_options($project_plan->project_plan_tasks_options($project_id,$project_plan_id,false,true));
        }
    }

    public function plan_eqipment_budget_list($project_plan_id){
        $this->load->model('project_plan_task_equipment_budget');
        $datatable_params = dataTable_post_params();
        echo $this->project_plan_task_equipment_budget->plan_equipment_budget_list($datatable_params["limit"],$datatable_params["start"],$datatable_params["keyword"],$datatable_params["order"],$project_plan_id);
    }

    public function delete_plan_task_equipment_budget(){
        $this->load->model('project_plan_task_equipment_budget');
        $plan_task_equipment = new Project_plan_task_equipment_budget();
        if($plan_task_equipment->load($this->input->post('plan_equipment_budget_id'))){
            $plan_task_equipment->delete();
        }
    }

    public function save_plan_task_casual_labour_budget(){
        $this->load->model('project_plan_task_casual_labour_budget');
        $plan_task_casual_labour = new Project_plan_task_casual_labour_budget();
        $plan_task_casual_labour->load($this->input->post('plan_labour_budget_id'));
        $plan_task_casual_labour->project_plan_task_id = $this->input->post('project_plan_task_id');
        $plan_task_casual_labour->casual_labour_type_id = $this->input->post('casual_labour_type_id');
        $plan_task_casual_labour->rate_mode = $this->input->post('rate_mode');
        $plan_task_casual_labour->duration = $this->input->post('duration');
        $plan_task_casual_labour->no_of_workers = $this->input->post('no_of_workers');
        $plan_task_casual_labour->rate = remove_commas($this->input->post('rate'));
        $plan_task_casual_labour->description = $this->input->post('description');
        $plan_task_casual_labour->created_by = $this->session->userdata('employee_id');
        $plan_task_casual_labour->save();
    }

    public function delete_plan_task_labour_budget(){
        $this->load->model('project_plan_task_casual_labour_budget');
        $plan_task_casual_labour = new Project_plan_task_casual_labour_budget();
        if($plan_task_casual_labour->load($this->input->post('plan_labour_budget_id'))){
            $plan_task_casual_labour->delete();
        }
    }

    public function plan_labour_budget_list($project_plan_id){
        $this->load->model('project_plan_task_casual_labour_budget');
        $datatable_params = dataTable_post_params();
        echo $this->project_plan_task_casual_labour_budget->plan_labour_budget_list($datatable_params["limit"],$datatable_params["start"],$datatable_params["keyword"],$datatable_params["order"],$project_plan_id);
    }

    public function load_task_unit(){
        $this->load->model('task');
        $task = new Task();
        if($task->load($this->input->post('task_id'))){
            echo $task->measurement_unit()->symbol;
        }
    }

    public function load_material_item_unit(){
        $this->load->model('material_item');
        $material_item = new Material_item();
        if($material_item->load($this->input->post('material_item_id'))){
            echo $material_item->unit()->symbol;
        }
    }

    public function validate_task_quantity(){
        $this->load->model('task');
        $task = new Task();
        $task_id = $this->input->post('task_id');
        if($task->load($task_id)){
            $total_executed_quantity = $task->project_plan_task_execution($task_id);
            $total_executed_quantity = !is_null($total_executed_quantity) ? $total_executed_quantity : 0;
            $quantity_to_execute = $task->quantity - $total_executed_quantity;
            echo $quantity_to_execute;
        }
    }

    public function save_project_task_execution(){
        $this->load->model('project_plan_task_execution');
        $execution = new Project_plan_task_execution();
        $execution->load($this->input->post('plan_task_execution_id'));
        $execution->task_id = $this->input->post('task_id');
        $execution->project_plan_id = $this->input->post('project_plan_id');
        $execution->executed_quantity = $this->input->post('executed_quantity');
        $execution->execution_date = $this->input->post('execution_date');
        $execution->created_by = $this->session->userdata('employee_id');
        $execution->save();
    }

    public function project_plan_task_execution_list($project_id,$project_plan_id){
        $this->load->model('project_plan_task_execution');
        $posted_params = dataTable_post_params();
        echo $this->project_plan_task_execution->project_plan_task_execution_list($project_id, $project_plan_id, $posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function delete_plan_task_execution(){
        $this->load->model('project_plan_task_execution');
        $plan_task_execution = new Project_plan_task_execution();
        if($plan_task_execution->load($this->input->post('plan_task_execution_id'))){
            $plan_task_execution->delete();
        }
    }

    public function save_plan_task_labour_execution(){
        $this->load->model('project_plan_task_execution_casual_labour');
        $plan_task_labour_execution = new Project_plan_task_execution_casual_labour();
        $plan_task_labour_execution->load($this->input->post('plan_labour_execution_id'));
        $plan_task_labour_execution->plan_task_execution_id = $this->input->post('plan_task_execution_id');
        $plan_task_labour_execution->date = $this->input->post('date');
        $plan_task_labour_execution->casual_labour_type_id = $this->input->post('casual_labour_type_id');
        $plan_task_labour_execution->rate_mode = $this->input->post('rate_mode');
        $plan_task_labour_execution->duration = $this->input->post('duration');
        $plan_task_labour_execution->no_of_workers = $this->input->post('no_of_workers');
        $plan_task_labour_execution->rate = remove_commas($this->input->post('rate'));
        $plan_task_labour_execution->description = $this->input->post('description');
        $plan_task_labour_execution->created_by = $this->session->userdata('employee_id');
        $plan_task_labour_execution->save();
    }

    public function delete_plan_task_labour_execution(){
        $this->load->model('project_plan_task_execution_casual_labour');
        $plan_task_labour_execution = new Project_plan_task_execution_casual_labour();
        if($plan_task_labour_execution->load($this->input->post('plan_labour_budget_id'))){
            $plan_task_labour_execution->delete();
        }
    }

    public function project_task_equipment_execution_list($project_plan_id){
        $this->load->model('project_plan_task_execution_equipment');
        $datatable_params = dataTable_post_params();
        echo $this->project_plan_task_execution_equipment->project_task_equipment_execution_list($datatable_params["limit"],$datatable_params["start"],$datatable_params["keyword"],$datatable_params["order"],$project_plan_id);
    }

    public function project_plan_labour_execution_list($project_plan_id){
        $this->load->model('project_plan_task_execution_casual_labour');
        $datatable_params = dataTable_post_params();
        echo $this->project_plan_task_execution_casual_labour->project_plan_labour_execution_list($datatable_params["limit"],$datatable_params["start"],$datatable_params["keyword"],$datatable_params["order"],$project_plan_id);
    }

    public function save_project_plan_task_equipment_execution(){
        $this->load->model('project_plan_task_execution_equipment');
        $plan_task_equipment_equipment = new Project_plan_task_execution_equipment();
        $plan_task_equipment_equipment->load($this->input->post('plan_equipment_execution_id'));
        $plan_task_equipment_equipment->date = $this->input->post('date');
        $plan_task_equipment_equipment->asset_id = $this->input->post('asset_id');
        $plan_task_equipment_equipment->plan_task_execution_id = $this->input->post('plan_task_execution_id');
        $plan_task_equipment_equipment->rate_mode = $this->input->post('rate_mode');
        $plan_task_equipment_equipment->rate = $this->input->post('rate');
        $plan_task_equipment_equipment->duration = $this->input->post('duration');
        $plan_task_equipment_equipment->quantity = $this->input->post('quantity');
        $plan_task_equipment_equipment->description = $this->input->post('description');
        $plan_task_equipment_equipment->created_by = $this->session->userdata('employee_id');
        $plan_task_equipment_equipment->save();
    }

    public function delete_plan_task_equipment_execution(){
        $this->load->model('project_plan_task_execution_equipment');
        $plan_task_equipment = new Project_plan_task_execution_equipment();
        if($plan_task_equipment->load($this->input->post('plan_equipment_execution_id'))){
            $plan_task_equipment->delete();
        }
    }

    public function preview_project_plans($project_id){
        $from = $this->input->post('from');
        $from = $from != '' ? $from : null;
        $to = $this->input->post('to');
        $to = $to != '' ? $to : null;
        $this->load->model(['project']);
        $project = new Project();
        if ($project->load($project_id)) {
            $data['project'] = $project;
            $data['from'] = $from;
            $data['to'] = $to;

            $html = $this->load->view('projects/plans/project_plans_sheet', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            //generate the PDF!
            $pdf->WriteHTML($html);
            $pdf->setFooter('<div style="text-align: center">Page {PAGENO} of {nb}</div>');
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output( $project->project_name.'Planning.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function preview_project_plan($project_plan_id){
        $this->load->model(['project_plan_task','project_plan']);
        $project_plan = new Project_plan();
        if ($project_plan->load($project_plan_id)) {
            $data['project_plan'] = $project_plan;

            $html = $this->load->view('projects/plans/project_plan_tasks_sheet', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            //generate the PDF!
            $pdf->WriteHTML($html);
            $pdf->setFooter('<div style="text-align: center">Page {PAGENO} of {nb}</div>');
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output( $project_plan->title .'Planning.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function preview_project_plans_execution($project_id){
        $from = $this->input->post('from');
        $from = $from != '' ? $from : null;
        $to = $this->input->post('to');
        $to = $to != '' ? $to : null;
        $this->load->model(['project']);
        $project = new Project();
        if ($project->load($project_id)) {
            $data['project'] = $project;
            $data['from'] = $from;
            $data['to'] = $to;

            $html = $this->load->view('projects/executions/project_plans_execution_sheet', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            //generate the PDF!
            $pdf->WriteHTML($html);
            $pdf->setFooter('<div style="text-align: center">Page {PAGENO} of {nb}</div>');
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output( $project->project_name.'Planning.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function preview_project_plan_execution($project_plan_id){
        $this->load->model(['project_plan_task_execution','project_plan']);
        $project_plan = new Project_plan();
        if ($project_plan->load($project_plan_id)) {
            $data['project_plan'] = $project_plan;

            $html = $this->load->view('projects/executions/project_plan_tasks_execution_sheet', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            //generate the PDF!
            $pdf->WriteHTML($html);
            $pdf->setFooter('<div style="text-align: center">Page {PAGENO} of {nb}</div>');
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output( $project_plan->title .'Planning.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    //Reports
    public function reports(){
        $report_type = $this->input->post('report_type');
        $project = new Project();
        $project->load($this->input->post('project_id'));
        $from = $this->input->post('from');
        $to = $this->input->post('to');


        if($report_type == 'cost_tracking_worksheet'){
            $this->cost_tracking_worksheet($project,$from,$to);
        } else if($report_type == 'budget_summary'){
            $this->budget_summary($project,null,null);
        } else if($report_type == 'requisition_report'){
            $this->requisitions_report($project,$from,$to);
        } else if($report_type == 'purchase_orders_report'){
            $this->purchase_orders_report($project,$from,$to);
        } else if($report_type == 'grns_report'){
            $this->grns_report($project,$from,$to);
        } else if($report_type == 'material_tracing_report'){
            $this->material_tracing_report($project,$from,$to);
        } else if($report_type == 'approved_payments_report'){
            $this->payments_report($project,$from,$to,true);
        } else if($report_type == 'payments_report'){
            $this->payments_report($project,$from,$to);
        } else if($report_type == 'projects_statement'){
            $this->project_statement($project,$from,$to);
        } else if($report_type == 'projects_inventory_position'){
            set_time_limit(86400);
            ini_set('memory_limit', -1);
            $project_id = $project->{$project::DB_TABLE_PK};
            $project = new Project();
            $project->load($project_id);
            $site_location = $project->location();

            //Requisitions
            $requisitions = $project->requisitions($from, $to);
            $goods_budget = $total_approved_amount = $goods_ordered_value = $order_received_value = $site_received_value = $material_used_value = 0;
            $json['requisitions'] = [];

            $total_store_sourced_amount = 0;
            $transfer_orders = [];
            foreach ($requisitions as $requisition) {
                $final_approval = $requisition->final_approval();
                if ($final_approval) {
                    $total_approved_amount += $amount = $final_approval->total_approved_amount(true);
                    $total_store_sourced_amount += $store_sourced_amount = $final_approval->material_items_approved_amount('store', true);
                    if ($store_sourced_amount > 0) {
                        $transfer_orders[] = [
                            '<a href="' . base_url('requisitions/preview_requisition_approved_chains/' . $requisition->{$requisition::DB_TABLE_PK}) . '" target="_blank">' . $requisition->requisition_number() . '</a>',
                            floatval($store_sourced_amount)
                        ];
                    }
                    $json['requisitions'][] = [
                        '<a href="' . base_url('requisitions/preview_requisition_approved_chains/' . $requisition->{$requisition::DB_TABLE_PK}) . '" target="_blank">' . $requisition->requisition_number() . '</a>',
                        $amount
                    ];
                }
            }

            //Ordered Goods
            $orders = $project->purchase_orders($from, $to);
            $total_supplier_sourced_amount = $total_supplier_sourced_received_value = 0;
            $order_grns = $purchase_orders = [];
            foreach ($orders as $order) {
                $total_supplier_sourced_amount += $supplier_sourced_amount = $order->total_order_in_base_currency();
                $purchase_orders[] = [
                    '<a href="' . base_url('procurements/preview_purchase_order/' . $order->{$order::DB_TABLE_PK}) . '" target="_blank">' . $order->order_number() . '</a>',
                    floatval($supplier_sourced_amount)
                ];
            }

            //Order Grns
            $sql = 'SELECT grn_id FROM purchase_order_grns
                LEFT JOIN purchase_orders ON purchase_order_grns.purchase_order_id = purchase_orders.order_id
                LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
                LEFT JOIN goods_received_notes ON purchase_order_grns.goods_received_note_id = goods_received_notes.grn_id
                WHERE goods_received_notes.receive_date >= "' . $from . '" AND goods_received_notes.receive_date <= "' . $to . '" AND project_id = ' . $project_id . '
                ';
            $query = $this->db->query($sql);
            $results = $query->result();
            $this->load->model('goods_received_note');
            foreach ($results as $result) {
                $grn = new Goods_received_note();
                $grn->load($result->grn_id);
                $total_supplier_sourced_received_value += $amount = $grn->material_value();
                $order_grns[] = [
                    '<a href="' . base_url('inventory/preview_grn/' . $grn->{$grn::DB_TABLE_PK}) . '" target="_blank">' . $grn->grn_number() . '</a>',
                    $amount
                ];
            }

            $order_received_value += $total_supplier_sourced_received_value;
            $json['received_materials']['supplier_sourced_amount'] = floatval($total_supplier_sourced_received_value);
            $json['received_materials']['orders_grns'] = $order_grns;

            $goods_ordered_value += $total_supplier_sourced_amount;
            $goods_ordered_value += $total_store_sourced_amount;
            $json['ordered_goods']['supplier_sourced_amount'] = $total_supplier_sourced_amount;
            $json['ordered_goods']['store_sourced_amount'] = $total_store_sourced_amount;
            $json['ordered_goods']['purchase_orders'] = $purchase_orders;
            $json['ordered_goods']['transfer_orders'] = $transfer_orders;

            //Cost Assignments
            $material_cost_center_assignments = $project->material_cost_center_assignments('IN', $from, $to);
            $store_sourced_received_material_value = $opening_stock_value = $project->material_opening_stock_value();
            $mcas[] = ['Opening', floatval($opening_stock_value)];
            foreach ($material_cost_center_assignments as $assignment) {
                $store_sourced_received_material_value += $value = $assignment->value();
                $mcas[] = [
                    '<a href="' . base_url('inventory/preview_material_cost_center_assignment/' . $assignment->{$assignment::DB_TABLE_PK}) . '" target="_blank">' . $assignment->assignment_number() . '</a>',
                    floatval($value)
                ];
            }
            $order_received_value += $store_sourced_received_material_value;

            $json['received_materials']['store_sourced_amount'] = floatval($store_sourced_received_material_value);
            $json['received_materials']['mcas'] = $mcas;

            //Site GRNS
            $site_grns = $site_location->grns($from, $to);
            $json['site_grns'] = [];
            foreach ($site_grns as $grn) {
                $site_received_value += $amount = $grn->material_value();
                $json['site_grns'][] = [
                    '<a href="' . base_url('inventory/preview_grn/' . $grn->{$grn::DB_TABLE_PK}) . '" target="_blank">' . $grn->grn_number() . '</a>',
                    $amount
                ];
            }

            //Material Used
            $activities = $project->activities();
            $json['cost_activities'][] = [
                'Project Shared',
                $project->actual_cost(['material'], $from, $to, true)
            ];

            $material_used_value += $project->actual_cost(['material'], $from, $to, true);
            foreach ($activities as $activity) {
                $amount = $activity->actual_cost(['material'], $from, $to);
                if ($amount > 0) {
                    $material_used_value += $amount;
                    $json['cost_activities'][] = [
                        '<a href="' . base_url('costs/material_costs_list/activity/' . $activity->{$activity::DB_TABLE_PK}) . '/' . $from . '/' . $to . '" target="_blank">' . $activity->activity_name . '</a>',
                        $amount
                    ];
                }
            }

            //Budget Figure
            $json['budget_activities'][] = [
                'Project Shared',
                $project->budget_figure(['material'], true)
            ];

            $goods_budget += $project->budget_figure(['material'], true);
            foreach ($activities as $activity) {
                $amount = $activity->budget_figure(['material']);
                if ($amount > 1) {
                    $goods_budget += $amount;
                    $json['budget_activities'][] = [
                        '<a href="' . base_url('budgets/material_budget_list/' . $activity->{$activity::DB_TABLE_PK}) . '" target="_blank">' . $activity->activity_name . '</a>',
                        $amount
                    ];
                }
            }

            $data = [
                'material_used_value' => $material_used_value,
                'total_approved_amount' => $total_approved_amount,
                'order_amount' => $goods_ordered_value,
                'ordered_received_value' => $order_received_value,
                'site_goods_received_value' => $site_received_value,
                'material_balance_value' => $project->material_balance_value($to),
                'site_material_balance_value' => $site_location->total_material_balance_value($project_id, $to)
            ];


            if ($this->input->post('print') == 'true') {

                $data['from'] = $from;
                $data['to'] = $to;
                $data['project'] = $project;
                $data['project_id'] = $project->{$project::DB_TABLE_PK};
                $data['print'] = true;

                $html = $this->load->view('reports/project_inventory_position_sheet', $data, true);

                $this->load->library('m_pdf');
                //actually, you can pass mPDF parameter on this load() function
                $pdf = $this->m_pdf->load();
                //generate the PDF!
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
                $pdf->WriteHTML($html);
                //offer it to user via browser download! (The PDF won't be saved on your server HDD)
                $pdf->Output('Project Summary Report - ' . $project->project_name . '.pdf', 'I');
            }
        } else if ($report_type == 'fuel_consumption') {
            $this->project_fuel_consumption($project, $from, $to);
        }
    }

    public function project_cash_flow()
    {
        $this->load->model('project');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $project_id = $this->input->post('project_id');
        $project = new Project();
        $project->load($project_id);
        $x_values = $budgets = $costs = [];
        $x = strtotime($from);
        $days = number_of_days($from,$to);
        $interval = $days > 60 ? round($days/60) : 1;

        $last_time_stamp = strtotime($to)+(86400*$interval);
        $from_timestamp = $x - (86400*$interval);
        for($x; $x <= $last_time_stamp; $x = $x+(86400*$interval)){
            $to_date = strftime('%Y-%m-%d',$x);
            $x_values[] = custom_standard_date($to_date);
            $budgets[] = $x/$interval*12300;
            $from_date =  strftime('%Y-%m-%d',$from_timestamp);
            $costs[] = round($project->actual_cost(['material','sub_contract','miscellaneous','equipment','permanent_labour','casual_labour'],$from_date,$to_date),2);
            $from_timestamp = $x;
        }

        $json['x_values'] = $x_values;
        $json['costs'] = $costs;
        $json['budgets'] = $budgets;
        echo json_encode($json);
    }

    private function requisitions_report($project,$from,$to){
        $data = [
            'from' => $from,
            'to' => $to,
            'project' => $project,
            'print' => $this->input->post('print') == 'true'
        ];
        $data['requisitions'] = $project->requisitions($from,$to);


        if($data['print']){

            $html = $this->load->view('projects/reports/requisition_report',$data,true);

            //this the PDF filename that user will get to download

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $pdf->AddPage('P', // L - landscape, P - portrait
                '', '', '', '',
                15, // margin_left
                15, // margin right
                15, // margin top
                15, // margin bottom
                9, // margin header
                6); // margin footer
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>'. $this->session->userdata('employee_name').'</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>'.strftime('%d/%m/%Y %H:%M:%S').'</span>
                        </div>
                        <div style="text-align: center">Page {PAGENO} of {nb}</div>
                    </div>';
            $pdf->SetFooter($footercontents);
            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force

            $pdf->Output('cost_tracking_'.$project->project_name.'.pdf', 'I'); // view in the explorer

        }   else{

            $this->load->view('projects/reports/requisition_report', $data);

        }
    }

    private function cost_tracking_worksheet($project,$from,$to){
        $data = [
            'from' => $from,
            'to' => $to,
            'project' => $project,
            'print' => $this->input->post('print') == 'true'
        ];
        $activities = $project->activities();
        $data['activities'] = [];
        $data['project_shared'] = [
            'cost_center_name' => 'Project Shared Costs',
            'material_budget' => $project->budget_figure('material',true),
            'permanent_labour_budget' => $project->budget_figure('permanent_labour',true),
            'casual_labour_budget' => $project->budget_figure('casual_labour',true),
            'miscellaneous_budget' => $project->budget_figure('miscellaneous',true),
            'sub_contract_budget' => $project->budget_figure('sub_contract',true),
            'equipment_budget' => $project->budget_figure('equipment',true),
            'completion_percentage' => $project->completion_percentage(),
            'material_cost' => $project->actual_cost('material',$from,$to,true),
            'permanent_labour_cost' => $project->actual_cost('permanent_labour',$from,$to,true),
            'casual_labour_cost' => $project->actual_cost('casual_labour',$from,$to,true),
            'equipment_cost' => $project->actual_cost('equipment',$from,$to,true),
            'miscellaneous_cost' => $project->actual_cost('miscellaneous',$from,$to,true),
            'contract_sum' => 0,
            'sub_contract_cost' => $project->actual_cost('sub_contract_cost',$from,$to,true)
        ];
        foreach ($activities as $activity){
            $tasks = $activity->tasks();
            $activity_tasks = [];
            foreach ($tasks as $task){
                $activity_tasks[] = [
                    'cost_center_name' => $task->task_name,
                    'material_budget' => $task->budget_figure('material'),
                    'permanent_labour_budget' => $task->budget_figure('permanent_labour'),
                    'casual_labour_budget' => $task->budget_figure('casual_labour'),
                    'miscellaneous_budget' => $task->budget_figure('miscellaneous'),
                    'sub_contract_budget' => $task->budget_figure('sub_contract'),
                    'equipment_budget' => $task->budget_figure('equipment'),
                    'completion_percentage' => $task->completion_percentage(),
                    'material_cost' => $task->actual_cost('material',$from,$to),
                    'permanent_labour_cost' => $task->actual_cost('permanent_labour',$from,$to),
                    'casual_labour_cost' => $task->actual_cost('casual_labour',$from,$to),
                    'equipment_cost' => $task->actual_cost('equipment',$from,$to),
                    'miscellaneous_cost' => $task->actual_cost('miscellaneous',$from,$to),
                    'sub_contract_cost' => $task->actual_cost('sub_contract',$from,$to),
                    'contract_sum' => $task->contract_sum()
                ];
            }

            $data['activities'][] = [
                'cost_center_name' => $activity->activity_name,
                'material_budget' => $activity->budget_figure('material'),
                'permanent_labour_budget' => $activity->budget_figure('permanent_labour'),
                'casual_labour_budget' => $activity->budget_figure('casual_labour'),
                'miscellaneous_budget' => $activity->budget_figure('miscellaneous'),
                'sub_contract_budget' => $activity->budget_figure('sub_contract'),
                'equipment_budget' => $activity->budget_figure('equipment'),
                'completion_percentage' => $activity->completion_percentage(),
                'material_cost' => $activity->actual_cost('material',$from,$to),
                'permanent_labour_cost' => $activity->actual_cost('permanent_labour',$from,$to),
                'casual_labour_cost' => $activity->actual_cost('casual_labour',$from,$to),
                'miscellaneous_cost' => $activity->actual_cost('miscellaneous',$from,$to),
                'equipment_cost' => $activity->actual_cost('equipment',$from,$to),
                'sub_contract_cost' => $activity->actual_cost('sub_contract',$from,$to),
                'contract_sum' => $activity->contract_sum(),
                'tasks' => $activity_tasks
            ];
            $data['contract_sum'] = $project->contract_sum();
        }

        if($data['print']){
            $html = $this->load->view('projects/reports/cost_tracking_sheet',$data,true);

            //this the PDF filename that user will get to download

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $pdf->AddPage(
                '', // L - landscape, P - portrait
                '', '', '', '',
                15, // margin_left
                15, // margin right
                15, // margin top
                15, // margin bottom
                9, // margin header
                6,'','','','','','','','','','A3-L'
            ); // margin footer
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>'. $this->session->userdata('employee_name').'</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>'.strftime('%d/%m/%Y %H:%M:%S').'</span>
                        </div>
                        <div style="text-align: center">Page {PAGENO} of {nb}</div>
                    </div>';
            $pdf->SetFooter($footercontents);
            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force

            $pdf->Output('cost_tracking_'.$project->project_name.'.pdf', 'I'); // view in the explorer
        } else {
            $this->load->view('projects/reports/cost_tracking_table', $data);
        }
    }

    private function budget_summary($project,$from,$to){
        $data = [
            'from' => $from,
            'to' => $to,
            'project' => $project,
            'print' => $this->input->post('print') == 'true'
        ];
        $activities = $project->activities();
        $data['project_shared'] = [
            'cost_center_name' => 'Project Shared Costs',
            'material_budget' => $project->budget_figure('material',true),
            'permanent_labour_budget' => $project->budget_figure('permanent_labour',true),
            'casual_labour_budget' => $project->budget_figure('casual_labour',true),
            'miscellaneous_budget' => $project->budget_figure('miscellaneous',true),
            'sub_contract_budget' => $project->budget_figure('sub_contract',true),
            'equipment_budget' => $project->budget_figure('equipment',true),
            'completion_percentage' => $project->completion_percentage(),
            'material_cost' => $project->actual_cost('material',$from,$to,true),
            'permanent_labour_cost' => $project->actual_cost('permanent_labour',$from,$to,true),
            'casual_labour_cost' => $project->actual_cost('casual_labour',$from,$to,true),
            'equipment_cost' => $project->actual_cost('equipment',$from,$to,true),
            'miscellaneous_cost' => $project->actual_cost('miscellaneous',$from,$to,true),
            'contract_sum' => 0,
            'sub_contract_cost' => $project->actual_cost('sub_contract_cost',$from,$to,true)
        ];
        foreach ($activities as $activity){
            $tasks = $activity->tasks();
            $activity_tasks = [];
            foreach ($tasks as $task){
                $activity_tasks[] = [
                    'cost_center_name' => $task->task_name,
                    'material_budget' => $task->budget_figure('material'),
                    'permanent_labour_budget' => $task->budget_figure('permanent_labour'),
                    'casual_labour_budget' => $task->budget_figure('casual_labour'),
                    'miscellaneous_budget' => $task->budget_figure('miscellaneous'),
                    'sub_contract_budget' => $task->budget_figure('sub_contract'),
                    'equipment_budget' => $task->budget_figure('equipment'),
                    'completion_percentage' => $task->completion_percentage(),
                    'material_cost' => $task->actual_cost('material',$from,$to),
                    'permanent_labour_cost' => $task->actual_cost('permanent_labour',$from,$to),
                    'casual_labour_cost' => $task->actual_cost('casual_labour',$from,$to),
                    'equipment_cost' => $task->actual_cost('equipment',$from,$to),
                    'miscellaneous_cost' => $task->actual_cost('miscellaneous',$from,$to),
                    'sub_contract_cost' => $task->actual_cost('sub_contract',$from,$to),
                    'contract_sum' => $task->contract_sum()
                ];
            }

            $data['activities'][] = [
                'cost_center_name' => $activity->activity_name,
                'material_budget' => $activity->budget_figure('material'),
                'permanent_labour_budget' => $activity->budget_figure('permanent_labour'),
                'casual_labour_budget' => $activity->budget_figure('casual_labour'),
                'miscellaneous_budget' => $activity->budget_figure('miscellaneous'),
                'sub_contract_budget' => $activity->budget_figure('sub_contract'),
                'equipment_budget' => $activity->budget_figure('equipment'),
                'completion_percentage' => $activity->completion_percentage(),
                'material_cost' => $activity->actual_cost('material',$from,$to),
                'permanent_labour_cost' => $activity->actual_cost('permanent_labour',$from,$to),
                'casual_labour_cost' => $activity->actual_cost('casual_labour',$from,$to),
                'miscellaneous_cost' => $activity->actual_cost('miscellaneous',$from,$to),
                'equipment_cost' => $activity->actual_cost('equipment',$from,$to),
                'sub_contract_cost' => $activity->actual_cost('sub_contract',$from,$to),
                'contract_sum' => $activity->contract_sum(),
                'tasks' => $activity_tasks
            ];
            $data['contract_sum'] = $project->contract_sum();
        }

        if($data['print']){
            $html = $this->load->view('projects/reports/budget_report_sheet',$data,true);

            //this the PDF filename that user will get to download

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>'. $this->session->userdata('employee_name').'</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>'.strftime('%d/%m/%Y %H:%M:%S').'</span>
                        </div>
                        <div style="text-align: center">Page {PAGENO} of {nb}</div>
                    </div>';
            $pdf->SetFooter($footercontents);
            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force

            $pdf->Output('Budget_summary_'.$project->project_name.'.pdf', 'I'); // view in the explorer
        } else {
            $this->load->view('projects/reports/budget_report_table', $data);
        }
    }

    private function payments_report($project,$from,$to,$approved = false){
        $project_id = $project->{$project::DB_TABLE_PK};
        $project = new Project();
        if($project->load($project_id)){
            $data['from'] = $from;
            $data['to'] = $to;

            if($approved) {
                $data['project_approved_payments'] = $project->project_approved_payments($from,$to,true);
            } else {
                $data['project_approved_payments'] = $project->project_approved_payments($from,$to);
            }
            $print = $this->input->post('print');
            $data['print'] = $print;
            if($print == 'true'){
                $data['project'] = $project;
                $html = $this->load->view('projects/reports/project_approved_payments_sheet',$data,true);
                //this the PDF filename that user will get to download

                //load mPDF library
                $this->load->library('m_pdf');
                //actually, you can pass mPDF parameter on this load() function
                $pdf = $this->m_pdf->load();
                $pdf->AddPage('L', // L - landscape, P - portrait
                    '', '', '', '',
                    15, // margin_left
                    15, // margin right
                    15, // margin top
                    15, // margin bottom
                    9, // margin header
                    6); // margin footer
                $pdf->WriteHTML($html);
                //$this->mpdf->Output($file_name, 'D'); // download force
                $pdf->Output($project->project_name.' Approved Payments '.standard_datetime().'.pdf', 'I'); // view in the explorer

            } else {
                $this->load->view('projects/reports/project_approved_payments_table', $data);
            }
        }
    }

    private function project_statement($project,$from,$to){
        $project_id = $project->{$project::DB_TABLE_PK};
        $project = new Project();
        if($project->load($project_id)) {
            $data['from'] = $from;
            $data['to'] = $to;

            $sql = 'SELECT account_id FROM project_accounts
                    WHERE project_id ='.$project_id;

            $query = $this->db->query($sql);
            if($query->num_rows() > 0) {
                $project_account_id = $query->row()->account_id;

                $this->load->model('account');
                $project_account = new Account();
                $project_account->load($project_account_id);
                $data['account'] = $project_account;
                $sql = 'SELECT currency_id FROM currencies
                    WHERE symbol = "TSH" OR currency_name LIKE "%Tanzania%"';

                $query = $this->db->query($sql);
                $currency_id = $query->row()->currency_id;
                $this->load->model('currency');
                $currency = new Currency();
                $currency->load($currency_id);
                $data['currency'] = $currency;
                $opening_balance_date = new DateTime($from);
                $opening_balance_date->modify(' - 1 day');
                $opening_balance_date = $opening_balance_date->format('Y-m-d');
                $data['print_pdf'] = $print_pdf = $this->input->post('print');
                $data['transactions'] = $transactions = $project_account->statement($currency_id, $from, $to);
                $data['opening_balance'] = $opening_balance = $project_account->balance($currency_id, $opening_balance_date);
                if ($data['print_pdf']) {
                    $html = $this->load->view('finance/statements/statement_sheet', $data, true);

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

                    $pdf->Output('Account Statement.pdf', 'I'); // view in the explorer
                } else {
                    $this->load->view('finance/statements/statement_transactions_table', $data);
                }
            } else {
                echo '<div style="text-align: center" class="alert alert-info col-xs-12">
                        No Petty Cash account for this project
                      </div>';
            }
        }
    }

    private function purchase_orders_report($project,$from,$to){
        $data = [
            'from' => $from,
            'to' => $to,
            'project' => $project,
            'print' => $this->input->post('print') == 'true'
        ];
        $data['purchase_orders'] = $project->purchase_orders($from,$to);


        if($data['print']){

            $html = $this->load->view('projects/reports/purchase_orders_report',$data,true);

            //this the PDF filename that user will get to download

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $pdf->AddPage('P', // L - landscape, P - portrait
                '', '', '', '',
                15, // margin_left
                15, // margin right
                15, // margin top
                15, // margin bottom
                9, // margin header
                6); // margin footer
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>'. $this->session->userdata('employee_name').'</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>'.strftime('%d/%m/%Y %H:%M:%S').'</span>
                        </div>
                        <div style="text-align: center">Page {PAGENO} of {nb}</div>
                    </div>';
            $pdf->SetFooter($footercontents);
            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force

            $pdf->Output('cost_tracking_'.$project->project_name.'.pdf', 'I'); // view in the explorer

        }   else{

            $this->load->view('projects/reports/purchase_orders_report', $data);

        }
    }

    private function grns_report($project,$from,$to){
        $data = [
            'from' => $from,
            'to' => $to,
            'project' => $project,
            'print' => $this->input->post('print') == 'true'
        ];

        $purchase_orders = $project->purchase_orders($from,$to);
        $grns = [];
        foreach ($purchase_orders as $order){
            $order_grns = $order->grns($from,$to);
            foreach ($order_grns as $grn){
                $grns[] = $grn;
            }
        }

        $retirement_grns = $project->imprest_voucher_retirement_grns($from,$to);
        $impvr_grns = [];
        foreach($retirement_grns as $grn){
            $impvr_grns[] = $grn;
        }

        $combined_grns = array_merge($impvr_grns,$grns);
        $data['grns'] = array_sort($combined_grns,'grn_id');

        if($data['print']){

            $html = $this->load->view('projects/reports/grns_report',$data,true);

            //this the PDF filename that user will get to download

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $pdf->AddPage('P', // L - landscape, P - portrait
                '', '', '', '',
                15, // margin_left
                15, // margin right
                15, // margin top
                15, // margin bottom
                9, // margin header
                6); // margin footer
            $footercontents = '
                    <div>
                        <div style="text-align: left; float: left; width: 50%">
                            <strong>Printed By:</strong> <span>'. $this->session->userdata('employee_name').'</span>
                        </div>
                        <div>
                            <strong>Date And Time:</strong> <span>'.strftime('%d/%m/%Y %H:%M:%S').'</span>
                        </div>
                        <div style="text-align: center">Page {PAGENO} of {nb}</div>
                    </div>';
            $pdf->SetFooter($footercontents);
            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force

            $pdf->Output('cost_tracking_'.$project->project_name.'.pdf', 'I'); // view in the explorer

        }   else{

            $this->load->view('projects/reports/grns_report', $data);

        }
    }

    private function material_tracing_report($from,$to){
        $project = new Project();
        if($project->load($this->input->post('project_id')) && $project->allowed_access()){
            $data['from'] = $from;
            $data['to'] = $to;
            $data['material_items'] = $project->related_material_items();
            $site_location = $project->location();
            $data['site_location_id'] = $site_location->{$site_location::DB_TABLE_PK};
            $data['project_id'] = $project->{$project::DB_TABLE_PK};
            $print = $this->input->post('print');
            if($print == 'true'){
                $data['project'] = $project;
                $html = $this->load->view('projects/reports/project_material_tracing_sheet',$data,true);
                //this the PDF filename that user will get to download

                //load mPDF library
                $this->load->library('m_pdf');
                //actually, you can pass mPDF parameter on this load() function
                $pdf = $this->m_pdf->load();
                $pdf->AddPage('L', // L - landscape, P - portrait
                    '', '', '', '',
                    15, // margin_left
                    15, // margin right
                    15, // margin top
                    15, // margin bottom
                    9, // margin header
                    6); // margin footer
                $pdf->WriteHTML($html);
                //$this->mpdf->Output($file_name, 'D'); // download force
                $pdf->Output($project->project_name.' Material Tracing Report '.standard_datetime().'.pdf', 'I'); // view in the explorer

            } else {
                $this->load->view('projects/reports/project_material_tracing_table', $data);
            }
        }
    }

    private function project_fuel_consumption($project, $from, $to)
    {
        $this->load->model('sub_location');
        $data['project'] = $project;
        $data['from'] = $from;
        $data['to'] = $to;

        $sub_location_ids = $this->input->post('sub_location_ids');
        $sub_location_ids = is_array($sub_location_ids) ? array_filter($sub_location_ids) : [];
        $sub_location_ids = !empty($sub_location_ids) ? (count($sub_location_ids) > 1 ? implode(',', $sub_location_ids) : implode($sub_location_ids)) : null;
        $where = ' project_id = ' . $project->project_id . ' AND status = "ACTIVE"  AND equipment_id IS NOT NULL';
        if (!is_null($sub_location_ids)) {
            $where .= ' AND sub_location_id IN (' . $sub_location_ids . ')';
        }
        $sql = 'SELECT sub_locations.* FROM sub_locations 
                LEFT JOIN inventory_locations ON sub_locations.location_id = inventory_locations.location_id WHERE' . $where;
        $query = $this->db->query($sql);
        $sub_locations_arr = $query->result('Sub_location');

        $table_items = [];
        foreach ($sub_locations_arr as $sub_location) {
            $table_items[] = [
                'name' => strtoupper($sub_location->sub_location_name),
                'consumption' => $sub_location->material_used_quantity($project->project_id, $from, $to),
                'rate' => $sub_location->material_average_price($to = null)
            ];
        }

        $data['table_items'] = $table_items;
        $print = $this->input->post('print');
        $data['print'] = $print;
        if ($print == 'true') {
            $data['project'] = $project;
            $html = $this->load->view('projects/reports/project_fuel_consumption_sheet', $data, true);
            //this the PDF filename that user will get to download

            //load mPDF library
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();
            $pdf->AddPage(
                'P', // L - landscape, P - portrait
                '',
                '',
                '',
                '',
                15, // margin_left
                15, // margin right
                15, // margin top
                15, // margin bottom
                9, // margin header
                6
            ); // margin footer
            $pdf->WriteHTML($html);
            //$this->mpdf->Output($file_name, 'D'); // download force
            $pdf->Output($project->project_name . ' Project Fuel Consumption ' . standard_datetime() . '.pdf', 'I'); // view in the explorer

        } else {
            $this->load->view('projects/reports/project_fuel_consumption_table', $data);
        }
    }


    //Certificates

    public function save_project_certificate(){
        $certifcate_date = $this->input->post('certificate_date');
        if($certifcate_date!="") {
            $this->load->model('Project_certificate');
            $project_certificate = new Project_certificate();
            $project_certificate->load($this->input->post('certificate_id'));
            $project_certificate->project_id = $this->input->post('project_id');
            $project_certificate->certificate_number = $this->input->post('certificate_number');
            $project_certificate->certificate_date = $certifcate_date;
            $project_certificate->certified_amount = $this->input->post('certified_amount');
            $project_certificate->comments = $this->input->post('comment');
            $project_certificate->created_by = $this->session->userdata('employee_id');
            $project_certificate->save();
        }
    }

    public function project_certificate_list($project_id = 0){
        $this->load->model('Project_certificate');
        $params= dataTable_post_params();
        echo $this->Project_certificate->project_certificate_list($project_id,$params['limit'],$params['start'],$params['keyword'],$params['order']);
    }

    public function delete_project_certificate(){
        $this->load->model('Project_certificate');
        $project_certificate = new Project_certificate();
        $project_certificate->load($this->input->post('certificate_id'));
        $project_certificate->delete();
    }

    //Project Extensions

    public function project_revision_list($project_id)
    {
        $this->load->model('revision');
        $datatable_params=dataTable_post_params();
        echo $this->revision->revision_list($project_id,$datatable_params["limit"],$datatable_params["start"],$datatable_params["keyword"],$datatable_params["order"]);
    }

    public function save_revision()
    {
        $this->load->model(['revision','revised_task','project_contract_review']);
        $item_types = $this->input->post('item_types');

        if(!empty($item_types)) {
            if(in_array('task_revision',$item_types)) {
                $revision = new Revision();
                $edit = $revision->load($this->input->post('revision_id'));
                $revision->project_id = $this->input->post('project_id');
                $revision->revision_date = $this->input->post('revision_date');
                $revision->description = $this->input->post('description');
                $revision->created_by = $this->session->userdata('employee_id');
                $revision->save();

                if ($edit) {
                    $revision->delete_revised_tasks();
                }
            }

            foreach($item_types as $index=>$item_type) {
                if ($item_type == "task_revision") {

                    $revised_task = new Revised_task();
                    $revised_task->revision_id = $revision->{$revision::DB_TABLE_PK};
                    $revised_task->task_id = $this->input->post('reasons_or_task_ids')[$index];
                    $revised_task->quantity = $this->input->post('quantities')[$index];
                    $revised_task->rate = $this->input->post('rates')[$index];
                    $revised_task->save();

                } else {

                    $project_contract_review = new Project_contract_review();
                    $project_contract_review->load($this->input->post('project_contract_review_id'));
                    $project_contract_review->project_id = $this->input->post('project_id');
                    $project_contract_review->review_date = $this->input->post('revision_date');
                    $project_contract_review->duration_variation = $this->input->post('duration_variations')[$index];
                    $project_contract_review->duration_type = $this->input->post('duration_types')[$index];
                    $project_contract_review->contract_sum_variation = $this->input->post('contract_sum_variations')[$index];
                    $project_contract_review->plus_or_minus_contract_sum = $this->input->post('plus_or_minus_contract_sums')[$index];
                    $project_contract_review->plus_or_minus_duration = $this->input->post('plus_or_minus_durations')[$index];
                    $project_contract_review->reason = $this->input->post('reasons_or_task_ids')[$index];
                    $project_contract_review->created_by = $this->session->userdata('employee_id');
                    $project_contract_review->save();
                }
            }
        }
    }

    public function preview_project_revision($revision_id){
        $this->load->model('revision');
        $project_revision = new Revision();
        if ($project_revision->load($revision_id)) {
            $data['project_revision'] = $project_revision;

            $html = $this->load->view('projects/contract_reviews/project_revision_sheet', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            //generate the PDF!
            $pdf->WriteHTML($html);
            $pdf->setFooter('<div style="text-align: center">Page {PAGENO} of {nb}</div>');
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output( $project_revision->revision_number() .'.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function preview_project_revisions($project_id){
        $this->load->model(['project']);
        $project = new Project();
        if ($project->load($project_id)) {
            $data['project'] = $project;

            $html = $this->load->view('projects/contract_reviews/project_revisions_sheet', $data, true);

            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            //generate the PDF!
            $pdf->WriteHTML($html);
            $pdf->setFooter('<div style="text-align: center">Page {PAGENO} of {nb}</div>');
            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output( $project->project_name.'Review.pdf', 'I');
        } else {
            redirect(base_url());
        }
    }

    public function delete_project_revision()
    {
        $this->load->model('revision');
        $project_revision = new Revision();
        $project_revision->load($this->input->post('project_revision_id'));
        $project_revision->delete_revised_tasks();
        $project_revision->delete();
    }

    public function save_project_sub_contract(){
        $this->load->model('Sub_contract');
        $Sub_contract = new Sub_contract();
        $edit = $Sub_contract->load($this->input->post('sub_contract_id'));
        $Sub_contract->stakeholder_id = $this->input->post('contractor_id');
        $Sub_contract->project_id = $this->input->post('project_id');
        $Sub_contract->contract_name = $this->input->post('contract_name');
        $Sub_contract->contract_date = $this->input->post('contract_date');
        $Sub_contract->description = $this->input->post('description');
        $Sub_contract->created_by = $this->session->userdata('employee_id');

        $Sub_contract->save();

    }

    public function sub_contracts_list($project_id = 0){
        $this->load->model('Sub_contract');
        $sub_contract= new Sub_contract();
        $posted_params = dataTable_post_params();
        echo $sub_contract->project_sub_contracts_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order'],$project_id);
    }

    public function delete_project_sub_contract(){
        $this->load->model('Sub_contract');
        $Sub_contract= new Sub_contract();
        if($Sub_contract->load($this->input->post('sub_contract_id'))){

            $Sub_contract->delete();
        }
    }

    public function save_sub_contract_item(){
        $this->load->model('Sub_contract_item');
        $sub_contract_item = new Sub_contract_item();
        $sub_contract_item->sub_contract_id= $this->input->post('sub_contract_id');
        $sub_contract_item->start_date= $this->input->post('start_date');
        $sub_contract_item->end_date= $this->input->post('end_date');
        $sub_contract_item->contract_sum= $this->input->post('contract_sum');
        $sub_contract_item->vat_inclusive = $this->input->post('vat_inclusive');
        $sub_contract_item->vat_percentage = 18;
        $sub_contract_item->description= $this->input->post('description');
        $sub_contract_item->task_id= $this->input->post('task_id');
        $sub_contract_item->task_id = $sub_contract_item->task_id != '' ? $sub_contract_item->task_id : null;
        $sub_contract_item->save();
    }

    public function delete_sub_contract_item(){
        $this->load->model('Sub_contract_item');
        $sub_contract_item = new Sub_contract_item();
        if($sub_contract_item->load($this->input->post('sub_contract_item_id'))){

            $sub_contract_item->delete();
        }
    }

    public function load_sub_contract_items(){
        $this->load->model('Sub_contract');
        $sub_contract= new Sub_contract();
        if($sub_contract->load($this->input->post('sub_contract_id'))){
            $data['sub_contract_items'] = $sub_contract->sub_contract_items();
            $this->load->view('projects/sub_contracts/sub_contract_items/sub_contract_items_tab',$data);
        }
    }

    public function projects_overview(){
        if(!$this->session->userdata('has_project')){
            check_permission('Executive Reports') || check_permission('Administrative Actions',true);
        }
        $this->load->model('project','project_closure');
        $data['on_going_projects_options'] = $this->project->on_going_projects_dropdown();
        $data['title'] = 'Projects Overview';
        $this->load->view('projects/projects_overview/index',$data);
    }

    public function completed_projects_list(){
        $this->load->model('project');
        $datatable_params=dataTable_post_params();
        echo $this->project->completed_projects_list($datatable_params["limit"],$datatable_params["start"],$datatable_params["keyword"],$datatable_params["order"]);
    }

    public function selected_on_going_project(){
        $this->load->model('project');
        $project = new Project();
        $project->load($this->input->post('project_id'));
        $data['project'] = $project;
        $this->load->view('projects/projects_overview/project_details',$data);
    }

    public function validate_plan_task_quantity(){
        $this->load->model('project_plan_task');
        $project_plan_task = new Project_plan_task();
        $project_plan_task_id = $this->input->post('project_plan_task_id');
        if($project_plan_task->load($project_plan_task_id)){
            echo $project_plan_task->quantity;
        }
    }

    public function services()
    {
        $this->load->model('maintenance_service');
        $limit = $this->input->post('length');
        if($limit != '') {
            $param = dataTable_post_params();
            echo $this->maintenance_service->services($param['limit'], $param['start'], $param['keyword'], $param['order']);

        }else{

            $this->load->model(['stakeholder', 'currency', 'measurement_unit']);
            $data['title'] = 'Services List';
            $data['stakeholder_options'] = $this->stakeholder->dropdown_options();
            $data['currency_options'] = currency_dropdown_options();
            $data['measurement_unit_options'] = measurement_unit_dropdown_options();
            $this->load->view('projects/services/index', $data);
        }
    }

    public function preview_services($service_id)
    {
        $this->load->model(['company_detail', 'stakeholder', 'maintenance_service', 'maintenance_service_item', 'currency', 'measurement_unit']);

        $service = new Maintenance_service();
        $service->load($service_id);
        $stakeholders = new Stakeholder();
        $stakeholders->load($service->stakeholder_id);
        $currency_details = new Currency();
        $currency_details->load($service->currency_id);
        $unit = new Measurement_unit();

        $data['service'] = $service;
        $data['company_details'] = $this->company_detail->company_details();
        $data['stakeholder_details'] = $stakeholders;
        $data['service_items'] = $this->maintenance_service_item->service_items_list($service_id);
        $data['currency_details'] = $currency_details;
        $data['unit'] = $unit;

        $html = $this->load->view('projects/services/services_sheet', $data, true);

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

        $pdf->Output('Invoice-'.add_leading_zeros($service_id).'.pdf', 'I'); // view in the explorer

    }

    public function save_services()
    {
        $this->load->model('maintenance_service');
        $service = new Maintenance_service();
        $edit = $service->load($this->input->post('service_id'));
        $service->service_date = $this->input->post('service_date');
        $service->currency_id = $this->input->post('currency_id');
        $service->client_id = $this->input->post('client_id');
        $service->location = $this->input->post('location');
        $service->remarks = $this->input->post('remarks');
        $service->created_by = $this->session->userdata('employee_id');
        if($service->save()){
            if($edit){
                $service->clear_items();
            }

            $this->load->model('maintenance_service_item');
            $unit_ids = $this->input->post('unit_ids');

            foreach ($unit_ids as $index => $unit_id){
                $service_item = new Maintenance_service_item();
                $service_item->service_id = $service->{$service::DB_TABLE_PK};
                $service_item->quantity = $this->input->post('quantities')[$index];
                $service_item->rate = $this->input->post('rates')[$index];
                $service_item->description = $this->input->post('descriptions')[$index];
                $service_item->measurement_unit_id = $this->input->post('unit_ids')[$index];
                $service_item->save();
            }
        }
    }

    public function delete_services()
    {
        $this->load->model('maintenance_service');
        $service_id = $this->input->post('service_id');
        $service = new Maintenance_service();
        $service->load($service_id);
        $service->delete();
    }

}
