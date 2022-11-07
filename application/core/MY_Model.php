<?php

class MY_Model extends CI_Model {

    const DB_TABLE = 'abstract';
    const DB_TABLE_PK = 'abstract';

    /**
     *Create record
     */
    private function insert(){
        if($this->db->insert($this::DB_TABLE, $this)){
            $this->{$this::DB_TABLE_PK} = $this->db->insert_id();
            return true;
        } else {
            return FALSE;
        }
    }

    /**
     * Update record
     */
    private function update(){

        if($this->db->update($this::DB_TABLE, $this, array($this::DB_TABLE_PK  => $this->{$this::DB_TABLE_PK}))){
            return true;
        } else {
            return FALSE;
        }
    }

    /**
     * Populate from an array or standard class
     * @param mixed $row
     */
    public function populate($row){
        foreach ($row as $key => $value){
            $this->$key = $value;
        }
    }

    /**
     * Load from the database
     * @param int $id
     */
    public function load($id){

        /*
         * select from the database
         */
        $query = $this->db->get_where($this::DB_TABLE, array($this::DB_TABLE_PK => $id));

        /**
         * Populate to an object
         */
        if(!empty($query->row())){
            $this->populate($query->row());
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete record
     */
    public function delete(){
        $this->db->delete($this::DB_TABLE, array( $this::DB_TABLE_PK => $this->{$this::DB_TABLE_PK}));
        unset($this->{$this::DB_TABLE_PK});
    }

    /**
     * Save the record
     */
    public function save(){
        return isset($this->{$this::DB_TABLE_PK}) ? $this->update() : $this->insert();
    }

    /**
     * Get an array of Models with an optional limit, offset
     * @param $limit Optional.
     * @param $offset Optional,if set requires $limit
     * @return an array of Models populated by database, keyed by PK
     */

    public function get($limit = 0, $offset = 0,$where=[],$order = ''){

        $this->db->select();
        $this->db->from($this::DB_TABLE);
        if($limit > 0){
            $this->db->limit($limit , $offset);
        }
        if(!empty($where)){
            $this->db->where($where);
        }

        if(!empty($order)){
            $this->db->order_by($order);
        }

        $query = $this->db->get();
        $ret_val = array();
        $class = get_class($this);
        foreach($query->result() as $row){
            $model = new $class;
            $model->populate($row);
            $ret_val[$row->{$this::DB_TABLE_PK}] = $model;
        }

        return $ret_val;
    }

    /**
     * @param array $name Optional
     * Tbis will return total number of records available a database
     * @return type int
     */
    public function count_rows($where = []){
        $this->db->select($this::DB_TABLE_PK)->from($this::DB_TABLE);
        if(!empty($where)){
            $this->db->where($where);
        }
        return $this->db->count_all_results();
    }

}

