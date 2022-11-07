<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 12/5/2018
 * Time: 11:42 AM
 */

class User_permission_privilege extends MY_Model{
    const DB_TABLE = 'user_permission_privileges';
    const DB_TABLE_PK = 'id';

    public $user_permission_id;
    public $permission_privilege_id;

    public function permission_privilege(){
        $this->load->model('permission_privilege');
        $permission_privilege = new Permission_privilege();
        $permission_privilege->load($this->permission_privilege_id);
        return $permission_privilege;
    }
}