<?php

class Contractor_evaluation_score extends MY_Model
{

    const DB_TABLE = 'contractor_evaluation_scores';
    const DB_TABLE_PK = 'id';

    public $contractor_id;
    public $supplier_evaluation_factors_id;

    public function evaluated_contractors_options()
    {
        $this->load->model('contractor');
        return $this->contractor->contractor_options();
    }

}