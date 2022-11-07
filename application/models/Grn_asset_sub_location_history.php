<?php

/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 24/04/2018
 * Time: 18:08
 */
class Grn_asset_sub_location_history extends MY_Model
{

    const DB_TABLE = 'grn_asset_sub_location_histories';
    const DB_TABLE_PK = 'id';

    public $grn_id;
    public $asset_sub_location_history_id;

    public function asset_sub_location_history()
    {
        $this->load->model('asset_sub_location_history');
        $asset_sub_location_history = new Asset_sub_location_history();
        $asset_sub_location_history->load($this->asset_sub_location_history_id);
        return $asset_sub_location_history;
    }

    public function receiving_price(){
        $sql = 'SELECT COALESCE((asset_sub_location_histories.book_value/(exchange_rate*purchase_order_grns.factor)),0) AS price FROM asset_sub_location_histories
                LEFT JOIN grn_asset_sub_location_histories ON asset_sub_location_histories.id = grn_asset_sub_location_histories.asset_sub_location_history_id
                LEFT JOIN goods_received_notes ON grn_asset_sub_location_histories.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                WHERE grn_asset_sub_location_histories.id = '.$this->{$this::DB_TABLE_PK};

        $query = $this->db->query($sql);
        return $query->row()->price;
    }


}

