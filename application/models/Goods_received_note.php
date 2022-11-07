<?php

class Goods_received_note extends MY_Model
{

    const DB_TABLE = 'goods_received_notes';
    const DB_TABLE_PK = 'grn_id';

    public $location_id;
    public $receive_date;
    public $receiver_id;
    public $comments;

    public function grn_number()
    {
        return ($this->is_site_grn() ? 'DN/' : 'GRN/') . add_leading_zeros($this->{$this::DB_TABLE_PK});
    }

    public function creator()
    {
        return 'receiver_id';
    }

    public function is_site_grn()
    {
        $location = $this->location();
        return !is_null($location->project_id);
    }

    public function location()
    {
        $this->load->model('inventory_location');
        $location = new Inventory_location();
        $location->load($this->location_id);
        return $location;
    }

    public function purchase_order_grns($limit, $start, $keyword, $order)
    {

        $order_column = $this->input->post('order')[0]['column'];
        $order_dir = $this->input->post('order')[0]['dir'];
        switch ($order_column) {
            case 0;
                $order_column = 'receive_date';
                break;
            case 1;
                $order_column = 'grn_id';
                break;
            case 2;
                $order_column = 'stakeholder_name';
                break;
            case 3;
                $order_column = 'location_name';
                break;
            case 5;
                $order_column = 'comments';
                break;
            default:
                $order_column = 'receive_date';
        }

        $order_string = $order_column . ' ' . $order_dir;

        $sql = 'SELECT grn_id,receive_date,goods_received_notes.comments,location_name, goods_received_notes.location_id, stakeholder_name,stakeholders.stakeholder_id
                FROM goods_received_notes
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                LEFT JOIN purchase_orders ON purchase_order_grns.purchase_order_id = purchase_orders.order_id
                LEFT JOIN stakeholders ON purchase_orders.stakeholder_id = stakeholders.stakeholder_id
                LEFT JOIN inventory_locations ON goods_received_notes.location_id = inventory_locations.location_id
                WHERE purchase_order_grns.purchase_order_id IS NOT NULL 
            ';

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();

        if ($keyword != '') {
            $sql .= ' AND (grn_id = "%' . $keyword . '%" OR receive_date LIKE "%' . $keyword . '%"  OR goods_received_notes.comments LIKE "%' . $keyword . '%" OR purchase_order_id LIKE "%' . $keyword . '%" OR stakeholder_name LIKE "%' . $keyword . '%"
                OR grn_id IN(
                    SELECT goods_received_note_id FROM purchase_order_grns
                    LEFT JOIN purchase_orders ON purchase_order_grns.purchase_order_id = purchase_orders.order_id
                    LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
                    LEFT JOIN projects ON project_purchase_orders.project_id = projects.project_id
                    WHERE project_name LIKE "%' . $keyword . '%"
                )
            ) ';
        }

        $query = $this->db->query($sql);
        $records_filtered = $query->num_rows();

        $sql .= " ORDER BY " . $order_string . " LIMIT " . $limit . " OFFSET " . $start;

        $query = $this->db->query($sql);

        $results = $query->result();
        $rows = [];
        foreach ($results as $row) {
            $grn = new Goods_received_note();
            $grn->load($row->grn_id);
            $data['reffering_object'] = $data['grn'] = $grn;
            $data['attachments'] = $grn->attachments();
            $rows[] = [
                custom_standard_date($row->receive_date),
                $grn->grn_number(),
                anchor(base_url('stakeholders/stakeholder_profile/' . $row->stakeholder_id), $row->stakeholder_name),
                check_permission('Inventory') ? anchor(base_url('inventory/location_profile/' . $row->location_id), $row->location_name) : $row->location_name,
                $grn->reference(),
                $grn->cost_center_name(),
                $this->load->view('procurements/purchase_orders/grn_list_actions', $data, true)
            ];
        }
        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function material_items()
    {
        $this->load->model('goods_received_note_material_stock_item');
        $where['grn_id'] = $this->{$this::DB_TABLE_PK};
        return $this->goods_received_note_material_stock_item->get(0, 0, $where);
    }

    public function imprest_voucher_retirement_grn()
    {
        $this->load->model('imprest_voucher_retirement_grn');
        $junctions = $this->imprest_voucher_retirement_grn->get(1, 0, ['grn_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junctions) ? array_shift($junctions) : false;
    }

    public function source_reference()
    {
        $order_junction = $this->purchase_order_grn();
        $transfer_grn = $this->transfer_grn();
        $imprest_voucher_retirement_grn =  $this->imprest_voucher_retirement_grn();
        $imprest_grn = $this->imprest_grn();
        if ($order_junction) {
            $reference = 'P.O/' . add_leading_zeros($order_junction->purchase_order_id);
        } else if ($transfer_grn) {
            $reference = 'EXT/' . add_leading_zeros($transfer_grn->transfer_id);
        } else if ($imprest_voucher_retirement_grn) {
            $reference = 'IMPV/' . add_leading_zeros($this->imprest_voucher_retirement_grn()->imprest_voucher_id());
        } else if ($imprest_grn) {
            $reference = 'IMP/' . add_leading_zeros($this->imprest_grn()->imprest_id);
        } else {
            $reference = 'PD/' . add_leading_zeros($this->unprocured_grn()->delivery_id);
        }
        return $reference;
    }

    public function source_name()
    {
        $order_junction = $this->purchase_order_grn();
        $transfer_grn = $this->transfer_grn();
        $imprest_voucher_retirement_grn =  $this->imprest_voucher_retirement_grn();
        $imprest_grn = $this->imprest_grn();
        if ($order_junction) {
            $source_name = $order_junction->purchase_order()->stakeholder()->stakeholder_name;
        } else if ($transfer_grn) {
            $source_name = $this->transfer_grn()->transfer()->source()->location_name;
        } else if ($imprest_voucher_retirement_grn) {
            $source_name = 'Cash Purchase/Imprest Voucher No.' . add_leading_zeros($this->imprest_voucher_retirement_grn()->imprest_voucher_id());
        } else if ($imprest_grn) {
            $source_name = 'Cash Purchase/Imprest No.' . add_leading_zeros($this->imprest_grn()->imprest_id);
        } else {
            $source_name = 'Delivery No.' . add_leading_zeros($this->unprocured_grn()->delivery_id);
        }
        return $source_name;
    }

    public function reference()
    {
        $purchase_order = $this->purchase_order();
        $transfer = $this->transfer();
        $imprest_voucher_retirement_grn =  $this->imprest_voucher_retirement_grn();
        $imprest_grn = $this->imprest_grn();
        if ($purchase_order) {
            $reference = 'P.O No. ' . $purchase_order->order_number() . (
                ($requisition = $purchase_order->requisition()) ? '/Req No.' . $requisition->requisition_number() : '');
        } else if ($transfer) {
            $reference = $this->transfer()->transfer_number();
        } else if ($imprest_voucher_retirement_grn) {
            $imprest_voucher = $this->imprest_voucher_retirement_grn()->imprest_voucher_retirement()->imprest_voucher();
            $reference = 'Imprest Voucher No: ' . $imprest_voucher->imprest_voucher_number();
            $requisition = $imprest_voucher->requisition_approval()->requisition();
            $reference .= '/Req No.' . add_leading_zeros($requisition->{$requisition::DB_TABLE_PK});
        } else if ($imprest_grn) {
            $imprest = $this->imprest_grn()->imprest();
            $pv = $imprest->payment_voucher();
            $reference = 'Imprest No: ' . $imprest->imprest_number() . '/PV No ' . $pv->payment_voucher_number();
            $reference .= '/Req No.' . add_leading_zeros($pv->requisition_approval_payment_voucher()->requisition_approval()->requisition_id);
        } else {
            $reference = 'Project Delivery No.' . add_leading_zeros($this->unprocured_grn()->delivery()->delivery_number());
        }
        return $reference;
    }

    public function cost_center_name()
    {
        $order = $this->purchase_order();
        $transfer = $this->transfer();
        $imprest_voucher_retirement_grn =  $this->imprest_voucher_retirement_grn();
        $imprest_grn = $this->imprest_grn();
        if ($order) {
            $cost_center_name = $order->cost_center_name();
        } else if ($transfer) {
            $cost_center_name = $transfer->project()->project_name;;
        } else if ($imprest_voucher_retirement_grn) {
            $cost_center_name = $this->imprest_voucher_retirement_grn()->imprest_voucher_retirement()->imprest_voucher()->cost_center_name();
        } else if ($imprest_grn) {
            $cost_center_name = $this->imprest_grn()->imprest()->cost_center_name();
        } else {
            $cost_center_name = $this->unprocured_grn()->delivery()->project()->project_name;
        }
        return $cost_center_name;
    }

    public function grn_asset_sub_location_histories()
    {
        $this->load->model('grn_asset_sub_location_history');
        return $this->grn_asset_sub_location_history->get(0, 0, ['grn_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function asset_items()
    {
        $transfer_grn = $this->transfer_grn();
        $unprocured_grn = $this->unprocured_grn();
        if ($transfer_grn) {
            $grn_items = $this->grn_asset_sub_location_histories();
            $assets = [];
            foreach ($grn_items as $item) {
                $assets[] = $item->asset_sub_location_history()->asset();
            }
            return $assets;
        } else if ($unprocured_grn) {
            $sql = 'SELECT asset_name, unprocured_delivery_asset_items.item_id AS temp,  COALESCE(rejected_quantity,0) AS rejected_quantity, 
                    COUNT(assets.id) AS quantity,asset_sub_location_histories.book_value AS price 
                    FROM asset_sub_location_histories
                    LEFT JOIN assets ON asset_sub_location_histories.asset_id = assets.id
                    LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id
                    LEFT JOIN grn_asset_sub_location_histories ON asset_sub_location_histories.id = grn_asset_sub_location_histories.asset_sub_location_history_id
                    LEFT JOIN goods_received_notes ON grn_asset_sub_location_histories.grn_id = goods_received_notes.grn_id
                    LEFT JOIN unprocured_delivery_grns ON goods_received_notes.grn_id = unprocured_delivery_grns.grn_id
                    LEFT JOIN unprocured_delivery_asset_items ON asset_items.id = unprocured_delivery_asset_items.asset_item_id
                    LEFT JOIN goods_received_note_asset_item_rejects ON goods_received_notes.grn_id = goods_received_note_asset_item_rejects.grn_id
                    WHERE unprocured_delivery_asset_items.delivery_id = unprocured_delivery_grns.delivery_id AND goods_received_notes.grn_id = ' . $this->{$this::DB_TABLE_PK} . '
                    GROUP BY asset_items.id,unprocured_delivery_asset_items.item_id
                    ';
            $query = $this->db->query($sql);
            return $query->result();
        } else {
            $sql = 'SELECT asset_name, purchase_order_asset_items.id AS temp,  COALESCE(rejected_quantity,0) AS rejected_quantity, 
                    COUNT(assets.id) AS quantity,asset_sub_location_histories.book_value AS price 
                    FROM asset_sub_location_histories
                    LEFT JOIN assets ON asset_sub_location_histories.asset_id = assets.id
                    LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id
                    LEFT JOIN grn_asset_sub_location_histories ON asset_sub_location_histories.id = grn_asset_sub_location_histories.asset_sub_location_history_id
                    LEFT JOIN goods_received_notes ON grn_asset_sub_location_histories.grn_id = goods_received_notes.grn_id
                    LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                    LEFT JOIN purchase_order_asset_items ON asset_items.id = purchase_order_asset_items.asset_item_id
                    LEFT JOIN goods_received_note_asset_item_rejects ON purchase_order_asset_items.id = goods_received_note_asset_item_rejects.purchase_order_asset_item_id
                    WHERE order_id = purchase_order_grns.purchase_order_id AND goods_received_notes.grn_id = ' . $this->{$this::DB_TABLE_PK} . '
                    GROUP BY asset_items.id,purchase_order_asset_items.id
                    ';


            $query = $this->db->query($sql);
            return $query->result();
        }
    }

    public function receiver()
    {
        $this->load->model('employee');
        $receiver = new Employee();
        $receiver->load($this->receiver_id);
        return $receiver;
    }

    public function delete_junctions()
    {
        foreach ($this->material_items() as $item) {
            $this->db->delete('material_stocks', ['stock_id' => $item->stock_id]);
        }
        foreach ($this->grn_asset_sub_location_histories() as $grn_ast_hist) {
            $asset_sub_hist = $grn_ast_hist->asset_sub_location_history();
            $this->db->delete('asset_sub_location_histories', ['id' => $asset_sub_hist->id]);
            $this->db->delete('assets', ['id' => $asset_sub_hist->asset_id]);
        }
        $this->db->delete('purchase_order_grns', ['goods_received_note_id ' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('grn_received_services', ['grn_id' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('goods_received_note_asset_item_rejects', ['grn_id' => $this->{$this::DB_TABLE_PK}]);
        $this->db->delete('goods_received_note_material_stock_items', ['grn_id' => $this->{$this::DB_TABLE_PK}]);
    }

    public function purchase_order_grn()
    {
        $this->load->model('purchase_order_grn');
        $order_junctions = $this->purchase_order_grn->get(1, 0, ['goods_received_note_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($order_junctions) ? array_shift($order_junctions) : false;
    }

    public function unprocured_grn()
    {
        $this->load->model('unprocured_delivery_grn');
        $junctions = $this->unprocured_delivery_grn->get(1, 0, ['grn_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junctions) ? array_shift($junctions) : false;
    }

    public function imprest_grn()
    {
        $this->load->model('imprest_grn');
        $junctions = $this->imprest_grn->get(1, 0, ['grn_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($junctions) ? array_shift($junctions) : false;
    }

    public function transfer_grn()
    {
        $this->load->model('external_material_transfer_grn');
        $transfer_grns = $this->external_material_transfer_grn->get(1, 0, ['grn_id' => $this->{$this::DB_TABLE_PK}]);
        return !empty($transfer_grns) ? array_shift($transfer_grns) : false;
    }

    public function purchase_order()
    {
        $purchase_order_grn = $this->purchase_order_grn();
        return $purchase_order_grn ? $purchase_order_grn->purchase_order() : false;
    }

    public function transfer()
    {
        $transfer_grn = $this->transfer_grn();
        return $transfer_grn ? $transfer_grn->transfer() : false;
    }

    public function material_value()
    {
        $sql = 'SELECT COALESCE(SUM(quantity*price),0) AS material_value FROM material_stocks
                LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                WHERE grn_id = ' . $this->{$this::DB_TABLE_PK};
        return doubleval($this->db->query($sql)->row()->material_value);
    }

    public function asset_value()
    {
        $sql = 'SELECT COALESCE(SUM(book_value),0) AS asset_value FROM asset_sub_location_histories
                LEFT JOIN grn_asset_sub_location_histories ON asset_sub_location_histories.id = grn_asset_sub_location_histories.asset_sub_location_history_id
                WHERE grn_id = ' . $this->{$this::DB_TABLE_PK};
        return doubleval($this->db->query($sql)->row()->asset_value);
    }

    public function service_items()
    {
        $sql = 'SELECT description, grn_received_services.* FROM grn_received_services
                LEFT JOIN purchase_order_service_items ON grn_received_services.purchase_order_service_item_id = purchase_order_service_items.id
                WHERE grn_received_services.grn_id = ' . $this->{$this::DB_TABLE_PK} . '
                ';
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function service_value()
    {
        $sql = 'SELECT COALESCE(SUM(received_quantity*rate),0) AS service_value FROM grn_received_services
                WHERE grn_id = ' . $this->{$this::DB_TABLE_PK} . '';
        return doubleval($this->db->query($sql)->row()->service_value);
    }

    public function material_value_in_order_currency()
    {
        $purchase_order_grn = $this->purchase_order_grn();
        return $this->material_value() / ($purchase_order_grn->exchange_rate * $purchase_order_grn->factor);
    }

    public function asset_value_in_order_currency()
    {
        $purchase_order_grn = $this->purchase_order_grn();
        return $this->asset_value() / ($purchase_order_grn->exchange_rate * $purchase_order_grn->factor);
    }

    public function fob()
    {
        $grn_material_items = $this->material_items();
        $grn_asset_items = $this->grn_asset_sub_location_histories();
        $fob = 0;

        foreach ($grn_material_items as $item) {
            $fob += $item->receiving_price() * $item->stock_item()->quantity;
        }

        foreach ($grn_asset_items as $item) {
            $fob += $item->receiving_price();
        }

        return $fob;
    }

    public function cif()
    {
        $cif = $this->fob();
        $order_grn = $this->purchase_order_grn();
        if ($order_grn) {
            $cif += $order_grn->freight + $order_grn->insurance + $order_grn->other_charges;
        }
        return $cif;
    }

    public function invoiced_amount()
    {
        $sql = 'SELECT COALESCE(SUM(amount),0) AS invoiced_amount FROM invoices
                LEFT JOIN grn_invoices ON invoices.id = grn_invoices.invoice_id
                WHERE grn_id = ' . $this->{$this::DB_TABLE_PK};
        $query = $this->db->query($sql);
        return $query->row()->invoiced_amount;
    }

    public function uninvoiced_amount()
    {
        return $this->cif() - $this->invoiced_amount();
    }

    public function sub_location()
    {
        $this->load->model([
            'goods_received_note_material_stock_item',
            'material_stock', 'sub_location',
            'grn_asset_sub_location_history',
            'asset_sub_location_history',
            'grn_received_service'
        ]);
        $stock_id = $this->goods_received_note_material_stock_item->get(1, 0, ['grn_id' => $this->{$this::DB_TABLE_PK}]);
        $grn_services = $this->grn_received_service->get(0, 0, ['grn_id' => $this->{$this::DB_TABLE_PK}]);
        $grn_asset = $this->grn_asset_sub_location_history->get(0, 0, ['grn_id' => $this->{$this::DB_TABLE_PK}]);

        if ($stock_id) {
            $id = array_shift($stock_id);
            $material_stok = new Material_stock();
            $material_stok->load($id->stock_id);
            $sub_location = new Sub_location();
            $sub_location->load($material_stok->sub_location_id);
        } else if ($grn_services) {
            $grn_service = array_shift($grn_services);
            $grn_service_id = $grn_service->{$grn_service::DB_TABLE_PK};
            $received_service = new Grn_received_service();
            $received_service->load($grn_service_id);
            $sub_location = $received_service->sub_location();
        } else if ($grn_asset) {
            $history_id = $this->grn_asset_sub_location_history->get(1, 0, ['grn_id' => $this->{$this::DB_TABLE_PK}]);
            $id = array_shift($history_id);
            $asset_history = new Asset_sub_location_history();
            $asset_history->load($id->asset_sub_location_history_id);
            $sub_location = new Sub_location();
            $sub_location->load($asset_history->sub_location_id);
        }

        return $sub_location;
    }

    public function attachments()
    {
        $this->load->model('procurement_attachment');
        $where = ' (reffering_id =' . $this->{$this::DB_TABLE_PK} . ' AND  reffering_to="GRN")';
        $purchase_order = $this->purchase_order();
        if ($purchase_order) {
            $where .= ' OR (reffering_id =' . $purchase_order->order_id . ' AND reffering_to = "ORDER")';
        }
        $junctions = $this->procurement_attachment->get(0, 0, $where);
        $attachments = [];
        foreach ($junctions as $junction) {
            $attachments[] = $junction->attachment();
        }
        return $attachments;
    }
}
