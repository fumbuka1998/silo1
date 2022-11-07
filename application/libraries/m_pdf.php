<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


ini_set("memory_limit","2048M");

class m_pdf {

    function m_pdf()
    {
        $CI = & get_instance();
    }

    function load($param=NULL)
    {
        include_once APPPATH.'/third_party/mpdf/mpdf.php';

        if ($param == NULL)
        {
            $param = '"en-GB-x","A4","","",10,10,10,10,6,3';
        }

        return new mPDF($param);
    }
}