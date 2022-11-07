<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 1/24/2019
 * Time: 6:05 PM
 */

class Project_certificate_invoice extends MY_Model{
    const DB_TABLE = 'project_certificate_invoices';
    const DB_TABLE_PK = 'id';

    public $project_certificate_id;
    public $outgoing_invoice_id;
}