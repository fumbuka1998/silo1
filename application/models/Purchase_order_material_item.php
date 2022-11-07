<?php

class Purchase_order_material_item extends MY_Model{
    
    const DB_TABLE = 'purchase_order_material_items';
    const DB_TABLE_PK = 'item_id';

    public $order_id;
    public $material_item_id;
    public $quantity;
    public $price;
    public $remarks;

    public function purchase_order()
    {
        $this->load->model('purchase_order');
        $purchase_order = new Purchase_order();
        $purchase_order->load($this->order_id);
        return $purchase_order;
    }

    public function material_item()
    {
        $this->load->model('material_item');
        $material_item = new Material_item();
        $material_item->load($this->material_item_id);
        return $material_item;
    }

    public function received_quantity(){
        $sql = 'SELECT COALESCE(SUM(material_stocks.quantity),0) AS received_quantity FROM material_stocks
                LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                LEFT JOIN purchase_order_material_item_grn_items ON goods_received_note_material_stock_items.item_id = purchase_order_material_item_grn_items.goods_received_note_item_id
                WHERE purchase_order_material_item_id = '.$this->{$this::DB_TABLE_PK};
        $query = $this->db->query($sql);
        return $query->row()->received_quantity;
    }

    public function unreceived_quantity(){
        $balance = $this->quantity - $this->received_quantity();
        return $balance > 0 ? $balance : 0;
    }

    public function grn_item_ids(){
        $sql = 'SELECT goods_received_note_material_stock_items.item_id FROM goods_received_note_material_stock_items
                LEFT JOIN material_stocks ON goods_received_note_material_stock_items.stock_id = material_stocks.stock_id
                LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                WHERE purchase_order_id = "'.$this->order_id.'" AND material_stocks.item_id = "'.$this->material_item_id.'"
        ';
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function grn_items(){
        $ids = $this->grn_item_ids();
        $this->load->model('goods_received_note_material_stock_item');
        $items = [];
        foreach ($ids as $id){
            $item = new Goods_received_note_material_stock_item();
            $item->load($id->item_id);
            $items[] = $item;
        }
        return $items;
    }

    public function matched_items(){
        $where = ' order_id = '.$this->order_id.' AND material_item_id = '.$this->material_item_id.' AND item_id != '.$this->{$this::DB_TABLE_PK};
        return $this->get(0,0,$where);
    }

    public function grn_items_junctions(){
        $this->load->model('purchase_order_material_item_grn_item');
        return $this->purchase_order_material_item_grn_item->get(0,0,['purchase_order_material_item_id' => $this->{$this::DB_TABLE_PK}]);
    }

}

