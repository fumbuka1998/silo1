<?php
/**
 * Created by PhpStorm.
 * User: userx
 * Date: 12/5/19
 * Time: 11:14 AM
 */

class Attendance extends  MY_Model{
    const DB_TABLE = 'attendances';
    const DB_TABLE_PK = 'id';

    public $date;
    public $time;
    public $employee_id;
    public $type;

    public function employee(){
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->employee_id);
        return $employee;
    }


}