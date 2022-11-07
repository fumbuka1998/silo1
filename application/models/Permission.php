<?php

class Permission extends MY_Model{
    
    const DB_TABLE = 'permissions';
    const DB_TABLE_PK = 'permission_id';

    public $name;

    public function permission_privileges(){
        $this->load->model('permission_privilege');
        return $this->permission_privilege->get(0,0,['parent_id'=>$this->{$this::DB_TABLE_PK}],'privilege ASC');
    }
}

