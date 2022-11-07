<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 4/17/2018
 * Time: 5:20 PM
 */

class Project_payment_voucher_item extends MY_Model{
    const DB_TABLE = 'project_payment_voucher_items';
    const DB_TABLE_PK ='id';

    public $project_id;
    public $payment_voucher_item_id;

    public function project()
    {
        $this->load->model('project');
        $project = new Project();
        $project->load($this->project_id);
        return $project;
    }
}