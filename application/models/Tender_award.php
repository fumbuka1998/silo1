<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/27/2018
 * Time: 4:34 PM
 */
class Tender_award extends MY_Model
{

    const DB_TABLE = 'tender_awards';
    const DB_TABLE_PK = 'id';

    public $date_submitted;
    public $tender_id;
    public $submitted_by;
    public $created_by;
}
