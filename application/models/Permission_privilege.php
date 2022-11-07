<?php

class Permission_privilege extends MY_Model
{
    const DB_TABLE = 'permission_privileges';
    const DB_TABLE_PK = 'id';

    public $parent_id;
    public $privilege;

    public function permissions()
    {
        $this->load->model('permission');
        $permissions = new Permission();
        $permissions->load($this->parent_id);
        return $permissions;
    }


}