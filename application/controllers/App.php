<?php

class App extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        check_login();
        $this->load->model(['requisition', 'purchase_order', 'purchase_order_payment_request','currency']);
        $data['purchase_orders'] = $this->purchase_order->purchase_orders_on_dashboard();
        $data['requisitions'] = $this->requisition->all_requisitions_on_dashboard();
        $data['exchange_currencies'] = $this->currency->get(0,0,['is_native' => '0']);
        $this->load->view('dashboard', $data);
    }

    public function requisitions()
    {
        $this->load->model('requisition');
        $posted_params = dataTable_post_params();
        echo $this->requisition->requisiton_lists_on_dashboard($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);

    }

    public function purchase_orders($orders_for = null,$holder_id = null){

        $this->load->model('purchase_order');
        $params = dataTable_post_params();
        echo $this->purchase_order->purchase_orders_list($params['limit'], $params['start'], $params['keyword'], $params['order'],$orders_for,$holder_id, true);

    }

    public function login()
    {
        $where = [
            'username' => $this->input->post('username'),
            'password' => sha1(md5($this->input->post('password')))
        ];


        $api_key = $this->config->item('crm_api_key');
        $crm_url = $this->config->item('crm_url');
        $this->load->library('MY_Curl');
        $curl = new MY_Curl();
        $curl->setPost(
            array(
                'api_key' => $api_key
            )
        );
        $curl->setUserAgent($this->input->user_agent());
        $curl->createCurl($crm_url.'API/check_subscription');
        $response = json_decode($curl->__tostring());



        $this->load->model('user');
        $users = $this->user->get(0, 0, $where,'user_id DESC');
        $user = array_shift($users);
        if (!empty($user)) {

            $due_invoices = array();
            if(!empty($response)){
                foreach ($response as $invoice){
                    $number_of_days = intval((time() - strtotime($invoice->invoice_date))/(60*60*24));
                    $termination_days = 21;
                    if($number_of_days > $termination_days){
                        redirect(base_url('app/login'));
                    } else if($number_of_days > 7) {
                        $due_invoices[] = $invoice;
                    }
                }
            }

            $employee = $user->employee();
            $permissions = $user->permission_names();
            $permission_privileges = $user->permission_previlege_names();
            $department = $employee->department();
            $userdata = [
                "employee_id" => $employee->employee_id,
                "employee_name" => $employee->full_name(),
                "department_name" => $department->department_name,
                'employee_email' => $employee->email,
                'employee_phone' => $employee->phone,
                'department_id' => $department->{$department::DB_TABLE_PK},
                'dp_path' => $employee->avatar_path(),
                'permissions' => $permissions,
                'permission_privileges' => $permission_privileges,
                'confidentiality'=>$user->confidentiality()->level_name,
                'job_position_id' => $employee->position_id,
                'has_project' => $employee->has_project(),
                'due_invoices' => $due_invoices
            ];
            $this->session->set_userdata($userdata);
            system_log('Login');
            redirect(base_url());
        }

        $this->load->view('login_form');
    }

    public function test_graph()
    {
        $this->load->view('test_view');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        system_log('Logout');
        redirect(base_url('app/login'));
    }

    public function error_404()
    {
        redirect(base_url());
    }

    //These are methods used for data entry errors corrections and modifications

    public function test(){
        $dataPOST = simplexml_load_string(file_get_contents('php://input'));
        inspect_object($dataPOST);
        echo $dataPOST->AMOUNT;
        exit;
        $sql = 'SELECT SQL_CALC_FOUND_ROWS * 
                FROM employees LIMIT 5';
        $query = $this->db->query($sql);
        inspect_object($query->result());

        $sql = 'SELECT FOUND_ROWS() AS num_results';
        $query = $this->db->query($sql);
        inspect_object($query->result());

        $sql = 'DELETE goods_received_note_material_stock_items,material_stocks FROM goods_received_note_material_stock_items
                LEFT JOIN material_stocks ON goods_received_note_material_stock_items.stock_id = material_stocks.stock_id
                WHERE grn_id IN (223,232,180) AND material_stocks.item_id IN (651,81,1239,1240,1241,1242,1243)
                ';

        $sql = 'SELECT * FROM external_material_transfer_items WHERE material_item_id NOT IN(651,81,1239,1240,1241,1242,1243)
                AND transfer_id IN (95,119)
                ';

        $sql = 'SELECT * FROM material_stocks 
                LEFT JOIN goods_received_note_material_stock_items ON material_stocks.stock_id = goods_received_note_material_stock_items.stock_id
                WHERE goods_received_note_material_stock_items.grn_id IN (223,232,180) AND material_stocks.item_id IN(651,81,1239,1240,1241,1242,1243)';

        $sql = 'DELETE FROM purchase_order_grns WHERE grn_id = 180;
                DELETE FROM goods_received_notes WHERE grn_id = 180
                ';
    }

    public function update_requisition_approvals(){
        $this->load->model('requisition_approval_material_item');
        $items = $this->requisition_approval_material_item->get(0,0,[
           'source_type' => 'vendor',
            'vendor_id' => 14
        ]);

        foreach ($items as $item){
            $item->source_type = 'store';
            $item->vendor_id = null;
            $item->location_id = 1;
            $item->save();
        }
    }

    public function update_requisition_material_items(){
        $this->load->model('requisition_material_item');
        $items = $this->requisition_material_item->get(0,0,[
            'requested_vendor_id' => 14
        ]);

        foreach ($items as $item){
            $item->source_type = 'store';
            $item->requested_location_id = 1;
            $item->requested_vendor_id = null;
            $item->save();
        }
    }

    public function update_external_material_transfers(){
        $sql = 'SELECT DISTINCT project_id,transfer_id FROM external_material_transfer_items';
        $query = $this->db->query($sql);
        $results = $query->result();
        $this->load->model('external_material_transfer');
        foreach ($results as $row){
            $transfer = new External_material_transfer();
            $transfer->load($row->transfer_id);
            $transfer->project_id = $row->project_id;
            $transfer->save();
        }
    }

    public function populate_cost_center_purchase_orders(){
        $this->load->model(['cost_center_requisition','cost_center_purchase_order']);
        $cost_center_requisitions = $this->cost_center_requisition->get();
        foreach ($cost_center_requisitions as $cost_center_requisition){
            $sql = 'SELECT * FROM requisition_purchase_orders
                    WHERE requisition_id = '.$cost_center_requisition->requisition_id;
            $query = $this->db->query($sql);
            $purchase_order_junction = new Cost_center_purchase_order();
            $purchase_order_junction->cost_center_id = $cost_center_requisition->cost_center_id;
            if($query->num_rows() > 0) {
                $purchase_order_junction->purchase_order_id = $query->row()->purchase_order_id;
                $purchase_order_junction->save();
            }
        }
    }

    public function insert_company_details()
    {
        $company_details = get_company_details();
        inspect_object($company_details);

        $this->load->model('company_detail');
        $company_detail = new Company_detail();
        $company_detail->company_name = $company_details['company_name'];
        $company_detail->created_by = $this->session->userdata('employee_id');
        $company_detail->telephone = $company_details['telephone'];
        $company_detail->mobile = $company_details['mobile'];
        $company_detail->tin = $company_details['TIN'];
        $company_detail->vrn = $company_details['VRN'];
        $company_detail->fax = $company_details['fax'];
        $company_detail->email = $company_details['email'];
        $company_detail->address = $company_details['address'];
        $company_detail->save();
    }

    public function check_material_costs_prices(){
        $this->load->model(['material_cost','material_average_price']);
        $material_costs = $this->material_cost->get(0,0,['source_sub_location_id' => 16]);
        $items = [];
        foreach ($material_costs as $material_cost){
            $average_prices = $this->material_average_price->get(0,0,[
                'material_item_id' => $material_cost->material_item_id,
                'sub_location_id' => $material_cost->source_sub_location_id
            ],' average_price DESC ');

            foreach ($average_prices as $average_price){
                /*$difference = $average_price->average_price - $material_cost->rate;
                if($difference > 0 && $difference < 1) {
                    $material_cost->rate = $average_price->average_price;
                    $material_cost->save();
                }*/

                /*if($average_price->average_price <= $material_cost->rate/1.8) {
                    $average_price->average_price = $material_cost->rate;
                    $average_price->save();
                }*/

                /*if($average_price->average_price > $material_cost->rate) {
                     $material_cost->rate = $average_price->average_price;
                     $material_cost->save();
                }*/
            }

            $items[] = [
                'material_cost' => $material_cost,
                'average_prices' => $average_prices
            ];
        }
        $this->load->view('test_table',['items' => $items]);
    }

    public function check_material_stock_prices(){
        $this->load->model('material_stock');
        $data['material_stocks'] = $this->material_stock->get(0,0,[
            'sub_location_id' => 16
        ]);
        $this->load->view('test_table2',$data);
    }

/*

    public function check_material_costs_prices(){
        $this->load->model(['material_cost','material_average_price']);
        $material_costs = $this->material_cost->get(0,0,['source_sub_location_id' => 11]);
        $items = [];
        foreach ($material_costs as $material_cost){
            $average_prices = $this->material_average_price->get(0,0,[
                'material_item_id' => $material_cost->material_item_id,
                'sub_location_id' => $material_cost->source_sub_location_id
            ],' average_price DESC ');

            foreach ($average_prices as $average_price){
                $difference = $average_price->average_price - $material_cost->rate;
                if($difference > 0 && $difference < 1) {
                    $material_cost->rate = $average_price->average_price;
                    $material_cost->save();
                }
            }

            $items[] = [
                'material_cost' => $material_cost,
                'average_prices' => $average_prices
            ];
        }
        $this->load->view('test_table',['items' => $items]);
    }

    public function check_material_stock_prices(){
        $this->load->model('material_stock');
        $data['material_stocks'] = $this->material_stock->get(0,0,[
            'sub_location_id' => 11
        ]);
        $this->load->view('test_table2',$data);

    }*/

    public function update_grn_dates(){
        $sql = 'SELECT grn_id FROM goods_received_notes
  LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
  LEFT JOIN purchase_orders ON purchase_order_grns.purchase_order_id = purchase_orders.order_id
  LEFT JOIN project_purchase_orders ON purchase_orders.order_id = project_purchase_orders.purchase_order_id
WHERE goods_received_notes.location_id = 1 AND project_id = 15 AND receive_date > "2017-12-07"
              ';
        $query = $this->db->query($sql);
        $result = $query->result();
        $this->load->model(['goods_received_note','material_average_price']);
        foreach ($result as $row){
            $grn = new Goods_received_note();
            $grn->load($row->grn_id);
            $purchase_order = $grn->purchase_order();
            $date_received = $purchase_order->delivery_date;

            $grn->receive_date = $date_received;
            $grn->save();
            $material_items = $grn->material_items();
            foreach ($material_items as $item){
                $stock_item = $item->stock_item();
                $stock_item->date_received = $date_received;
                //$stock_item->save();
                $sql = 'SELECT average_price_id FROM material_average_prices
                        WHERE sub_location_id = 1 AND material_item_id = "'.$stock_item->item_id.'"  AND transaction_date > "2017-12-08" AND project_id = 15';

                $query = $this->db->query($sql);
                $result = $query->result();
                foreach ($result as $item){
                    $average_price = new Material_average_price();
                    $average_price->load($item->average_price_id);
                    $average_price->transaction_date = $date_received;
                    //$average_price->save();
                    echo 'Average Price Update '.$average_price->transaction_date.'<br/>';
                }
                echo 'Stock Item Updated '.$stock_item->material_item()->item_name.' <hr/>';
            }

        }
    }

    public function add_grn_items_to_order_item_junction(){
        $this->load->model('goods_received_note_material_stock_item');
        $sql = 'SELECT goods_received_note_material_stock_items.item_id FROM goods_received_note_material_stock_items
                LEFT JOIN goods_received_notes ON goods_received_note_material_stock_items.grn_id = goods_received_notes.grn_id
                LEFT JOIN purchase_order_grns ON goods_received_notes.grn_id = purchase_order_grns.goods_received_note_id
                LEFT JOIN material_stocks ON goods_received_note_material_stock_items.stock_id = material_stocks.stock_id
                WHERE purchase_order_grns.id IS NOT NULL AND goods_received_note_material_stock_items.item_id NOT IN(
                  SELECT goods_received_note_id FROM purchase_order_material_item_grn_items
                )
                ';
        $query = $this->db->query($sql);
        $results = $query->result();
        $this->load->model('purchase_order_material_item_grn_item');
        //echo $query->num_rows().'<hr/>';
        $updated = $skiped = 0;
        foreach ($results as $row){
            $grn_item = new Goods_received_note_material_stock_item();
            $grn_item->load($row->item_id);
            $material_stock = $grn_item->stock_item();
            $order_material_item = $grn_item->order_material_item();
            $received_quantity = $order_material_item->received_quantity();
            if($received_quantity == 0){
                $junction_entry = new Purchase_order_material_item_grn_item();
                $junction_entry->goods_received_note_item_id = $grn_item->{$grn_item::DB_TABLE_PK};
                $junction_entry->purchase_order_material_item_id = $order_material_item->{$order_material_item::DB_TABLE_PK};
                $junction_entry->save();
                $updated++;
            } else {
                $skiped++;
            }
        }
        echo "Inserted: ".$updated.'<hr/>Skipped: '.$skiped;
    }

    public function check_received_items(){
        $sql = 'SELECT main_table.item_id FROM purchase_order_material_items main_table
            WHERE (
              SELECT COALESCE(COUNT(item_id),0) FROM purchase_order_material_items
              WHERE purchase_order_material_items.order_id = main_table.order_id
              AND main_table.material_item_id = purchase_order_material_items.material_item_id
            ) > 1 ORDER BY `main_table`.`material_item_id`';

        $query = $this->db->query($sql);
        $results = $query->result();
        $this->load->model('purchase_order_material_item');
        foreach ($results as $row){
            $order_material_item = new Purchase_order_material_item();
            $order_material_item->load($row->item_id);
            $object = [
              'quantity_ordered' => $order_material_item->quantity,
              'order_material_id' => $order_material_item->order_id,
              'material_id' => $order_material_item->material_item_id,
              'grn_items' => $order_material_item->grn_item_ids()
            ];
            inspect_object($object);
            echo '<hr/>';
        }
    }

    public function check_order_received_items()
    {
        $sql = 'SELECT main_table.item_id FROM purchase_order_material_items main_table
                WHERE (
                  SELECT COALESCE(COUNT(item_id),0) FROM purchase_order_material_items
                  WHERE purchase_order_material_items.order_id = main_table.order_id
                  AND main_table.material_item_id = purchase_order_material_items.material_item_id
                ) > 1';
        $query = $this->db->query($sql);
        $orders_result = $query->result();
        $this->load->model(['purchase_order_material_item']);

        $order_material_items = [];
        foreach ($orders_result as $item) {
            $order_item = new Purchase_order_material_item();
            $order_item->load($item->item_id);
            $order_material_items[] = $order_item;
        }
        $this->load->view('test_table',['order_material_items' => $order_material_items]);
    }

    public function populate_vendor_accounts(){
        $this->load->model(['vendor','vendor_account']);
        $vendors = $this->vendor->get();
        foreach ($vendors as $vendor){
            $vendor_account = new Vendor_account();
            $vendor_account->account_id = $vendor->account_id;
            $vendor_account->vendor_id = $vendor->{$vendor::DB_TABLE_PK};
            $vendor_account->save();
        }
    }

    public function update_purchase_order_grns(){
        $this->load->model('purchase_order_grn');
        $data['order_grns'] = $order_grns = $this->purchase_order_grn->get();
        foreach ($order_grns as $order_grn) {
            $grn = $order_grn->grn();
            $cif = $grn->fob() + $order_grn->freight+$order_grn->insurance+$order_grn->other_charges;
            $cpf = $cif * 0.01 * $order_grn->cpf * $order_grn->exchange_rate;
            $rdl = $cif * 0.01 * $order_grn->rdl * $order_grn->exchange_rate;
            $import_duty = $cif * 0.01 * $order_grn->import_duty * $order_grn->exchange_rate;

            $order_grn->cpf = $cpf;
            $order_grn->rdl = $rdl;
            $order_grn->import_duty = $import_duty;
            $order_grn->vat = (($cif*$order_grn->exchange_rate)+$cpf+$rdl+$import_duty)*$order_grn->vat/100;
            $order_grn->save();
            $data['order_grns'][] = $order_grn;
        }
        $this->load->view('test_table3',$data);
    }

    public function merge_material_items()
    {
        //Material Stocks
        echo '<h2>MATERIAL STOCKS</h2>';
        $this->load->model('material_stock');
        $sn = 0;
        $material_stocks = $this->material_stock->get(0,0,['item_id' => 1371]);
        foreach ($material_stocks as $material_stock){
            $sn++;
            echo $sn.' : '.$material_stock->quantity.'<hr/>';
            $material_stock->item_id = 1077;
            $material_stock->save();
        }

        echo '<h2>REQUISITION MATERIAL ITEMS</h2>';
        $this->load->model('requisition_material_item');
        $requisition_material_items = $this->requisition_material_item->get(0,0,['material_item_id' => 1371]);
        foreach ($requisition_material_items as $requisition_material_item){
            $sn++;
            echo $sn.' : '.$requisition_material_item->requested_quantity.'<hr/>';
            $requisition_material_item->material_item_id = 1077;
            $requisition_material_item->save();
        }

        echo '<h2>PURCHASE ORDER MATERIAL ITEMS</h2>';

        $this->load->model('purchase_order_material_item');

        $order_material_items = $this->purchase_order_material_item->get(0,0,['material_item_id' => 1371]);
        foreach ($order_material_items as $order_material_item){
            $sn++;
            echo $sn.' : '.$order_material_item->quantity.'<hr/>';
            $order_material_item->material_item_id = 1077;
            $order_material_item->save();
        }
        echo '<h2>AVERAGE PRICES</h2>';

        $this->load->model('material_average_price');

        $average_prices = $this->material_average_price->get(0,0,['material_item_id' => 1371]);
        foreach ($average_prices as $average_price){
            $sn++;
            echo $sn.' : '.$average_price->average_price.' | '.$average_price->project_id.'<hr/>';
            $average_price->delete();
        }

        echo '<h2>EXTERNAL TRANSFER MATERIAL ITEMS</h2>';

        $this->load->model('external_material_transfer_item');

        $external_transfer_material_items = $this->external_material_transfer_item->get(0,0,['material_item_id' => 1371]);
        foreach ($external_transfer_material_items as $external_transfer_material_item){
            $sn++;
            echo $sn.' : '.$external_transfer_material_item->quantity.'<hr/>';
            $external_transfer_material_item->material_item_id = 1077;
            $external_transfer_material_item->save();
        }

        echo '<h2>MATERIAL COSTS</h2>';

        $this->load->model('material_cost');

        $material_costs = $this->material_cost->get(0,0,['material_item_id' => 1371]);
        foreach ($material_costs as $material_cost){
            $sn++;
            echo $sn.' : '.$material_cost->quantity.'<hr/>';
            $material_cost->material_item_id = 1077;
            $material_cost->save();
        }

        echo '<h2>MATERIAL BUDGETS</h2>';

        $this->load->model('material_budget');

        $material_budgets = $this->material_budget->get(0,0,['material_item_id' => 1371]);
        foreach ($material_budgets as $material_budget){
            $sn++;
            echo $sn.' : '.$material_budget->quantity.'<hr/>';
            $material_budget->material_item_id = 1077;
            $material_budget->save();
        }

    }

    public function insert_ztests(){
        $this->load->model('ztest');
        $name = $this->input->post('name');
        if(trim($name) != ''){
            $test = new Ztest();
            $test->name = $this->input->post('name');
            $test->age = $this->input->post('age');
            $test->size = $this->input->post('size');

            if($test->save()) {
                echo 'The object was saved to the database';
            }
        } else {
            $this->load->view('training/training_view');
        }
    }

    public function delete_ztests($id){
        $this->load->model('ztest');
        $test = new Ztest();
        $test->load($id);
        $test->delete();
        //inspect_object($test); exit;
    }

    public function remove_opening_stock(){
        $this->load->model('material_opening_stock');
        $where = [
            'sub_location_id = '
        ];
        $opening_stocks = $this->material_opening_stock->get();
        foreach ($opening_stocks as $opening_stock){
            $material_stock = $opening_stock->material_stock();
            $opening_stock->delete();
            $material_stock->delete();
        }
    }

    public function update_stock_prices()
    {
        $this->load->model(['material_stock','external_material_transfer',
            'goods_received_note_material_stock_item','external_material_transfer_grn','external_material_transfer_item','internal_material_transfer_item']
        );
        $stocks = $this->material_stock->get(0,0,['price' => 0]);
        foreach ($stocks as $stock){
            $material_item = $stock->material_item();
            $grn_items = $this->goods_received_note_material_stock_item->get(1,0,['stock_id' => $stock->{$stock::DB_TABLE_PK}]);
            if(!empty($grn_items)){
                $grn_id = array_shift($grn_items)->grn_id;
                $transfer_grns = $this->external_material_transfer_grn->get(1,0,['grn_id' => $grn_id]);
                if(!empty($transfer_grns)){
                    $transfer_items = $this->external_material_transfer_item->get(0,0,[
                        'material_item_id' => $stock->item_id,
                        'transfer_id' => array_shift($transfer_grns)->transfer_id
                    ]);

                    foreach ($transfer_items as $transfer_item){

                        echo 'EXTERNAL: ' .$price = $material_item->sub_location_average_price($transfer_item->source_sub_location_id,$transfer_item->project_id);
                        echo '<hr/>';
                        $transfer_item->price = $price;
                        $transfer_item->save();
                    }
                }

            } else {
                $transfer_items = $this->internal_material_transfer_item->get(1,0,['stock_id' => $stock->{$stock::DB_TABLE_PK}]);
                $transfer_item = array_shift($transfer_items);
                echo 'INTERNAL: '. $price = $material_item->sub_location_average_price($transfer_item->source_sub_location_id,$stock->project_id).'<hr/>';
            }
            $stock->price = $price;
            $stock->save();
        }
    }

    public function update_average_prices(){
        $this->load->model(['material_average_price','material_stock']);
        $average_prices = $this->material_average_price->get(0,0,['average_price' => 0]);
        foreach ($average_prices as $average_price){
            $where = [
              'sub_location_id' => $average_price->sub_location_id,
              'item_id' => $average_price->material_item_id,
            ];
            $matching_stocks = $this->material_stock->get(1,0,$where,' stock_id DESC ');
            foreach ($matching_stocks as $stock){
                echo '<hr/>'.$average_price->average_price = $stock->price;
            }
            $average_price->save();
        }
    }

    public function test_email()
    {
        $this->load->library('email');

        $this->email->from('derm@epmtz.com', 'EPM');
        $this->email->to('stunnardp@gmail.com.com');
        $this->email->cc('stunnaedward@gmail.com');
        $this->email->bcc('yohana.edward@bizytech.com');

        $this->email->subject('Email Test');
        $this->email->message('Testing the email class.');

        echo $this->email->send();
    }

    public function upgrade_client_accounts()
    {
        $this->load->model(['client','client_account']);
        $clients = $this->client->get();
        foreach ($clients as $client){
            $client_account = new Client_account();
            $client_account->account_id = $client->account_id;
            $client_account->client_id = $client->{$client::DB_TABLE_PK};
            $client_account->save();
        }
    }

    public function upgrade_project_accounts()
    {
        $this->load->model(['project','project_account']);
        $projects = $this->project->get(0,0,' petty_cash_account_id IS NOT NULL ');
        foreach ($projects as $project){
            $project_account = new Project_account();
            $project_account->project_id = $project->{$project::DB_TABLE_PK};
            $project_account->account_id = $project->petty_cash_account_id;
            $project_account->save();
        }
    }

    public function email_view(){
        $this->load->view('includes/email');
    }

    public function delete_cash_material_double_count(){

    }

    public function convert_other_requests_to_invoice_requests()
    {
        $this->load->model([
            'purchase_order_payment_request_cash_item',
            'purchase_order_payment_request_invoice_item',
            'purchase_order_payment_request_approval_cash_item',
            'purchase_order_payment_request_approval_invoice_item',
            'purchase_order_invoice',
            'invoice',
            'vendor_invoice'
        ]);
        $cash_items = $this->purchase_order_payment_request_cash_item->get(0,0,[
            'purchase_order_payment_request_id >= ' => 119,
            'purchase_order_payment_request_id <= ' => 125,
            ]);

        foreach ($cash_items as $cash_item){
            $payment_request = $cash_item->purchase_order_payment_request();
            $purchase_order = $payment_request->purchase_order();

            $payment_request_approvals = $payment_request->purchase_order_payment_request_approvals();

            foreach ($payment_request_approvals as $payment_request_approval) {

                $approved_cash_item = $cash_item->approved_item($payment_request_approval->{$payment_request_approval::DB_TABLE_PK});
                $invoice = new Invoice();
                $invoice->amount = $cash_item->requested_amount;
                $invoice->invoice_date = $payment_request->request_date;
                $invoice->reference = $cash_item->reference;
                $invoice->currency_id = $purchase_order->currency_id;
                $invoice->created_by = $payment_request->requester_id;
                $vendor = $purchase_order->vendor();
                if ($invoice->save()) {
                    $order_invoice = new Purchase_order_invoice();
                    $order_invoice->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                    $order_invoice->purchase_order_id = $payment_request->purchase_order_id;
                    $order_invoice->save();

                    $vendor_invoice = new Vendor_invoice();
                    $vendor_invoice->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                    $vendor_invoice->vendor_id = $purchase_order->vendor_id;
                    $vendor_invoice->save();
                }

                $invoice_item = new Purchase_order_payment_request_invoice_item();
                $invoice_item->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                $invoice_item->description = $cash_item->description;
                $invoice_item->purchase_order_payment_request_id = $cash_item->purchase_order_payment_request_id;
                $invoice_item->requested_amount = $cash_item->requested_amount;
                $invoice_item->remarks = $cash_item->description;
                $invoice_item->save();

                $approved_invoice_item = new Purchase_order_payment_request_approval_invoice_item();
                $approved_invoice_item->approved_amount = $approved_cash_item->approved_amount;
                $approved_invoice_item->purchase_order_payment_request_invoice_item_id = $invoice_item->{$invoice::DB_TABLE_PK};
                $approved_invoice_item->claimed_by = $vendor->vendor_name;
                $approved_invoice_item->purchase_order_payment_request_approval_id = $payment_request_approval->{$payment_request_approval::DB_TABLE_PK};
                $approved_invoice_item->save();
                $approved_cash_item->delete();
            }

            $cash_item->delete();




        }
    }

    public function new_payment_status_migrate()
    {
        $this->load->model(['invoice_payment_voucher','payment_voucher_item_approved_invoice_item']);
        $invoice_ids = 'SELECT invoice_id FROM purchase_order_payment_request_invoice_items
        LEFT JOIN purchase_order_payment_request_approval_invoice_items ON purchase_order_payment_request_invoice_items.id = purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id
        LEFT JOIN purchase_order_payment_requests ON purchase_order_payment_request_invoice_items.purchase_order_payment_request_id = purchase_order_payment_requests.id
        WHERE status = "APPROVED" ';

        $invoice_payment_vouchers = $this->invoice_payment_voucher->get(0,0,' invoice_id IN('.$invoice_ids.') ',' invoice_id');
        $html = '<table border="1px solid black" cellspacing="0">
                    <tr>
                        <th>Invoice ID</th><th>INV Amount</th><th>PV Items</th><th>Approval Items</th>
                    </tr>
';
        foreach ($invoice_payment_vouchers as $invoice_payment_voucher){
            $invoice = $invoice_payment_voucher->invoice();
            $payment_voucher_items = $invoice_payment_voucher->payment_voucher()->payment_voucher_items();

            $sql = 'SELECT purchase_order_payment_request_approval_invoice_items.* FROM purchase_order_payment_request_approval_invoice_items
                    LEFT JOIN purchase_order_payment_request_invoice_items ON purchase_order_payment_request_approval_invoice_items.purchase_order_payment_request_invoice_item_id = purchase_order_payment_request_invoice_items.id
                    WHERE invoice_id = '.$invoice_payment_voucher->invoice_id;

            $query = $this->db->query($sql);
            $pr_approval_items = $query->result();

            $html .= '<tr>
                <td>'.$invoice_payment_voucher->invoice_id.'</td><td>'.number_format($invoice->amount).'</td>
                <td>
                <table width="100%"  border="1px solid black" cellspacing="0">
                <tr>
                <th>PV No</th><th>PV ITEM ID</th><th>Amount</th><th>Paid To</th>
</tr>';
            foreach ($payment_voucher_items as $payment_voucher_item){
                $html .= '<tr '.($invoice->amount != $payment_voucher_item->amount ? ' style="background-color: red;" ' : '' ).'>
                <td>'.$payment_voucher_item->payment_voucher_id.'</td>
                <td>'.$payment_voucher_item->{$payment_voucher_item::DB_TABLE_PK}.'</td>
                <td>'.number_format($payment_voucher_item->amount).'</td>
                <td>'.$payment_voucher_item->debit_account()->account_name.'</td>
</tr>';
                foreach ($pr_approval_items as $pr_approval_item){
                    if($pr_approval_item->approved_amount == $payment_voucher_item->amount){
                        $junction = new Payment_voucher_item_approved_invoice_item();
                        $junction->payment_voucher_item_id = $payment_voucher_item->{$payment_voucher_item::DB_TABLE_PK};
                        $junction->purchase_order_payment_request_approval_invoice_item_id = $pr_approval_item->id;
                        $junction->save();
                    }
                }
            }
            $html .= '
</table>
</td><td>
                <table width="100%"  border="1px solid black" cellspacing="0">
                <tr>
                <th>Item ID</th><th>Approval ID</th><th>Amount</th>
</tr>';
            foreach ($pr_approval_items as $pr_approval_item){
                $html .= '<tr>
                <td>'.$pr_approval_item->id.'</td>
                <td>'.$pr_approval_item->purchase_order_payment_request_approval_id.'</td>
                <td>'.number_format($pr_approval_item->approved_amount).'</td>
</tr>';
            }
            $html .= '
</table>
</td>
            </tr>';




        }
        $html .= '</table>';
        echo $html;
    }

    /*public function convert_old_payments_to_new(){
        $this->load->model(['purchase_order_payment_request_approval_payment_voucher']);
        $purchase_order_payment_request_approval_payment_vouchers = $this->purchase_order_payment_request_approval_payment_voucher->get();

        foreach ($purchase_order_payment_request_approval_payment_vouchers as $approval_payment_voucher){
            $payment_voucher = $approval_payment_voucher->payment_voucher();
            $payment_voucher_items = $payment_voucher->payment_voucher_items();
            $approval = $approval_payment_voucher->purchase_order_payment_request_approval();
            $invoice_items = $approval->invoice_items();

            $html = '<br/>'.$approval_payment_voucher->{$approval_payment_voucher::DB_TABLE_PK}.'<hr/><table  border="1px solid black" cellspacing="0">
                        <tr>
                        <th>Invoice ID</th><th>INVOICEITEMID</th><th>INVOICEAMOUNT</th><th>PVITEMS</th>
                        </tr>';
            foreach ($invoice_items as $invoice_item){
                $html .= '<tr>
                    <td>'.$invoice_item->purchase_order_payment_request_invoice_item()->invoice_id.'</td>
                        <td>'.$invoice_item->{$invoice_item::DB_TABLE_PK}.'</td><td>'.$invoice_item->approved_amount.'</td>
                        <td>';
                        $html .= '<table border="1px solid black" cellspacing="0">
                <tr>
                    <th>PVID</th><th>PVITEMID</th><th>PVITEM AMOUNT</th>
</tr>';
                foreach ($payment_voucher_items as $payment_voucher_item) {
                    $html .= '<tr><td>'.$payment_voucher_item->payment_voucher_id.'</td>
                    <td>' . $payment_voucher_item->{$payment_voucher_item::DB_TABLE_PK} . '</td>
                    <td>' . $payment_voucher_item->amount . '</td></tr>';
                }
                $html .= '</table>';
                $html .= '</td>
                </tr>';
            }

            $html .= '</table>';

            if(count($payment_voucher_items) > 0) {
                echo $html;
            }

        }
    }*/
    public function export_db(){
            $this->load->dbutil();

    // Backup your entire database and assign it to a variable
            $backup = $this->dbutil->backup(['format'      => 'zip']);

            $db_name = $this->db->database . '_' . strftime('%Y%m%d%H%M.zip');
            $kaboom = explode('_', $db_name);
            $ftp_backup_folder = $kaboom[1];

            $this->load->helper('file');
            $file_path = './backups/' . $db_name;
            write_file($file_path, $backup);


            $this->load->library('ftp');

            $config['hostname'] = 'ams-node5.websitehostserver.net';
            $config['username'] = 'epmtzbackups@yohgates.com';
            $config['password'] = 'epmtzbackups@123';
            $config['debug'] = TRUE;

            $this->ftp->connect($config);

            $this->ftp->upload($file_path, '/'.$ftp_backup_folder.'/'.$db_name, 'auto', 0775);

            $this->ftp->close();

            $this->load->helper('download');
            force_download($db_name, $backup);
        }

    public function backup_db(){
        $this->load->dbutil();

// Backup your entire database and assign it to a variable
        $backup = $this->dbutil->backup(['format'      => 'zip']);

        $db_name = $this->db->database.'_'.strftime('%Y%m%d%H%M.zip');

        $kaboom = explode('_',$db_name);
        $ftp_backup_folder = $kaboom[1];

        $this->load->helper('file');
        $file_path = './backups/'.$db_name;
        write_file($file_path, $backup);


        $this->load->library('ftp');

        $config['hostname'] = 'ams-node5.websitehostserver.net';
        $config['username'] = 'epmtzbackups@yohgates.com';
        $config['password'] = 'epmtzbackups@123';
        $config['debug'] = TRUE;

        $this->ftp->connect($config);

        $this->ftp->upload($file_path, '/'.$ftp_backup_folder.'/'.$db_name, 'auto', 0775);

        $this->ftp->close();
    }

    public function curl_test()
    {


        //Initialise the cURL var
        $ch = curl_init();

        //Get the response from cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //Set the Url
        curl_setopt($ch, CURLOPT_URL, base_url('app/curl_uploaded_file'));
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('User-Agent: Opera/9.80 (Windows NT 6.2; Win64; x64) Presto/2.12.388 Version/12.15',
            'Referer: '.base_url('app/curl_test'),
            'Content-Type: multipart/form-data')
        );


        curl_setopt($ch, CURLOPT_POST, true); // enable posting

        //Create a POST array with the file in it
        $postData = array(
            'file[0]' => new CURLFile('/Applications/XAMPP/xamppfiles/htdocs/epm/images/logo.png','image/png','testpic'),
        );

        inspect_object($postData);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        // Execute the request
        $response = curl_exec($ch);
        echo $response;
    }

    public function curl_uploaded_file(){
        inspect_object($_FILES);
        inspect_object($_POST);
    }

    public function ticket_list_simulation()
    {
        echo '
            
        ';
    }

    public function insert_average_prices_for_missing()
    {
        $this->load->model(['material_stock','material_average_price']);
        $sql = 'SELECT * FROM material_stocks ';

        $query = $this->db->query($sql);
        $results = $query->result();

        $sn = 0;
        foreach ($results as $material_stock){
            $matching_average_prices = $this->material_average_price->get(1,0,[
                'sub_location_id' => $material_stock->sub_location_id,
                'project_id' => $material_stock->project_id,
                'material_item_id' => $material_stock->item_id
            ]);
            if(empty($matching_average_prices)) {
                $sn++;
                $material_average_price = new Material_average_price();
                $material_average_price->average_price = $material_stock->price;
                $material_average_price->transaction_date = $material_stock->date_received;
                $material_average_price->datetime_updated = $material_stock->date_received;
                $material_average_price->project_id = $material_stock->project_id;
                $material_average_price->sub_location_id = $material_stock->sub_location_id;
                $material_average_price->material_stock_id = $material_stock->stock_id;
                $material_average_price->material_item_id = $material_stock->item_id;
                $material_average_price->save();
            }
        }
        echo $sn;
    }

    public function fix_37288()
    {
        $this->load->model(['material_average_price','material_cost','material_stock']);
            $sql = 'SELECT material_stocks.*,item_name,category_id FROM material_stocks
                    LEFT JOIN material_items ON material_stocks.item_id = material_items.item_id
                    WHERE price = 37288';

            $query = $this->db->query($sql);
            $main_stocks = $query->result();

            $html = '';
            foreach ($main_stocks as $main_stock){
               $html .= '<hr/><b>'.$main_stock->category_id.': '.$main_stock->item_name.'</b>';
                $sql1 = 'SELECT * FROM material_stocks 
                WHERE item_id = '.$main_stock->item_id.' AND price != 37288 AND price > 0 AND stock_id != '.$main_stock->stock_id;
                $sql2 = ' AND project_id '.(is_null($main_stock->project_id) ? ' IS NULL ' : ' = '.$main_stock->project_id);
                $sql3 = ' ORDER BY date_received LIMIT 1';
                $query = $this->db->query($sql1.$sql2.$sql3);
                $sub_stocks = $query->result();
                if(!empty($sub_stocks)) {
                    $sub_stock = array_shift($sub_stocks);
                    $sql = 'UPDATE material_stocks SET price = '.$sub_stock->price.' WHERE stock_id = '.$main_stock->stock_id;
                    $query = $this->db->query($sql);

                    $sql = "DELETE FROM material_average_prices 
                      
                    WHERE average_price = 37288 AND sub_location_id = {$main_stock->sub_location_id} 
                    AND material_item_id = {$main_stock->item_id} ";
                    $this->db->query($sql);

                    $material_average_price = new Material_average_price();
                    $material_average_price->average_price = $sub_stock->price;
                    $material_average_price->project_id = $main_stock->project_id;
                    $material_average_price->sub_location_id = $main_stock->sub_location_id;
                    $material_average_price->material_item_id = $main_stock->item_id;
                    $material_average_price->datetime_updated = datetime();
                    $material_average_price->transaction_date = $main_stock->date_received;
                   $material_average_price->save();

                } else {
                    $query = $this->db->query($sql1.$sql3);
                    $sub_stocks = $query->result();

                    if(!empty($sub_stocks)) {
                        $sub_stock = array_shift($sub_stocks);
                        $sql = 'UPDATE material_stocks SET price = ' . $sub_stock->price . ' WHERE stock_id = ' . $main_stock->stock_id;
                        $query = $this->db->query($sql);

                        $sql = "DELETE FROM material_average_prices 
                      
                    WHERE average_price = 37288 AND sub_location_id = {$main_stock->sub_location_id} 
                    AND material_item_id = {$main_stock->item_id} ";
                        $this->db->query($sql);

                        $material_average_price = new Material_average_price();
                        $material_average_price->average_price = $sub_stock->price;
                        $material_average_price->project_id = $main_stock->project_id;
                        $material_average_price->sub_location_id = $main_stock->sub_location_id;
                        $material_average_price->material_item_id = $main_stock->item_id;
                        $material_average_price->datetime_updated = datetime();
                        $material_average_price->transaction_date = $main_stock->date_received;
                        $material_average_price->save();
                    }

                }

                if(isset($material_average_price) && $material_average_price->average_price > 0) {
                    $material_costs = $this->material_cost->get(0, 0, [
                        'material_item_id' => $main_stock->item_id,
                        'rate' => $material_average_price->average_price
                        ]);

                    foreach ($material_costs as $material_cost){
                        $material_cost->rate = $material_average_price->average_price;
                        echo ''.$material_cost->save().'<br/>';
                    }
                } else {
                    echo 'Zero Price<br/>';
                }



            }

            $material_costs = $this->material_cost->get(0,0,['rate' => 37288]);
            $sn = 0;
            foreach ($material_costs as $material_cost){
                $material_item = $material_cost->material();
                if($material_item->category_id != 3) {
                    $average_price = $material_item->sub_location_average_price($material_cost->source_sub_location_id, $material_cost->project_id);
                    if($average_price != 37288){
                        $sn++;
                        $material_cost->rate = $average_price;
                        $material_cost->save();
                        echo '<br/>' . $material_item->item_name;

                    } else {
                        $material_stocks = $this->material_stock->get(1,0,[
                            'price != ' => 37288,
                            'project_id' => $material_cost->project_id,
                            'item_id' => $material_cost->material_item_id
                        ],' date_received DESC');

                        if(!empty($material_stocks)){
                            $material_cost->rate = array_shift($material_stocks)->price;
                            $material_cost->save();
                        } else {
                            $material_stocks = $this->material_stock->get(1,0,[
                                'price != ' => 37288,
                                'item_id' => $material_cost->material_item_id
                            ],' date_received DESC');
                            if(!empty($main_stocks)){
                                $material_cost->rate = array_shift($material_stocks)->price;
                                $material_cost->save();
                            }
                        }
                    }
                }
            }

            //echo $html;
    }

    public function offset_order_payments(){
        $today = date('Y-m-d');
        $sn = 0;
        $employee_id = $this->session->userdata('employee_id');
        $this->load->model([
            'purchase_order_payment_request',
            'purchase_order_payment_request_invoice_item',
            'purchase_order',
            'invoice',
            'purchase_order_payment_request_approval',
            'purchase_order_payment_request_approval_invoice_item',
            'payment_voucher',
            'payment_voucher_item',
            'vendor_invoice',
            'purchase_order_invoice',
            'invoice_payment_voucher',
            'payment_voucher_item_approved_invoice_item',
            'vendor'
        ]);

        $order_ids = [108];
        echo count($order_ids).'<br>';
        foreach ($order_ids as $order_id){
            $purchase_order = new Purchase_order();
            $purchase_order->load($order_id);
            $currency = $purchase_order->currency();
            $balance_due = $purchase_order->amount_due();
            $vendor = $purchase_order->vendor();

            $invoiced_amount = $purchase_order->invoiced_amount();

            $invoice = new Invoice();
            $invoice->currency_id = $purchase_order->currency_id;

            if($invoiced_amount > 0){
                $invoice->amount = $purchase_order->uninvoiced_amount();
            } else {
                $invoice->amount = $balance_due;
            }

            if($balance_due > 0) {
                $invoice->invoice_date = $today;
                $invoice->reference = 'OFFSET INVOICE AUTHORISED BY MR ARNOLD';
                $invoice->created_by = $this->session->userdata('employee_id');
                if($invoice->save()){

                    $order_invoice = new Purchase_order_invoice();
                    $order_invoice->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                    $order_invoice->purchase_order_id = $order_id;
                    if($order_invoice->save()){

                        $vendor_invoice = new Vendor_invoice();
                        $vendor_invoice->vendor_id = $vendor->{$vendor::DB_TABLE_PK};
                        $vendor_invoice->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                        if($vendor_invoice->save()) {
                            $payment_request = new Purchase_order_payment_request();
                            $payment_request->purchase_order_id = $purchase_order->{$purchase_order::DB_TABLE_PK};
                            $payment_request->request_date = date('Y-m-d');
                            $payment_request->currency_id = $purchase_order->currency_id;
                            $payment_request->remarks = 'OFFSET PAYMENT ORDERED BY MR ARNOLD';
                            $payment_request->approval_module_id = 3;
                            $payment_request->requester_id = $this->session->userdata('employee_id');
                            $payment_request->finalizer_id = $this->session->userdata('employee_id');
                            $payment_request->status = 'APPROVED';

                            if ($payment_request->save()) {
                                $pr_invoice_item = new Purchase_order_payment_request_invoice_item();
                                $pr_invoice_item->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                                $pr_invoice_item->purchase_order_payment_request_id = $payment_request->{$payment_request::DB_TABLE_PK};
                                $pr_invoice_item->remarks = 'OFFSET PAYMENT ORDERED BY MR ARNOLD';
                                $pr_invoice_item->requested_amount = $invoice->amount;
                                if ($pr_invoice_item->save()) {
                                    $pr_approval = new Purchase_order_payment_request_approval();
                                    $pr_approval->purchase_order_payment_request_id = $payment_request->{$payment_request::DB_TABLE_PK};
                                    $pr_approval->created_by = $this->session->userdata('employee_id');
                                    $pr_approval->approval_chain_level_id = 15;
                                    $pr_approval->is_final = 1;
                                    $pr_approval->comments = 'APPROVED AS A BY PASS';
                                    $pr_approval->approval_date = $today;
                                    if ($pr_approval->save()) {
                                        $pr_approval_invoice_item = new Purchase_order_payment_request_approval_invoice_item();
                                        $pr_approval_invoice_item->purchase_order_payment_request_approval_id = $pr_approval->{$pr_approval::DB_TABLE_PK};
                                        $pr_approval_invoice_item->purchase_order_payment_request_invoice_item_id = $pr_invoice_item->{$pr_invoice_item::DB_TABLE_PK};
                                        $pr_approval_invoice_item->approved_amount = $pr_invoice_item->requested_amount;
                                        if ($pr_approval_invoice_item->save()) {
                                            $pv = new Payment_voucher();
                                            $pv->currency_id = $payment_request->currency_id;
                                            $pv->credit_account_id = 270;
                                            $pv->reference = 'OFFSET PAYMENT ';
                                            $pv->employee_id = $employee_id;
                                            $pv->remarks = 'OFFSET PAYMENT AUTHORISED BY MD: ARNOLD ' . $purchase_order->order_number();
                                            $pv->exchange_rate = $currency->rate_to_native();
                                            $pv->payment_date = $today;
                                            $pv->payee = $vendor->vendor_name;
                                            if ($pv->save()) {
                                                $pv_item = new Payment_voucher_item();
                                                $pv_item->payment_voucher_id = $pv->{$pv::DB_TABLE_PK};
                                                $pv_item->amount = $balance_due;
                                                $pv_item->description = $invoice->reference;
                                                $pv_item->debit_account_id = $vendor->account_id;

                                                if ($pv_item->save()) {
                                                    $invoice_pv = new Invoice_payment_voucher();
                                                    $invoice_pv->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                                                    $invoice_pv->payment_voucher_id = $pv->{$pv::DB_TABLE_PK};
                                                    if ($invoice_pv->save()) {
                                                        $sn++;
                                                        echo '<hr/>'.$sn.': '.$purchase_order->order_number();

                                                        $pviai = new Payment_voucher_item_approved_invoice_item();
                                                        $pviai->payment_voucher_item_id = $pv_item->{$pv_item::DB_TABLE_PK};
                                                        $pviai->purchase_order_payment_request_approval_invoice_item_id = $pr_approval_invoice_item->{$pr_approval_invoice_item::DB_TABLE_PK};
                                                        $pviai->save();
                                                    }
                                                }
                                            }
                                        }
                                    }


                                }
                            }
                        }
                    }

                }



            } else {
                echo $balance_due.'<br/>';
            }
        }
    }

    public function offset_order_payments_108(){
        $today = date('Y-m-d');
        $sn = 0;
        $employee_id = $this->session->userdata('employee_id');
        $this->load->model([
            'purchase_order_payment_request',
            'purchase_order_payment_request_invoice_item',
            'purchase_order',
            'invoice',
            'purchase_order_payment_request_approval',
            'purchase_order_payment_request_approval_invoice_item',
            'payment_voucher',
            'payment_voucher_item',
            'vendor_invoice',
            'purchase_order_invoice',
            'invoice_payment_voucher',
            'payment_voucher_item_approved_invoice_item',
            'vendor'
        ]);

        $order_ids = [108];
        echo count($order_ids).'<br>';
        foreach ($order_ids as $order_id){
            $purchase_order = new Purchase_order();
            $purchase_order->load($order_id);
            $currency = $purchase_order->currency();
            $balance_due = $purchase_order->amount_due();
            $vendor = $purchase_order->vendor();

            $invoiced_amount = $purchase_order->invoiced_amount();

            $invoice = new Invoice();
            $invoice->currency_id = $purchase_order->currency_id;

            if($invoiced_amount > 0){
                $invoice->amount = $purchase_order->uninvoiced_amount();
            } else {
                $invoice->amount = $balance_due;
            }

            $invoice->amount = 164544.9;

            if($invoice->amount > 0) {
                $invoice->invoice_date = $today;
                $invoice->reference = 'OFFSET INVOICE AUTHORISED BY MR ARNOLD';
                $invoice->created_by = $this->session->userdata('employee_id');
                if($invoice->save()){

                    $order_invoice = new Purchase_order_invoice();
                    $order_invoice->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                    $order_invoice->purchase_order_id = $order_id;
                    if($order_invoice->save()){

                        $vendor_invoice = new Vendor_invoice();
                        $vendor_invoice->vendor_id = $vendor->{$vendor::DB_TABLE_PK};
                        $vendor_invoice->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                        if($vendor_invoice->save()) {
                            $payment_request = new Purchase_order_payment_request();
                            $payment_request->purchase_order_id = $purchase_order->{$purchase_order::DB_TABLE_PK};
                            $payment_request->request_date = date('Y-m-d');
                            $payment_request->currency_id = $purchase_order->currency_id;
                            $payment_request->remarks = 'OFFSET PAYMENT ORDERED BY MR ARNOLD';
                            $payment_request->approval_module_id = 3;
                            $payment_request->requester_id = $this->session->userdata('employee_id');
                            $payment_request->finalizer_id = $this->session->userdata('employee_id');
                            $payment_request->status = 'APPROVED';

                            if ($payment_request->save()) {
                                $pr_invoice_item = new Purchase_order_payment_request_invoice_item();
                                $pr_invoice_item->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                                $pr_invoice_item->purchase_order_payment_request_id = $payment_request->{$payment_request::DB_TABLE_PK};
                                $pr_invoice_item->remarks = 'OFFSET PAYMENT ORDERED BY MR ARNOLD';
                                $pr_invoice_item->requested_amount = $invoice->amount;
                                if ($pr_invoice_item->save()) {
                                    $pr_approval = new Purchase_order_payment_request_approval();
                                    $pr_approval->purchase_order_payment_request_id = $payment_request->{$payment_request::DB_TABLE_PK};
                                    $pr_approval->created_by = $this->session->userdata('employee_id');
                                    $pr_approval->approval_chain_level_id = 15;
                                    $pr_approval->is_final = 1;
                                    $pr_approval->comments = 'APPROVED AS A BY PASS';
                                    $pr_approval->approval_date = $today;
                                    if ($pr_approval->save()) {
                                        $pr_approval_invoice_item = new Purchase_order_payment_request_approval_invoice_item();
                                        $pr_approval_invoice_item->purchase_order_payment_request_approval_id = $pr_approval->{$pr_approval::DB_TABLE_PK};
                                        $pr_approval_invoice_item->purchase_order_payment_request_invoice_item_id = $pr_invoice_item->{$pr_invoice_item::DB_TABLE_PK};
                                        $pr_approval_invoice_item->approved_amount = $pr_invoice_item->requested_amount;
                                        if ($pr_approval_invoice_item->save()) {
                                            $pv = new Payment_voucher();
                                            $pv->currency_id = $payment_request->currency_id;
                                            $pv->credit_account_id = 270;
                                            $pv->reference = 'OFFSET PAYMENT ';
                                            $pv->employee_id = $employee_id;
                                            $pv->remarks = 'OFFSET PAYMENT AUTHORISED BY MD: ARNOLD ' . $purchase_order->order_number();
                                            $pv->exchange_rate = $currency->rate_to_native();
                                            $pv->payment_date = $today;
                                            $pv->payee = $vendor->vendor_name;
                                            if ($pv->save()) {
                                                $pv_item = new Payment_voucher_item();
                                                $pv_item->payment_voucher_id = $pv->{$pv::DB_TABLE_PK};
                                                $pv_item->amount = $invoice->amount;
                                                $pv_item->description = $invoice->reference;
                                                $pv_item->debit_account_id = $vendor->account_id;

                                                if ($pv_item->save()) {
                                                    $invoice_pv = new Invoice_payment_voucher();
                                                    $invoice_pv->invoice_id = $invoice->{$invoice::DB_TABLE_PK};
                                                    $invoice_pv->payment_voucher_id = $pv->{$pv::DB_TABLE_PK};
                                                    if ($invoice_pv->save()) {
                                                        $sn++;
                                                        echo '<hr/>'.$sn.': '.$purchase_order->order_number();

                                                        $pviai = new Payment_voucher_item_approved_invoice_item();
                                                        $pviai->payment_voucher_item_id = $pv_item->{$pv_item::DB_TABLE_PK};
                                                        $pviai->purchase_order_payment_request_approval_invoice_item_id = $pr_approval_invoice_item->{$pr_approval_invoice_item::DB_TABLE_PK};
                                                        $pviai->save();
                                                    }
                                                }
                                            }
                                        }
                                    }


                                }
                            }
                        }
                    }

                }



            } else {
                echo $balance_due.'<br/>';
            }
        }
    }

    public function insert_papv_junction(){
        $this->load->model('purchase_order_payment_request_approval_payment_voucher');

        $sql = 'SELECT payment_voucher_items.payment_voucher_id,purchase_order_payment_request_approval_id FROM purchase_order_payment_request_approval_invoice_items
                LEFT JOIN payment_voucher_item_approved_invoice_items ON purchase_order_payment_request_approval_invoice_items.id = payment_voucher_item_approved_invoice_items.purchase_order_payment_request_approval_invoice_item_id
                LEFT JOIN payment_voucher_items ON payment_voucher_item_approved_invoice_items.payment_voucher_item_id = payment_voucher_items.payment_voucher_item_id
                WHERE purchase_order_payment_request_approval_id NOT IN(
                  SELECT purchase_order_payment_request_approval_payment_vouchers.purchase_order_payment_request_approval_id FROM purchase_order_payment_request_approval_payment_vouchers
                ) AND payment_voucher_id IS NOT NULL 
        ';

        $query = $this->db->query($sql);

        $results = $query->result();
        echo $query->num_rows();
        foreach ($results as $row){
            $junction = new Purchase_order_payment_request_approval_payment_voucher();
            $junction->payment_voucher_id = $row->payment_voucher_id;
            $junction->purchase_order_payment_request_approval_id = $row->purchase_order_payment_request_approval_id;
            $junction->save();
        }
    }

    public function convert_tools_to_assets(){
        $this->load->model(['material_item','material_stock','asset_item','asset','asset_sub_location_history','external_material_transfer_item']);
        $material_items = $this->material_item->get(0,0,['category_id' => 21]);
        foreach ($material_items as $material_item){

        }
    }

    public function fix_the_doubles_GRN($order_id){
        $this->load->model([
            'purchase_order_grn',
            'goods_received_note_material_stock_item',
            'purchase_order_material_item_grn_item',
            'goods_received_note'
        ]);
        $where = 'purchase_order_id ='.$order_id;
        $purchase_order_grns = $this->purchase_order_grn->get(0,0,$where,'id ASC');
        count($purchase_order_grns);
        if(!empty($purchase_order_grns)){
            echo  count($purchase_order_grns).'<br/>';
            ?>
            <table class="table table-bordered" border="1px  solid black">
                <tr>
                    <td>GRN</td><td>STOCKITEMS</td>
                </tr>
                <?php
                $n = count($purchase_order_grns);
                foreach($purchase_order_grns as $purchase_order_grn) {
                    ?>
                    <tr>
                        <td><?= $purchase_order_grn->goods_received_note_id ?></td>
                        <?php
                        $grn = new Goods_received_note();
                        $grn->load($purchase_order_grn->goods_received_note_id);
                        $grn_material_stock_items = $grn->material_items();
                        ?>
                        <td>
                            <table class="table table-bordered" border="1px solid black">
                                <tr>
                                    <td>GRNITEM</td>
                                    <td>STOCKID</td>
                                    <td>REJECTED QTY</td>
                                </tr>
                                <?php
                                if($n > 1) {
                                    foreach ($grn_material_stock_items as $grn_material_stock_item) {
                                        ?>
                                        <tr>
                                            <td><?= $grn_material_stock_item->item_id ?></td>
                                            <td><?= $grn_material_stock_item->stock_id ?></td>
                                            <td><?= $grn_material_stock_item->rejected_quantity ?></td>
                                        </tr>
                                        <?php
                                        $material_stock = $grn_material_stock_item->stock_item();
                                        $where = [
                                            'goods_received_note_item_id' => $grn_material_stock_item->item_id
                                        ];
                                        $purchase_order_item_grn_item = $this->purchase_order_material_item_grn_item->get(0, 0, $where, 'id DESC');
                                        $purchase_order_item_grn_item = !empty($purchase_order_item_grn_item) ? array_shift($purchase_order_item_grn_item) : null;
                                        !is_null($purchase_order_item_grn_item) ? $purchase_order_item_grn_item->delete() : '';
                                        $material_stock->delete();
                                        $grn_material_stock_item->delete();

                                    }

                                    $grn->delete();
                                    $n--;
                                } else {
                                    foreach ($grn_material_stock_items as $grn_material_stock_item) {
                                        ?>
                                        <tr>
                                            <td><?= $grn_material_stock_item->item_id ?></td>
                                            <td><?= $grn_material_stock_item->stock_id ?></td>
                                            <td><?= $grn_material_stock_item->rejected_quantity ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </table>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
        }

    }

    public function test_post()
    {
        inspect_object($this->input->post());
    }

    public function run_querries(){
        $sql = '';

        $kaboom = explode(';',rtrim($sql,';'));

        foreach ($kaboom as $sql){
            $query = $this->db->query($sql);
            echo $sql.'<hr/>';
        }

    }




	/**********MIGRATION FNS**********/
	public function migrate(){
	    $this->fill_the_payment_voucher_item_approved_request_items_junction();
	    echo '<br/>';
	    echo '<br/>';
	    echo '################################################################################################'.'<br/>';
	    echo '<br/>';
	    echo '<br/>';
        $this->migrate_contractors_and_clients_to_vendors();
	    echo '<br/>';
	    echo '<br/>';
	    echo '################################################################################################'.'<br/>';
	    echo '<br/>';
	    echo '<br/>';
        $this->fill_the_payment_voucher_credit_account_junction();
	    echo '<br/>';
	    echo '<br/>';
	    echo '################################################################################################'.'<br/>';
	    echo '<br/>';
	    echo '<br/>';
        $this->fill_the_journal_junctions();
	    echo '<br/>';
	    echo '<br/>';
	    echo '################################################################################################'.'<br/>';
	    echo '<br/>';
	    echo '<br/>';
        $this->fix_withholding_currency();
	    echo '<br/>';
	    echo '<br/>';
	    echo '################################################################################################'.'<br/>';
	    echo '<br/>';
	    echo '<br/>';
    }

    public function fill_the_payment_voucher_credit_account_junction(){
        $this->load->model(['payment_voucher','payment_voucher_credit_account']);
        $payment_vouchers = $this->payment_voucher->get();
        foreach($payment_vouchers as $payment_voucher){
            $voucher = new Payment_voucher();
            $voucher->load($payment_voucher->{$payment_voucher::DB_TABLE_PK});
            $payment_voucher_credit_account = new Payment_voucher_credit_account();
            $payment_voucher_credit_account->payment_voucher_id = $payment_voucher->{$payment_voucher::DB_TABLE_PK};
            $payment_voucher_credit_account->account_id = $payment_voucher->credit_account_id;
            $payment_voucher_credit_account->amount = $voucher->amount();
            $payment_voucher_credit_account->narration = 'Payment for for approved requested cash item';
            $payment_voucher_credit_account->save();
        }
        echo 'Payment voucher credit accounts junction filled';

    }

    public function migrate_contractors_and_clients_to_vendors(){
        $this->load->model(['stakeholder','withholding_tax','project','stock_sale','maintenance_service','account','payment_voucher_item','receipt','outgoing_invoice','sub_contract']);
        $sql_one = 'SELECT * FROM clients';
        $clients = $this->db->query($sql_one)->result();

        $sql_two = 'SELECT * FROM contractors';
        $contractors = $this->db->query($sql_two)->result();

        $sql_three = 'SELECT * FROM stakeholders';
        $stakeholders = $this->db->query($sql_three)->result();
        $no = 0;
        ?>
        <div class="container col-md-12">
        <table style="table-layout: fixed" width="100%" border="1px solid black" cellspacing="0">
            <thead>
            <tr style="background-color: #0c0c0c; color: white">
                <th>No</th><th style="width: 50%">moving Clients..</th><th style="width: 48%">Items</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($clients as $client){
                $no++;
                $stakeholder = new Stakeholder();
                $stakeholder->stakeholder_name = $client->client_name;
                $stakeholder->phone = $client->phone;
                $stakeholder->alternative_phone = $client->alternative_phone;
                $stakeholder->email = $client->email;
                $stakeholder->address = $client->address;
                $stakeholder->account_id = $client->account_id;
                $stakeholder->created_by = 1;
                if($stakeholder->save()){
                    ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td><?= $stakeholder->stakeholder_name ?></td>
                        <td>
                            <?php
                            $projects_query = 'SELECT * FROM projects WHERE stakeholder_id = '.$client->client_id.'';
                            $projects = $this->db->query($projects_query)->result();
                            if(!empty($projects)){ ?>
                                <table style="table-layout: fixed" width="100%" border="1px solid black" cellspacing="0">
                                    <tr style="background-color: #00a7d0">
                                        <td>Projects</td>
                                    </tr>
                                    <?php
                                    foreach ($projects as $project) {
                                        $stakeholder_project = new Project();
                                        $stakeholder_project->load($project->project_id);
                                        $stakeholder_project->stakeholder_id = $stakeholder->{$stakeholder::DB_TABLE_PK};
                                        $stakeholder_project->save();
                                        ?>
                                        <tr>
                                            <td><?= $stakeholder_project->project_name ?></td>
                                        </tr>
                                        <?php
                                    } ?>
                                </table>
                                <br/>
                            <?php }

                            $stock_sales_query = 'SELECT * FROM stock_sales WHERE stakeholder_id = '.$client->client_id;
                            $stock_sales = $this->db->query($stock_sales_query)->result();
                            if(!empty($stock_sales)){ ?>
                                <table style="table-layout: fixed" width="100%" border="1px solid black" cellspacing="0">
                                    <tr style="background-color: #01ff70">
                                        <td>Stock Sales</td>
                                    </tr>
                                    <?php
                                    foreach($stock_sales as $stock_sale){
                                        $stakeholder_sale = new Stock_sale();
                                        $stakeholder_sale->load($stock_sale->id);
                                        $stakeholder_sale->stakeholder_id = $stakeholder->{$stakeholder::DB_TABLE_PK};
                                        $stakeholder_sale->save();
                                        ?>
                                        <tr>
                                            <td><?= $stakeholder_sale->sale_number() ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <br/>
                                <?php
                            }

                            $maintenance_services_query = 'SELECT * FROM maintenance_services WHERE client_id = '.$client->client_id;
                            $maintenance_services = $this->db->query($maintenance_services_query)->result();
                            if(!empty($maintenance_services)){ ?>
                                <table style="table-layout: fixed" width="100%" border="1px solid black" cellspacing="0">
                                    <tr style="background-color: #00a65a">
                                        <td>Maintenance Services</td>
                                    </tr>
                                    <?php
                                    foreach($maintenance_services as $maintenance_service){
                                        $stakeholder_service = new Maintenance_service();
                                        $stakeholder_service->load($maintenance_service->service_id);
                                        $stakeholder_service->client_id = $stakeholder->{$stakeholder::DB_TABLE_PK};
                                        $stakeholder_service->save();
                                        ?>
                                        <tr>
                                            <td><?= $stakeholder_service->maintenance_services_no() ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <br/>
                                <?php
                            }

                            $outgoing_invoices_query = 'SELECT * FROM outgoing_invoices WHERE invoice_to = '.$client->client_id;
                            $outgoing_invoices = $this->db->query($outgoing_invoices_query)->result();
                            if(!empty($outgoing_invoices)){ ?>
                                <table style="table-layout: fixed" width="100%" border="1px solid black" cellspacing="0">
                                    <tr style="background-color: #8d4654">
                                        <td>Outgoing Invoices</td>
                                    </tr>
                                    <?php
                                    foreach($outgoing_invoices as $outgoing_invoice){
                                        $invoice = new Outgoing_invoice();
                                        $invoice->load($outgoing_invoice->id);
                                        $invoice->invoice_to = $stakeholder->{$stakeholder::DB_TABLE_PK};
                                        $invoice->save();
                                        ?>
                                        <tr>
                                            <td><?= $invoice->outgoing_inv_number() ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <br/>
                                <?php
                            }

                            $client_account_query = 'SELECT * FROM accounts
                                                        INNER JOIN clients ON accounts.account_id = clients.account_id
                                                        WHERE client_id = '.$client->client_id;
                            $client_accounts = $this->db->query($client_account_query)->result();
                            if(!empty($client_accounts)){
                                foreach($client_accounts as $client_account){
                                    $account = new Account();
                                    $account->load($client_account->account_id);
                                    $payment_voucher_items_query = 'SELECT * FROM payment_voucher_items WHERE debit_account_id = '.$client_account->account_id;
                                    $payment_voucher_items = $this->db->query($payment_voucher_items_query)->result();
                                    if(!empty($payment_voucher_items)){
                                        ?>
                                        <table style="table-layout: fixed" width="100%" border="1px solid black" cellspacing="0">
                                            <tr style="background-color: #000066">
                                                <td>Payment Voucher Items</td>
                                            </tr>
                                            <?php
                                            foreach($payment_voucher_items as $payment_voucher_item){
                                                $stakeholder_payment = new Payment_voucher_item();
                                                $stakeholder_payment->load($payment_voucher_item->payment_voucher_item_id);
                                                $stakeholder_payment->stakeholder_id = $stakeholder->{$stakeholder::DB_TABLE_PK};
                                                $stakeholder_payment->save();
                                                ?>
                                                <tr>
                                                    <td><?= 'pvitem/'.$stakeholder_payment->{$stakeholder_payment::DB_TABLE_PK}.'/'.$stakeholder_payment->payment_voucher()->payment_voucher_number() ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </table>
                                        <br/>
                                        <?php
                                    }

                                    $receipts_query = 'SELECT * FROM receipts WHERE credit_account_id = '.$client_account->account_id;
                                    $receipts = $this->db->query($receipts_query)->result();
                                    if(!empty($receipts)){
                                        ?>
                                        <table style="table-layout: fixed" width="100%" border="1px solid black" cellspacing="0">
                                            <tr style="background-color: #125acd">
                                                <td>Receipts</td>
                                            </tr>
                                            <?php
                                            foreach($receipts as $receipt){
                                                $receipt_record = new Receipt();
                                                $receipt_record->load($receipt->id);
                                                $receipt_record->credit_account_id = $stakeholder->{$stakeholder::DB_TABLE_PK};
                                                $receipt_record->save();
                                                ?>
                                                <tr>
                                                    <td><?= $receipt_record->receipt_number() ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </table>
                                        <br/>
                                        <?php
                                    }
                                }
                            }

                            ?>
                        </td>
                    </tr>
                    <?php
                }
            } ?>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align: center"><strong>No</strong></td><td style="width: 50%; text-align: center"><strong>moving Contractors..</strong></td><td style="width: 48%; text-align: center"><strong>Items</strong></td>
            </tr>
            <?php
            foreach($contractors as $contractor){
                $no++;
                $stakeholder = new Stakeholder();
                $stakeholder->stakeholder_name = $contractor->contractor_name;
                $stakeholder->phone = $contractor->phone;
                $stakeholder->alternative_phone = $contractor->alternative_phone;
                $stakeholder->email = $contractor->email;
                $stakeholder->address = $contractor->address;
                $stakeholder->account_id = null;
                $stakeholder->created_by = 1;
                if($stakeholder->save()){
                    ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td><?= $stakeholder->stakeholder_name ?></td>
                        <td>
                            <?php
                            $sub_contracts_query = 'SELECT * FROM sub_contracts WHERE stakeholder_id = '.$contractor->id.'';
                            $sub_contracts = $this->db->query($sub_contracts_query)->result();
                            if(!empty($sub_contracts)){ ?>
                                <table style="table-layout: fixed" width="100%" border="1px solid black" cellspacing="0">
                                    <tr style="background-color: #9999ff">
                                        <td>Sub contracts</td>
                                    </tr>
                                    <?php
                                    foreach ($sub_contracts as $sub_contract) {
                                        $scs = new Sub_contract();
                                        $scs->load($sub_contract->id);
                                        $scs->stakeholder_id = $stakeholder->{$stakeholder::DB_TABLE_PK};
                                        $scs->save();
                                        ?>
                                        <tr>
                                            <td><?= $scs->contract_name ?></td>
                                        </tr>
                                        <?php
                                    } ?>
                                </table>
                                <br/>
                            <?php }

                            $contracor_account_query = 'SELECT * FROM contractor_accounts
                                                        INNER JOIN accounts ON contractor_accounts.account_id = accounts.account_id
                                                        WHERE contractor_id = '.$contractor->id;
                            $contracor_accounts = $this->db->query($contracor_account_query)->result();
                            if(!empty($contracor_accounts)){
                                foreach($contracor_accounts as $contracor_account){
                                    $account = new Account();
                                    $account->load($contracor_account->account_id);
                                    $payment_voucher_items_query = 'SELECT * FROM payment_voucher_items WHERE debit_account_id = '.$contracor_account->account_id;
                                    $payment_voucher_items = $this->db->query($payment_voucher_items_query)->result();
                                    if(!empty($payment_voucher_items)){
                                        ?>
                                        <table style="table-layout: fixed" width="100%" border="1px solid black" cellspacing="0">
                                            <tr style="background-color: #a94442">
                                                <td>Payment Voucher Items</td>
                                            </tr>
                                            <?php
                                            foreach($payment_voucher_items as $payment_voucher_item){
                                                $stakeholder_payment = new Payment_voucher_item();
                                                $stakeholder_payment->load($payment_voucher_item->payment_voucher_item_id);
                                                $stakeholder_payment->stakeholder_id = $stakeholder->{$stakeholder::DB_TABLE_PK};
                                                $stakeholder_payment->debit_account_id = null;
                                                $stakeholder_payment->save();
                                                ?>
                                                <tr>
                                                    <td><?= 'pvitem/'.$stakeholder_payment->{$stakeholder_payment::DB_TABLE_PK}.'/'.$stakeholder_payment->payment_voucher()->payment_voucher_number() ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </table>
                                        <br/>
                                        <?php
                                    }


                                    $wht_query = 'SELECT * FROM withholding_taxes WHERE credit_account_id =' . $contracor_account->account_id;
                                    $wht_items = $this->db->query($wht_query)->result();
                                    if (!empty($wht_items)) {
                                        ?>
                                        <table style="table-layout: fixed" width="100%" border="1px solid black"
                                               cellspacing="0">
                                            <tr style="background-color: #c87f0a">
                                                <td>Withholding Taxes</td>
                                            </tr>
                                            <?php
                                            foreach ($wht_items as $wht_item) {
                                                $wht = new Withholding_tax();
                                                $wht->load($wht_item->id);
                                                $wht->stakeholder_id = $stakeholder->{$stakeholder::DB_TABLE_PK};
                                                $wht->credit_account_id = null;
                                                $wht->save();
                                                $number = ($wht->payment_voucher_item_id != '' && $wht->receipt_item_id == '') ? $wht->payment_voucher_item()->payment_voucher()->payment_voucher_number() : $wht->receipt_item()->receipt()->receipt_number();
                                                ?>
                                                <tr>
                                                    <td><?= 'WHT/' . $wht->{$wht::DB_TABLE_PK}.' of '.$number ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </table>
                                        <br/>
                                        <?php
                                    }
                                }
                            }

                            ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align: center"><strong>No</strong></td><td style="width: 50%; text-align: center"><strong>moving items form wat used to be vendor accounts..</strong></td><td style="width: 48%; text-align: center"><strong>Items</strong></td>
            </tr>
            <?php
            foreach($stakeholders as $stakeholder){
                ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= $stakeholder->stakeholder_name ?></td>
                    <td>
                        <?php
                        $has_account = $stakeholder->account_id != '' ? $stakeholder->account_id : false;
                        if($has_account) {
                            $sql_four = 'SELECT * FROM payment_voucher_items WHERE debit_account_id =' . $stakeholder->account_id;
                            $payment_voucher_items = $this->db->query($sql_four)->result();
                            if (!empty($payment_voucher_items)) {
                                ?>
                                <table style="table-layout: fixed" width="100%" border="1px solid black"
                                       cellspacing="0">
                                    <tr style="background-color: #cc006a">
                                        <td>Payment Voucher Items</td>
                                    </tr>
                                    <?php
                                    foreach ($payment_voucher_items as $payment_voucher_item) {
                                        $stakeholder_payment = new Payment_voucher_item();
                                        $stakeholder_payment->load($payment_voucher_item->payment_voucher_item_id);
                                        $stakeholder_payment->stakeholder_id = $stakeholder->stakeholder_id;
                                        $stakeholder_payment->debit_account_id = null;
                                        $stakeholder_payment->save();
                                        ?>
                                        <tr>
                                            <td><?= 'pvitem/' . $stakeholder_payment->{$stakeholder_payment::DB_TABLE_PK} . '/' . $stakeholder_payment->payment_voucher()->payment_voucher_number() ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <br/>
                                <?php
                            }

                            $wht_query = 'SELECT * FROM withholding_taxes WHERE credit_account_id =' . $stakeholder->account_id;
                            $wht_items = $this->db->query($wht_query)->result();
                            if (!empty($wht_items)) {
                                ?>
                                <table style="table-layout: fixed" width="100%" border="1px solid black"
                                       cellspacing="0">
                                    <tr style="background-color: #c87f0a">
                                        <td>Withholding Taxes</td>
                                    </tr>
                                    <?php
                                    foreach ($wht_items as $wht_item) {
                                        $wht = new Withholding_tax();
                                        $wht->load($wht_item->id);
                                        $wht->stakeholder_id = $stakeholder->stakeholder_id;
                                        $wht->credit_account_id = null;
                                        $wht->save();
                                        $number = ($wht->payment_voucher_item_id != '' && $wht->receipt_item_id == '') ? $wht->payment_voucher_item()->payment_voucher()->payment_voucher_number() : $wht->receipt_item()->receipt()->receipt_number();
                                        ?>
                                        <tr>
                                            <td><?= 'WHT/' . $wht->{$wht::DB_TABLE_PK}.' of '.$number ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <br/>
                                <?php
                            }
                        } else {
                            ?>
                            No Account
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
    }

    public function fill_the_journal_junctions(){
        $this->load->model([
            'journal_voucher',
            'journal_payment_voucher',
            'journal_voucher_credit_account',
            'journal_voucher_item',
            'payment_voucher',
            'payment_voucher_credit_account',
            'payment_voucher_item'
        ]);

        $payment_vouchers  = $this->payment_voucher->get();
        foreach($payment_vouchers as $payment_voucher){
            $journal = new Journal_voucher();
            $journal->transaction_date = $payment_voucher->payment_date;
            $journal->reference = $payment_voucher->reference;
            $journal->journal_type = "CASH PAYMENT";
            $confidentiality_position = confidentiality_chain_position($this->session->userdata('confidentiality'));
            $journal->confidentiality_chain_position = $confidentiality_position ? $confidentiality_position : 0;
            $journal->currency_id = $payment_voucher->currency_id;
            $journal->remarks = $payment_voucher->remarks;
            $journal->created_by = $payment_voucher->employee_id;
            if($journal->save()){

                $jv_pv = new Journal_payment_voucher();
                $jv_pv->payment_voucher_id = $payment_voucher->{$payment_voucher::DB_TABLE_PK};
                $jv_pv->journal_id = $journal->{$journal::DB_TABLE_PK};
                $jv_pv->save();

                $sql_one = 'SELECT * FROM payment_voucher_credit_accounts WHERE payment_voucher_id = '.$payment_voucher->{$payment_voucher::DB_TABLE_PK};
                $payment_voucher_credit_accounts = $this->db->query($sql_one)->result();
                foreach($payment_voucher_credit_accounts as $credit_account){
                    $stakeholder_id = $credit_account->stakeholder_id != '' ? $credit_account->stakeholder_id : null;
                    $account_id = $credit_account->account_id != '' ? $credit_account->account_id : null;


                    $jv_crdt_acc = new Journal_voucher_credit_account();
                    $jv_crdt_acc->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                    if(!is_null($stakeholder_id)){
                        $jv_crdt_acc->stakeholder_id = $stakeholder_id;
                    } else {
                        $jv_crdt_acc->account_id = $account_id;
                    }
                    $jv_crdt_acc->amount = $credit_account->amount;
                    $jv_crdt_acc->narration = $credit_account->narration != '' ? $credit_account->narration : 'N/A';
                    $jv_crdt_acc->save();

                }


                $sql_two = 'SELECT * FROM payment_voucher_items WHERE payment_voucher_id = '.$payment_voucher->{$payment_voucher::DB_TABLE_PK};
                $payment_voucher_items = $this->db->query($sql_two)->result();
                foreach($payment_voucher_items as $payment_voucher_item){
                    $stakeholder_id = $payment_voucher_item->stakeholder_id != '' ? $payment_voucher_item->stakeholder_id : null;
                    $account_id = $payment_voucher_item->debit_account_id != '' ? $payment_voucher_item->debit_account_id : null;


                    $jv_item = new Journal_voucher_item();
                    $jv_item->journal_voucher_id = $journal->{$journal::DB_TABLE_PK};
                    $jv_item->amount = $payment_voucher_item->amount;
                    if (!is_null($stakeholder_id)) {
                        $jv_item->stakeholder_id = $stakeholder_id;
                    } else {
                        $jv_item->debit_account_id = $account_id;
                    }
                    $jv_item->narration = $payment_voucher_item->description != '' ? $payment_voucher_item->description : 'N/A';
                    $jv_item->save();

                }

            }
        }

        echo "Journal junctions filled";
    }

	public function fix_withholding_currency(){
		$this->load->model(['withholding_tax']);
		$whts = $this->withholding_tax->get();
		foreach($whts as $wht){
			$item_withheld = new Withholding_tax();
			$item_withheld->load($wht->{$wht::DB_TABLE_PK});
			$receipt_item_id = $item_withheld->receipt_item_id != '' ? $item_withheld->receipt_item_id : null;
			$payment_voucher_item_id = $item_withheld->payment_voucher_item_id != '' ? $item_withheld->payment_voucher_item_id : null;
			if(!is_null($receipt_item_id) && is_null($payment_voucher_item_id)){
				$currency_id = $item_withheld->receipt_item()->receipt()->currency_id;
			} else if(!is_null($payment_voucher_item_id) && is_null($receipt_item_id)){
				$currency_id = $item_withheld->payment_voucher_item()->payment_voucher()->currency_id;
			}
			$item_withheld->currency_id = $currency_id;
			$item_withheld->save();
		}
		echo 'Withholding currency fixed';
	}

    public function fill_the_payment_voucher_item_approved_request_items_junction(){
        $this->load->model(['requisition_approval_payment_voucher','requisition_approval','payment_voucher']);
        $approval_payment_vouchers = $this->requisition_approval_payment_voucher->get();
        $no = 0;
        ?>
        <div class="container col-md-12">
        <table style="table-layout: fixed" width="100%" border="1px solid black" cellspacing="0">
            <thead>
                <th style="width: 5%">Reference</th><th>Items As Approved</th><th style="width: 10%">Mergery Goes Here</th><th>Items AS paid</th><th style="width: 5%">Reference</th>
            </thead>
            <tbody>
            <?php
            foreach($approval_payment_vouchers as $approval_payment_voucher){
                $payment_voucher = new Payment_voucher();
                $payment_voucher->load($approval_payment_voucher->payment_voucher_id);
                $approval = new Requisition_approval();
                $approval->load($approval_payment_voucher->requisition_approval_id);
                $requisition = $approval->requisition();

                $sql = 'SELECT * FROM payment_voucher_items WHERE payment_voucher_id = '.$approval_payment_voucher->payment_voucher_id;
                $payment_voucher_items = $this->db->query($sql)->result();


                $sql = 'SELECT requisition_approval_material_items.id AS approved_item_id, "material" AS item_type, CONCAT(item_name," (",approved_quantity, symbol,") ") AS item_description, (approved_quantity*approved_rate) AS approved_amount, approved_quantity, approved_rate
						FROM requisition_approval_material_items
						LEFT JOIN requisition_material_items ON requisition_approval_material_items.requisition_material_item_id = requisition_material_items.id
						LEFT JOIN material_items ON requisition_material_items.material_item_id = material_items.item_id
						LEFT JOIN measurement_units ON material_items.unit_id = measurement_units.unit_id
						WHERE requisition_approval_id = '.$approval_payment_voucher->requisition_approval_id.' AND requisition_approval_material_items.source_type = "cash"
                        
                        UNION
                        
                        SELECT requisition_approval_asset_items.id AS approved_item_id, "asset" AS item_type, CONCAT(asset_name," (",approved_quantity," Nos) ") AS item_description, (approved_quantity*approved_rate) AS approved_amount, approved_quantity, approved_rate
                        FROM requisition_approval_asset_items
                        LEFT JOIN requisition_asset_items ON requisition_approval_asset_items.requisition_asset_item_id = requisition_asset_items.id
                        LEFT JOIN asset_items ON requisition_asset_items.asset_item_id = asset_items.id
                        WHERE requisition_approval_id = '.$approval_payment_voucher->requisition_approval_id.' AND requisition_approval_asset_items.source_type = "cash"
                        
                        UNION
                        
                        SELECT requisition_approval_service_items.id AS approved_item_id, "service" AS item_type, CONCAT(requisition_service_items.description," (",approved_quantity," ",symbol,") ") AS item_description, (approved_quantity*approved_rate) AS approved_amount, approved_quantity, approved_rate
                        FROM requisition_approval_service_items
                        LEFT JOIN requisition_service_items ON requisition_approval_service_items.requisition_service_item_id = requisition_service_items.id
                        LEFT JOIN measurement_units ON requisition_service_items.measurement_unit_id = measurement_units.unit_id
                        WHERE requisition_approval_id = '.$approval_payment_voucher->requisition_approval_id.' AND requisition_approval_service_items.source_type = "cash"
                        
                        UNION
                        
                        SELECT requisition_approval_cash_items.id AS approved_item_id, "cash" AS item_type, CONCAT(requisition_cash_items.description," (",approved_quantity," ",symbol,") ") AS item_description, (approved_quantity*approved_rate) AS approved_amount, approved_quantity, approved_rate 
                        FROM requisition_approval_cash_items
                        LEFT JOIN requisition_cash_items ON requisition_approval_cash_items.requisition_cash_item_id = requisition_cash_items.id
                        LEFT JOIN measurement_units ON requisition_cash_items.measurement_unit_id = measurement_units.unit_id
                        WHERE requisition_approval_id = '.$approval_payment_voucher->requisition_approval_id.'
                    ';
                $approval_items = $this->db->query($sql)->num_rows() > 0 ? $this->db->query($sql)->result() : false;
                ?>
                <tr>
                    <td><?= anchor(base_url('requisitions/preview_requisition/'.$requisition->{$requisition::DB_TABLE_PK}),$approval->requisition()->requisition_number(),'target="_blank"') ?></td>
                    <td>
                        <table style="table-layout: fixed" width="100%" border="1px solid black" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Description</th><th style="width: 20%">Approved Amount</th><th style="width: 8%">Indx</th><th style="width: 8%">Aprv Item id</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $total_approved_amount = 0;
                                foreach($approval_items as $sn=>$approval_item){
                                    $total_approved_amount += $approval_item->approved_amount;
//                                    $item_description = preg_replace('/\s/', '', $approval_item->item_description);
                                    ?>
                                    <tr>
                                        <td><?= wordwrap($approval_item->item_description,40,'<br/>') ?></td>
                                        <td style="text-align: right"><?= number_format($approval_item->approved_amount,2) ?></td>
                                        <td style="text-align: right"><?= $sn ?></td>
                                        <td style="text-align: right"><?= $approval_item->approved_item_id ?></td>
                                    </tr>
                            <?php
                                }
                            ?>
                            <tr style="background-color: #6EBEF4">
                                <td style="text-align: right">APPROVED AMOUNT</td><td style="text-align: right"><?= number_format($total_approved_amount,2) ?></td>
                                <td colspan="2"></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <td>
                        <?php
                        $item_count = (count($payment_voucher_items) == count($approval_items)) ? count($approval_items) : false;
                        if($item_count) {
                            ?>
                            <table style="table-layout: fixed" width="100%" border="1px solid black" cellspacing="0">
                                <tr>
                                    <th><i>Inserted junction(s)</i></th>
                                </tr>
                            <?php
                            for ($i = 0; $i < $item_count; $i++) {
                                $sql = 'SELECT payment_voucher_item_id FROM payment_voucher_item_approved_cash_request_items WHERE payment_voucher_item_id ='.$payment_voucher_items[$i]->payment_voucher_item_id.' LIMIT 1';
                                $pv_item = $this->db->query($sql)->num_rows() > 0 ? $this->db->query($sql)->row()->payment_voucher_item_id : false;
                                if(!$pv_item){
                                ?>
                                <tr>
                                    <?php
                                    $this->load->model('payment_voucher_item_approved_cash_request_item');
                                    $pv_item_apcr_item = new Payment_voucher_item_approved_cash_request_item();
                                    $pv_item_apcr_item->payment_voucher_item_id = $payment_voucher_items[$i]->payment_voucher_item_id;
                                    $pv_item_apcr_item->quantity = $approval_items[$i]->approved_quantity;
                                    $pv_item_apcr_item->rate = $payment_voucher_items[$i]->amount/$approval_items[$i]->approved_quantity;
                                    if($approval_items[$i]->item_type == "cash"){
                                        $pv_item_apcr_item->requisition_approval_cash_item_id = $approval_items[$i]->approved_item_id;
                                    } else if($approval_items[$i]->item_type == "service"){
                                        $pv_item_apcr_item->requisition_approval_service_item_id = $approval_items[$i]->approved_item_id;
                                    } else if($approval_items[$i]->item_type == "material"){
                                        $pv_item_apcr_item->requisition_approval_material_item_id = $approval_items[$i]->approved_item_id;
                                    } else if($approval_items[$i]->item_type == "asset"){
                                        $pv_item_apcr_item->requisition_approval_asset_item_id = $approval_items[$i]->approved_item_id;
                                    }
                                    $pv_item_apcr_item->save();
                                    ?>
                                    <td>[<?= $approval_items[$i]->item_type.'_'.$approval_items[$i]->approved_item_id ?>,<?= $payment_voucher_items[$i]->payment_voucher_item_id ?>]</td>
                                </tr>
                                <?php
                                } else {
                                    ?>
                                    <tr>
                                        <td><?= "Done!" ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </table>
                            <?php
                        }
                        ?>
                    </td>
                    <td>
                        <table style="table-layout: fixed" width="100%" border="1px solid black" cellspacing="0">
                            <thead>
                            <tr>
                                <th style="width: 8%">PV item id</th><th style="width: 8%">Indx</th><th style="width: 20%">Paid Amount</th><th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_paid_amount = 0;
                            foreach($payment_voucher_items as $sn=>$payment_voucher_item){
                                $total_paid_amount += $payment_voucher_item->amount;
//                                $pv_item_description = preg_replace('/\s/', '',$payment_voucher_item->description);
                                $pv_item_description = explode('@',$payment_voucher_item->description);
                                    ?>
                                <tr>
                                    <td><?= $payment_voucher_item->payment_voucher_item_id ?></td>
                                    <td><?= $sn ?></td>
                                    <td style="text-align: right"><?= number_format($payment_voucher_item->amount,2) ?></td>
                                    <td><?= wordwrap($pv_item_description[0],40,'<br/>') ?></td>
                                </tr>
                                <?php
                                }
                            ?>
                            <tr style="background-color: #6EBEF4">
                                <td colspan="2"></td>
                                <td style="text-align: right"><?= number_format($total_paid_amount,2) ?></td>
                                <td style="text-align: left">PAID AMOUNT</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <td><?= anchor(base_url('finance/preview_payment_voucher/'.$approval_payment_voucher->payment_voucher_id),$payment_voucher->payment_voucher_number(),'target = "_blank"') ?></td>
                </tr>
                <?php }
            ?>
            </tbody>
        </table>
        </div>
        <?php
    }
}



