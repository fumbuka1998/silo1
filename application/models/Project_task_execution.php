<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/12/2018
 * Time: 12:13 PM
 */

class Project_task_execution extends MY_Model{
    const DB_TABLE = 'project_task_executions';
    const DB_TABLE_PK = 'id';

    public $task_id;
    public $quantity;
    public $execution_date;
    public $created_by;

}