<?php

class purchase_order_asset_item extends MY_Model
{

    const DB_TABLE = 'purchase_order_asset_items';
    const DB_TABLE_PK = 'id';

    public $order_id;
    public $asset_item_id;
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

    public function asset_item()
    {
        $this->load->model('asset_item');
        $asset_item = new Asset_item();
        $asset_item->load($this->asset_item_id);
        return $asset_item;
    }

    public function received_quantity(){
        $sql = 'SELECT COUNT(grn_asset_sub_location_histories.id) AS quantity_received FROM grn_asset_sub_location_histories
                LEFT JOIN asset_sub_location_histories ON grn_asset_sub_location_histories.asset_sub_location_history_id = asset_sub_location_histories.id
                LEFT JOIN assets ON asset_sub_location_histories.asset_id = assets.id
                LEFT JOIN goods_received_notes ON grn_asset_sub_location_histories.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                WHERE asset_item_id = '.$this->asset_item_id.' AND purchase_order_id = '.$this->order_id;

        $query = $this->db->query($sql);
        return $query->row()->quantity_received;
    }

    public function unreceived_quantity(){
        return $this->quantity - $this->received_quantity();
    }
}

