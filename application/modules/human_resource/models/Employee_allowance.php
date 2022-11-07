<?php
/**
 * Created by PhpStorm.
 * User: zeus
 * Date: 28/03/2019
 * Time: 14:03
 */


class Employee_allowance extends MY_Model
{

    const DB_TABLE = 'employee_allowances';
    const DB_TABLE_PK = 'id';

    public $employee_id;
    public $allowance_id;
    public $allowance_amount;
    public $created_by;

    public function employee_allowances($employee_id)
    {
        $allowances = $this->get(0,0,['employee_id' => $employee_id]);
        $data = [];
        foreach ($allowances as $allowance){
            $data[] = [
                'employee_allowance_id' => $allowance->id,
                'allowance_id' => $allowance->allowance_id,
                'employee_id' => $allowance->employee_id,
                'allowance_amount' => $allowance->allowance_amount
            ];
        }
        return $data;
    }

    public function clear_items(){
        $this->db->delete($this::DB_TABLE,$this->{$this::DB_TABLE_PK});
    }

}