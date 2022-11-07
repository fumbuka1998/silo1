<?php

class Client extends MY_Model{
    
    const DB_TABLE = 'clients';
    const DB_TABLE_PK = 'client_id';

    public $client_name;
    public $phone;
    public $alternative_phone;
    public $email;
    public $address;
    public $account_id;

    public function clients_list($limit, $start, $keyword, $order){

       $order_string = dataTable_order_string(['client_name','phone','alternative_phone','email','address'],$order,'client_name');

       $where = '';
       if($keyword != ''){
           $where .= 'client_name LIKE "%'.$keyword.'%" OR phone LIKE "%'.$keyword.'%" OR alternative_phone LIKE "%'.$keyword.'%"  OR email LIKE "%'.$keyword.'%"  OR address LIKE "%'.$keyword.'%" ';
       }

       $clients = $this->client->get($limit,$start,$where,$order_string);
       $rows = [];
       foreach($clients as $client){
           $rows[] = [
               anchor(base_url('clients/profile/'.$client->{$client::DB_TABLE_PK}),$client->client_name),
               $client->phone,
               $client->alternative_phone,
               $client->email,
               $client->address
           ];
       }
       $records_filtered = $this->client->count_rows($where);
       $records_total = $this->client->count_rows();
       $json = [
           "recordsTotal" => $records_total,
           "recordsFiltered" => $records_filtered,
           "data" => $rows
       ];
       return json_encode($json);
    }
    
    public function account()
    {
        $this->load->model('account');
        $account = new Account();
        $account->load($this->account_id);
        return $account;
    }
    
    public function number_of_projects(){
        $this->load->model('project');
        $where['project_id'] = $this->{$this::DB_TABLE_PK};
        return $this->project->count_rows($where);
    }

    public function clients_options()
    {
        $options[''] = '&nbsp;';
        $clients = $this->get(0,0,'','client_name');
        foreach($clients as $client){
            $options[$client->{$client::DB_TABLE_PK}] = $client->client_name;
        }
        return $options;
    }

    public function accounts_dropdown_options()
    {
        $account = $this->account();
        return [$account->{$account::DB_TABLE_PK} => $account->account_name];
    }

    public function clients_debt($currency_id = null){
        $sql = 'SELECT * FROM (
                  SELECT CONCAT("Sale_",stock_sales_asset_items.id,"_asset") AS debted_item, "Stock Sales" AS debt_nature, CONCAT(asset_name,"/",LPAD(asset_sub_location_histories.asset_id, 4, 0)," - ","SALE/",LPAD(stock_sales.id, 4, 0)) AS corresponding_alias 
                  FROM stock_sales_asset_items
                  LEFT JOIN asset_sub_location_histories ON stock_sales_asset_items.asset_sub_location_history_id = asset_sub_location_histories.id
                  LEFT JOIN assets ON asset_sub_location_histories.asset_id = assets.id
                  LEFT JOIN asset_items ON assets.asset_item_id = asset_items.id
                  LEFT JOIN stock_sales ON stock_sales_asset_items.stock_sale_id = stock_sales.id
                  WHERE currency_id = '.$currency_id.'
                  AND client_id = '.$this->{$this::DB_TABLE_PK}.' 
                  
                  UNION
                  SELECT CONCAT("Sale_",stock_sales_material_items.id,"_matl") AS debted_item, "Stock Sales" AS debt_nature, CONCAT(item_name," - ","SALE/",LPAD(stock_sales.id, 4, 0)) AS corresponding_alias 
                  FROM stock_sales_material_items
                  LEFT JOIN material_items ON stock_sales_material_items.material_item_id = material_items.item_id
                  LEFT JOIN stock_sales ON stock_sales_material_items.stock_sale_id = stock_sales.id
                  WHERE currency_id = '.$currency_id.'
                  AND client_id = '.$this->{$this::DB_TABLE_PK}.' 
                  
                  UNION
                  SELECT CONCAT("Service_",maintenance_service_items.item_id,"_serv") AS debted_item_id, "Maintenance Services" AS debt_nature, CONCAT(maintenance_service_items.description," - ","SVC/",LPAD(maintenance_services.service_id, 4, 0)) AS corresponding_alias 
                  FROM maintenance_service_items 
                  LEFT JOIN maintenance_services ON maintenance_service_items.service_id = maintenance_services.service_id
                  WHERE currency_id = '.$currency_id.'
                  AND client_id = '.$this->{$this::DB_TABLE_PK}.'
                 
                  UNION
                  SELECT CONCAT("Certificate_",id,"_cert") AS debted_item_id, "Project Certificates" AS debt_nature, certificate_number AS corresponding_alias
                  FROM project_certificates
                  LEFT JOIN projects ON project_certificates.project_id = projects.project_id
                  WHERE currency_id = '.$currency_id.'
                  AND client_id = '.$this->{$this::DB_TABLE_PK}.'
                  
                ) AS clients_debts';

        $query = $this->db->query($sql);
        $debted_items = $query->result();
        $debt_categories = [
            'Maintenance Services',
            'Stock Sales',
            'Project Certificates'
        ];
        $options[] = '&nbsp;';
        foreach($debt_categories as $category){
            $this->load->model(['maintenance_service_item','stock_sales_asset_item','stock_sales_material_item','project_certificate']);
            foreach($debted_items as $item){
                $exploded_item = explode('_',$item->debted_item);
                if($item->debt_nature == "Maintenance Services"){
                    $maintenance_service = new Maintenance_service_item();
                    $maintenance_service->load($exploded_item[1]);
                    $item_balance = $maintenance_service->amount() - $maintenance_service->invoiced_amount();
                } else if($item->debt_nature == "Stock Sales") {
                    if($exploded_item[2] == "matl"){
                        $sold_material = new Stock_sales_material_item();
                        $sold_material->load($exploded_item[1]);
                        $item_balance = $sold_material->amount() - $sold_material->invoiced_amount();
                    } else {
                        $sold_asset = new Stock_sales_asset_item();
                        $sold_asset->load($exploded_item[1]);
                        $item_balance = $sold_asset->price - $sold_asset->invoiced_amount();
                    }

                } else {
                    $project_certificate = new Project_certificate();
                    $project_certificate->load($exploded_item[1]);
                    $item_balance = $project_certificate->certified_amount - $project_certificate->invoiced_amount();
                }

                if($item->debt_nature == $category && $item_balance > 0){
                    $options[$category][$item->debted_item] = $item->corresponding_alias;
                }
            }
        }
        return $options;
    }


}

