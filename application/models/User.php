<?php

class User extends MY_Model{
    
    const DB_TABLE = 'users';
    const DB_TABLE_PK = 'user_id';

    public $username;
    public $password;
    public $confidentiality_level_id;
    public $employee_id;
    public $active;

    public function employee(){
        $this->load->model('employee');
        $employee = new Employee();
        $employee->load($this->employee_id);
        return $employee;
    }

    public function confidentiality(){
        $this->load->model('human_resource/employee_confidentiality_level');
        $level = new Employee_confidentiality_level();
        $level->load($this->confidentiality_level_id);
        return $level;
    }

    public function permissions(){
        $this->load->model('user_permission');
        $sql = 'SELECT * FROM users_permissions WHERE user_id = '.$this->{$this::DB_TABLE_PK};
        $permissions = $this->db->query($sql)->result();
        $options = [];
        foreach($permissions as $permission){
            $user_permission = new User_permission();
            $user_permission->load($permission->user_permission_id);
            $options[$permission->user_permission_id] = $user_permission;
        }
        return $options;
    }

    public function permission_ids(){
        $permissions = $this->permissions();
        $permission_ids = [];
        foreach($permissions as $permission){
            $permission_ids[] = $permission->permission_id;
        }
        return $permission_ids;
    }

    public function permission_privilege_ids()
    {
        $user_permissions = $this->permissions();
        $permission_privilege_ids = [];
        foreach($user_permissions as $user_permission){
            $user_permission_privileges = $user_permission->user_permission_privileges();
            foreach ($user_permission_privileges as $user_permission_privilege){
                $permission_privilege_ids[$user_permission_privilege->{$user_permission_privilege::DB_TABLE_PK}] = $user_permission_privilege->permission_privilege_id;
            }
        }
        return $permission_privilege_ids;
    }

    public function permission_previlege_names(){
        $sql = 'SELECT user_permission_privileges.id, privilege FROM user_permission_privileges
                LEFT JOIN permission_privileges ON user_permission_privileges.permission_privilege_id = permission_privileges.id
                LEFT JOIN users_permissions ON user_permission_privileges.user_permission_id = users_permissions.user_permission_id
                WHERE user_id ='.$this->{$this::DB_TABLE_PK};
        $results = $this->db->query($sql)->result();
        $this->load->model('user_permission_privilege');
        foreach ($results as $result) {
            $user_permission_privilege = new User_permission_privilege();
            $user_permission_privilege->load($result->id);
            $privilege_names[$result->id] = $user_permission_privilege->permission_privilege()->privilege;
        }
        return $privilege_names;
    }


    public function permission_names(){
        $permissions = $this->permissions();
        $permission_names = [];
        foreach($permissions as $permission){
            $permission_names[] = $permission->permission()->name;
        }
        return $permission_names;
    }

    public function delete_permissions(){
        $permissions = $this->permissions();
        foreach($permissions as $permission){
            $permission->delete();
        }
    }


}

