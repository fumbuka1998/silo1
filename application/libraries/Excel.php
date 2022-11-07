<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

ini_set("memory_limit","8000M");
set_time_limit (360);
ini_set('max_execution_time',360);

//require_once APPPATH."/third_party/PHPExcel.php";
require_once APPPATH."/third_party/office/PHPExcel/Classes/PHPExcel.php";
class Excel extends PHPExcel {
    public function __construct() {
        parent::__construct();
    }
}
