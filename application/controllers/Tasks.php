<?php

class Tasks extends CI_Controller{
    
    public function __construct() {
        parent::__construct();
        check_login();
        $this->load->model('task');
    }

    public function save_task(){
        $task = new Task();
        $edit = $task->load($this->input->post('task_id'));
        $task->task_name = $this->input->post('task_name');
        $task->activity_id = $this->input->post('activity_id');
        $task->start_date = $this->input->post('start_date') != '' ? $this->input->post('start_date') : null;
        $task->end_date = $this->input->post('end_date') != '' ? $this->input->post('end_date') : null;
        $task->measurement_unit_id = $this->input->post('measurement_unit_id');
        $task->quantity = $this->input->post('quantity');
        $task->rate = $this->input->post('rate');
        $task->predecessor = null;
        $task->description = $this->input->post('description');
        if($task->save()){
            $activity = $task->activity();
            $project = $activity->project();
            $action = $edit ? 'Task Update' : 'Task Registration';
            $description = 'Task '.$task->task_name.' for activity '.$activity->activity_name.' in '.$project->project_name.' was ';
            $description .= $edit ? 'updated' : 'registered';
            system_log($action,$description,$project->{$project::DB_TABLE_PK});
        }

    }

    public function delete_task(){
        $task = new Task();
        if($task->load($this->input->post('task_id'))){
            $activity = $task->activity();
            $project = $activity->project();
            $description = 'The task '.$task->task_name.' from '.$activity->activity_name.' in '.$project->project_name.' was deleted';
            $task->delete();
            system_log('Task Delete',$description,$project->{$project::DB_TABLE_PK});
        }
    }

    public function activity_tasks_list($activity_id = 0){

        $this->load->model('activity');
        $activity = new Activity();
        $activity->load($activity_id);
        $data['activity'] = $activity;
        $data['project'] = $activity->project();
        $data['store_sub_location_options'] = $activity->store_sub_location_options();
        $keyword = $this->input->post('search')['value'];
        $limit = $this->input->post('length');
        $start = $this->input->post('start');

        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        switch ($order_column) {
            case 0;
                $order_column = 'task_name';
                break;
            case 1;
                $order_column = 'start_date';
                break;
            case 2;
                $order_column = 'weight_percentage';
                break;
            default:
                $order_column = 'start_date';
        }

        $order = $order_column.' '.$order_dir;

        $where = ' activity_id = "'.$activity_id.'"';
        if($keyword != ''){
            $where .= ' AND (task_name LIKE "%'.$keyword.'%" OR start_date LIKE "%'.$keyword.'%" OR end_date LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%") ';
        }

        $tasks = $this->task->get($limit,$start,$where,$order);
        $rows = [];
        foreach($tasks as $task){
            $data['task'] = $task;

            $rows[] = [
                $task->task_name,
                $task->start_date != '' ? custom_standard_date($task->start_date) : 'N/A',
                $task->end_date != '' ? custom_standard_date($task->end_date) : 'N/A',
                '<span style="text-align: center">'.$task->measurement_unit()->symbol.'</span>',
                '<span class="pull-right">'.$task->quantity.'</span>',
                '<span class="pull-right">'.number_format($task->rate,2).'</span>',
                number_format(($task->quantity*$task->rate),2),
                '<span class="pull-right">'.$this->load->view('projects/activities/tasks/task_list_actions',$data,true).'</span>'
            ];
        }
        $records_filtered = $this->task->count_rows($where);
        $records_total = $this->task->count_rows(['activity_id' => $activity_id]);
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);
    }

    public function tasks_list($level){
        $this->load->model('task');
        $datatable_params=dataTable_post_params();
        echo $this->task->tasks_list($datatable_params["limit"],$datatable_params["start"],$datatable_params["keyword"],$datatable_params["order"], $level);
    }

    public function save_task_progress(){
        $this->load->model('task_progress_update');
        $progress_update = new Task_progress_update();
        $edit = $progress_update->load($this->input->post('update_id'));
        $progress_update->task_id = $this->input->post('task_id');
        $progress_update->datetime_updated = $this->input->post('datetime');
        $progress_update->description = $this->input->post('description');
        $progress_update->percentage = $this->input->post('percentage');
        if($progress_update->save()){
            $task = $progress_update->task();
            $project = $task->project();
            $activity = $task->activity();
            $description = 'Project: '.$project->project_name.', Activity: '.$activity->activity_name.', Task: '.$task->task_name;
            system_log('Task Progress Update',$description,$project->{$project::DB_TABLE_PK});
        }
    }

    public function task_progress_updates_list($task_id = 0){
        $this->load->model('task_progress_update');
        $limit = $this->input->post('length');
        $keyword = $this->input->post('search')['value'];
        $start = $this->input->post('start');

        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        switch ($order_column) {
            case 0;
                $order_column = 'datetime_updated';
                break;
            case 1;
                $order_column = 'percentage';
                break;
            case 2;
                $order_column = 'description';
                break;
            default:
                $order_column = 'datetime_updated';
        }

        $order = $order_column.' '.$order_dir;

        $where = 'task_id = "'.$task_id.'"';
        if($keyword != ''){
            $where .= ' AND (datetime_updated LIKE "%'.$keyword.'%" OR percentage = "'.$keyword.'" OR description LIKE "%'.$keyword.'%")  ';
        }

        $updates = $this->task_progress_update->get($limit,$start,$where,$order);
        $rows = [];
        $data['task_id'] = $task_id;
        foreach($updates as $update){
            $data['progress_update'] = $update;
            $rows[] = [
                $update->datetime_updated,
                $update->percentage,
                $update->description,
                $this->load->view('projects/activities/tasks/task_progress_list_actions',$data,true)
            ];
        }
        $records_filtered = $this->task_progress_update->count_rows($where);
        $records_total = $this->task_progress_update->count_rows(['task_id' => $task_id]);
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        echo json_encode($json);
    }

    public function delete_task_progress_update(){
        $this->load->model('task_progress_update');
        $update = new Task_progress_update();
        if($update->load($this->input->post('update_id'))){
            $task = $update->task();
            $project = $task->project();
            $activity = $task->activity();
            $description = 'Project: '.$project->project_name.', Activity: '.$activity->activity_name.', Task: '.$task->task_name;
            $update->delete();
            system_log('Task Progress Delete',$description,$project->{$project::DB_TABLE_PK});
        }
    }

    public function task_progress_graph_values(){
        $this->load->model('task_progress_update');
        $updates = $this->task_progress_update->get(0,0,['task_id' => $this->input->post('task_id')],' datetime_updated ASC');
        $data = [];
        foreach($updates as $update){
            $data[] = [strtotime($update->datetime_updated)*1000,intval($update->percentage)];
        }
        $output['data'] = $data;
        echo json_encode($output);
    }

    public function load_task_summary(){
        $task = new Task();
        if($task->load($this->input->post('task_id'))){
            $this->load->view('projects/activities/tasks/task_summary_tab',['task' => $task]);
        }
    }

}

