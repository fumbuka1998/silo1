<?php

class Maintenance_invoice extends MY_Model
{
    const DB_TABLE = 'maintenance_invoices';
    const DB_TABLE_PK = 'id';

    public $outgoing_invoice_id;
    public $service_id;

}