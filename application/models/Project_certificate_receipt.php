<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/12/2018
 * Time: 3:41 PM
 *
 */
class Project_certificate_receipt extends MY_Model
{

    const DB_TABLE = 'project_certificate_receipts';
    const DB_TABLE_PK = 'id';

    public $receipt_id;
    public $certificate_id;

    public function certificate()
    {
        $this->load->model('project_certificate');
        $certificate = new Project_certificate();
        $certificate->load($this->certificate_id);
        return $certificate;
    }

    public function receipt()
    {
        $this->load->model('receipt');
        $receipt = new Receipt();
        $receipt->load($this->receipt_id);
        return $receipt;
    }
}