<?php

class Stakeholder_evaluation_score extends MY_Model
{

    const DB_TABLE = 'stakeholder_evaluation_scores';
    const DB_TABLE_PK = 'id';

    public $stakeholder_id;
    public $stakeholder_evaluation_factor_id;

    public function evaluated_stakeholders_options()
    {
        $this->load->model('stakeholder');
        return $this->stakeholder->stakeholder_options();
    }

}
