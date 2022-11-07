<?php

class User_permission extends MY_Model{
    
    const DB_TABLE = 'users_permissions';
    const DB_TABLE_PK = 'user_permission_id';

    public $user_id;
    public $permission_id;

    public function permission(){
        $this->load->model('permission');
        $permission = new Permission();
        $permission->load($this->permission_id);
        return $permission;
    }

    public function user_permission_privileges(){
        $this->load->model('user_permission_privilege');
        return $this->user_permission_privilege->get(0,0,['user_permission_id'=>$this->{$this::DB_TABLE_PK}]);
    }

}

