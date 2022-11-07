<?php

class Official_ssf extends MY_Model{

    const DB_TABLE = 'official_ssfs';
    const DB_TABLE_PK = 'id';

    public $ssf_name;
    public $employee_deduction_percentage;
    public $employer_deduction_percentage;

    public function official_ssf_list(){
        $official_ssfs = $this->get();
        return $official_ssfs;
    }

    public function official_ssf_options(){
        $official_ssfs = $this->get();
        $options[''] = '&nbsp;';
        foreach ($official_ssfs as $official_ssfs){
            $options[$official_ssfs->{$this::DB_TABLE_PK}] =$official_ssfs->ssf_name;
        }
        return $options;
    }

}