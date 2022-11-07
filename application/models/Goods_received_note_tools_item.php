<?php

class Goods_received_note_tools_item extends MY_Model{
    
    const DB_TABLE = 'goods_received_note_tools_items';
    const DB_TABLE_PK = 'id';

    public $grn_id;
    public $tool_type_id;
    public $quantity;
    public $price;
    public $remarks;

    public function tool_type()
    {
        $this->load->model('tool_type');
        $tool_type = new Tool_type();
        $tool_type->load($this->tool_type_id);
        return $tool_type;
    }

    public function grn()
    {
        $this->load->model('goods_received_note');
        $grn = new Goods_received_note();
        $grn->load($this->grn_id);
        return $grn;
    }

}

