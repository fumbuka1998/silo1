<?php

class Official_hif extends MY_Model{

    const DB_TABLE = 'official_hifs';
    const DB_TABLE_PK = 'id';

    public $hif_name;
    public $employee_deduction_percentage;
    public $employer_deduction_percentage;

    public function official_hif_list(){
        $official_hifs = $this->get();
        return $official_hifs;
    }

    public function official_hif_options(){
        $official_hifs = $this->get();
        $options[''] = '&nbsp;';
        foreach ($official_hifs as $official_hifs){
            $options[$official_hifs->{$this::DB_TABLE_PK}] =$official_hifs->hif_name;
        }
        return $options;
    }

}