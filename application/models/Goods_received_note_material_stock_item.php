<?php

class Goods_received_note_material_stock_item extends MY_Model{
    
    const DB_TABLE = 'goods_received_note_material_stock_items';
    const DB_TABLE_PK = 'item_id';

    public $grn_id;
    public $stock_id;
    public $rejected_quantity;
    public $remarks;

    public function stock_item()
    {
        $this->load->model('material_stock');
        $stock_item = new Material_stock();
        $stock_item->load($this->stock_id);
        return $stock_item;
    }

    public function goods_received_note()
    {
        $this->load->model('goods_received_note');
        $grn = new Goods_received_note();
        $grn->load($this->grn_id);
        return $grn;
    }

    public function order_material_item(){
        $sql = 'SELECT purchase_order_material_items.item_id FROM material_stocks
                LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                LEFT JOIN purchase_orders ON purchase_order_grns.purchase_order_id = purchase_orders.order_id
                LEFT JOIN purchase_order_material_items ON purchase_orders.order_id = purchase_order_material_items.order_id
                WHERE purchase_order_material_items.material_item_id = material_stocks.item_id
                 AND goods_received_note_material_stock_items.item_id = "'.$this->{$this::DB_TABLE_PK}.'"
                LIMIT 1
                ';
        $query = $this->db->query($sql);
        if($query->num_rows()) {
            $this->load->model('purchase_order_material_item');
            $order_material_item = new Purchase_order_material_item();
            $order_material_item->load($query->row()->item_id);
            return $order_material_item;
        } else {
            return false;
        }

    }

    public function purchase_order_material_item(){
        $sql = 'SELECT item_id FROM purchase_order_material_items
                LEFT JOIN purchase_order_material_item_grn_items ON purchase_order_material_items.item_id = purchase_order_material_item_grn_items.purchase_order_material_item_id
                WHERE goods_received_note_item_id = '.$this->{$this::DB_TABLE_PK}.' LIMIT 1';
        $query = $this->db->query($sql);
        if($query->num_rows() > 0) {
            $item_id = $query->row()->item_id;
            $this->load->model('purchase_order_material_item');
            $item = new Purchase_order_material_item();
            $item->load($item_id);
            return $item;
        } else {
            return false;
        }
    }

    public function external_material_transfer_item(){
        $sql = 'SELECT external_material_transfer_items.item_id FROM external_material_transfer_items
                LEFT JOIN external_material_transfers ON external_material_transfer_items.transfer_id = external_material_transfers.transfer_id
                LEFT JOIN external_material_transfer_grns ON external_material_transfers.transfer_id = external_material_transfer_grns.transfer_id
                LEFT JOIN goods_received_notes ON external_material_transfer_grns.grn_id = goods_received_notes.grn_id
                LEFT JOIN goods_received_note_material_stock_items ON goods_received_notes.grn_id = goods_received_note_material_stock_items.grn_id
                LEFT JOIN material_stocks ON goods_received_note_material_stock_items.stock_id = material_stocks.stock_id
                WHERE external_material_transfer_items.material_item_id = material_stocks.item_id 
                AND goods_received_note_material_stock_items.item_id = '.$this->{$this::DB_TABLE_PK}.'
                AND goods_received_note_material_stock_items.stock_id = '.$this->stock_id;

        $query = $this->db->query($sql);
        if($query->num_rows() > 0) {
            $this->load->model('external_material_transfer_item');
            $transfer_item = new External_material_transfer_item();
            $transfer_item->load($query->row()->item_id);
            return $transfer_item;
        } else {
            return false;
        }

    }

    public function retirement_material_item(){
        $sql = 'SELECT imprest_voucher_retirement_material_items.id FROM imprest_voucher_retirement_material_items
                LEFT JOIN imprest_voucher_retirements ON imprest_voucher_retirement_material_items.imprest_voucher_retirement_id = imprest_voucher_retirements.id
                LEFT JOIN imprest_voucher_retirement_grns ON imprest_voucher_retirements.id = imprest_voucher_retirement_grns.imprest_voucher_retirement_id
                LEFT JOIN goods_received_notes ON imprest_voucher_retirement_grns.grn_id = goods_received_notes.grn_id
                LEFT JOIN goods_received_note_material_stock_items ON goods_received_notes.grn_id = goods_received_note_material_stock_items.grn_id
                WHERE goods_received_note_material_stock_items.item_id = '.$this->{$this::DB_TABLE_PK}.'
                AND goods_received_note_material_stock_items.stock_id = '.$this->stock_id;
        $query = $this->db->query($sql);
        if($query->num_rows() > 0) {
            $this->load->model('imprest_voucher_retirement_material_item');
            $retirement_material_item = new Imprest_voucher_retirement_material_item();
            $retirement_material_item->load($query->row()->id);
            return $retirement_material_item;
        } else {
            return false;
        }

    }

    public function receiving_price(){
        $sql = 'SELECT COALESCE((material_stocks.price/(exchange_rate*purchase_order_grns.factor)),0) AS price FROM material_stocks
                LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                WHERE goods_received_note_material_stock_items.item_id = '.$this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        return $query->row()->price;
    }

}

