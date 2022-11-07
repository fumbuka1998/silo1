<?php

class Currency extends MY_Model{
    
    const DB_TABLE = 'currencies';
    const DB_TABLE_PK = 'currency_id';

    public $currency_name;
    public $symbol;
    public $is_native;

    public function name_and_symbol(){
        return $this->currency_name.' ('.$this->symbol.')';
    }

    public function rate_to_native($date = null){
        $date = is_null($date) ? date('Y-m-d') : $date;
        $sql = 'SELECT exchange_rate FROM exchange_rate_updates
                WHERE currency_id = "'.$this->{$this::DB_TABLE_PK}.'" AND update_date <= "'.$date.'"
                ORDER BY update_date DESC LIMIT 1';
        $query = $this->db->query($sql);
        return $query->num_rows() > 0 ? $query->row()->exchange_rate : false;
    }

    public function dropdown_options($nbsp = false){
        $currencies = $this->get();
        $options = [];
        if ($nbsp) {
            $options[''] = '&nbsp;';
        }
        foreach ($currencies as $currency){
            $options[$currency->{$this::DB_TABLE_PK}] = $currency->name_and_symbol();
        }
        return $options;

    }

    public function currencies_list($limit, $start, $keyword, $order){
        //order string
        $order_string = dataTable_order_string(['currency_name','symbol'],$order,'currency_name');

        $where = '';
        if($keyword != ''){
            $where .= ' currency_name LIKE "%'.$keyword.'%" OR symbol LIKE "%'.$keyword.'%" ';
        }

        $currencies = $this->get($limit,$start,$where,$order_string);
        $rows = [];

        foreach($currencies as $currency){
            $data['currency'] = $currency;
            $rows[] = [
                $currency->currency_name,
                $currency->symbol,
                number_format($currency->rate_to_native(),3),
                $this->load->view('finance/settings/currency_list_actions',$data,true)
            ];
        }
        $records_filtered = $this->count_rows($where);
        $records_total = $this->count_rows();

        $json = [
            "recordsTotal" => $records_total,
            "recordsFiltered" => $records_filtered,
            "data" => $rows
        ];
        return json_encode($json);
    }

    public function convert($source_currency_id, $destination_currency_id, $amount){
        $source_currency = new self();
        $source_currency->load($source_currency_id);
        $source_to_native = $source_currency->rate_to_native();

        $destination_currency = new self();
        $destination_currency->load($destination_currency_id);
        $destination_to_native = $destination_currency->rate_to_native();
        return $amount * $source_to_native/$destination_to_native;
    }
}

