<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/12/2018
 * Time: 3:41 PM
 *
 */
class Project_certificates_receipt extends MY_Model
{

    const DB_TABLE = 'project_certificates_receipts';
    const DB_TABLE_PK = 'id';

    public $receipt_id;
    public $certificate_id;
    public $with_holding_tax;
}