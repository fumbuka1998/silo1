<?php
/**
 * Created by PhpStorm.
 * User: stunna
 * Date: 12/11/2017
 * Time: 09:46
 */

class Company_detail extends MY_Model{
    
    const DB_TABLE = 'company_details';
    const DB_TABLE_PK = 'id';

    public $company_name;
    public $telephone;
    public $mobile;
    public $fax;
    public $email;
    public $address;
    public $tin;
    public $vrn;
    public $website;
    public $tagline;
    public $corporate_color;
    public $created_by;

    public function company_details()
    {
        $company_details = $this->get(1,0,['id' => 3]);
        $details = array_shift($company_details);
        return $details;
    }

}

