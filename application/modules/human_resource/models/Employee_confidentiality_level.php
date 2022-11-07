<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 7/8/2019
 * Time: 11:19 AM
 */

class Employee_confidentiality_level extends MY_Model{
    const DB_TABLE = 'employee_confidentiality_levels';
    const DB_TABLE_PK = 'level_id';

    public $level_name;
    public $chain_position;
    public $created_by;


    public function dropdown_options($justArray = false){
        $levels = $this->get();
        if(!$justArray) {
            $options[] = '';
            foreach ($levels as $level) {
                $options[$level->{$level::DB_TABLE_PK}] = $level->level_name;
            }
            return $options;
        } else {
            return !empty($levels) ? $levels : false;
        }
    }
    
}