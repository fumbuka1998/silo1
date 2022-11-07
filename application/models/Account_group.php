<?php

class Account_group extends MY_Model{

	const DB_TABLE = 'account_groups';
	const DB_TABLE_PK = 'account_group_id';

	public $group_name;
	public $description;
	public $parent_id;
	public $group_code;
	public $group_nature_id;
	public $level;

	public function parent_group(){
		$parent = new self;
		$parent->load($this->parent_id);
		return $parent;
	}

	public function group_nature()
	{
		$group_nature = new self;
		$group_nature->load($this->group_nature_id);
		return $group_nature;
	}

	public function account_group_options($group_natures = 'all',$parent_id = null){
		$sql = 'SELECT account_groups.account_group_id,account_groups.group_name FROM account_groups 
                ';
		if($group_natures != 'all' && !empty($group_natures)){
			$nature_names = '';
			foreach ($group_natures as $group_nature){
				$nature_names .= '"'.$group_nature.'",';
			}
			$nature_names = rtrim($nature_names,', ');
			$sql .= ' LEFT JOIN account_groups nature ON account_groups.account_group_id = nature.group_nature_id
                WHERE account_groups.level > 1 AND nature.group_name IN('.$nature_names.')';
		} else {
			$sql .= ' WHERE account_groups.level > 1';
		}

		if(!is_null($parent_id)){
			$sql .= ' AND account_groups.parent_id = '.$parent_id.'';
		}

		$query = $this->db->query($sql);
		$results = $query->result();
		$options[''] = '&nbsp;';
		foreach ($results as $account_group){
			if($account_group->group_name != "ACCOUNTS RECEIVABLE" || $account_group->group_name != "ACCOUNTS PAYABLE") {
				$options[$account_group->account_group_id] = $account_group->group_name;
			}
		}
		return $options;
	}

	public function account_groups_list($limit, $start, $keyword, $order){

		//order string
		$order_string = dataTable_order_string(['group_name','','description'],$order,'group_name');

		$where = '';
		if($keyword != ''){
			$where .= 'group_name LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%" ';
		}

		$account_groups = $this->get($limit,$start,$where,$order_string);
		$rows = [];

		$this->load->model('account_group');
		$data['account_group_options'] = $this->account_group->account_group_options();
		foreach($account_groups as $account_group){
			$data['account_group'] = $account_group;
			$rows[] = [
				$account_group->group_name,
				$account_group->parent_group()->group_name,
				$account_group->description,
				$this->load->view('finance/settings/account_groups_list_actions',$data,true)
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

	public function account_group_details($account_group_id)
	{
		$account_group = new Account_group();
		$account_group->load($account_group_id);
		return $account_group;
	}

	public function parents(){
		$parents = $this->get(0,0,['level'=>1]);
		$parent_groups = [];
		foreach($parents as $parent){
			$parent_group = new self();
			$parent_group->load($parent->account_group_id);
			$parent_groups[] = $parent_group;
		}
		return $parent_groups;
	}

	public function natures($parent_id){
		$group_parent = new Account_group();
		$group_parent->load($parent_id);
		$where = ' parent_id ='.$parent_id.' AND level = '.($group_parent->level+1);
		$group_natures = $this->get(0,0,$where);
		$account_group_natures = [];
		foreach($group_natures as $nature){
			$group_nature = new self();
			$group_nature->load($nature->account_group_id);
			$account_group_natures[] = $group_nature;
		}
		return $account_group_natures;
	}

	public function sub_groups($nature_id){
		$group_nature = new self();
		$group_nature->load($nature_id);
		$where = ' group_nature_id ='.$nature_id.' AND level = '.($group_nature->level+1).' AND level < 5';
		$sub_groups = $this->get(0,0,$where);
		$sub_group_options = [];
		foreach ($sub_groups as $sub_group){
			$group = new self();
			$group->load($sub_group->account_group_id);
			$sub_group_options[] = $group;
		}
		return $sub_group_options;
	}

	public function rowspan(){
		$sub_groups = $this->sub_groups($this->{$this::DB_TABLE_PK});
		$group_accounts = $this->group_accounts();
		$n = 0;
		foreach($sub_groups as $sub_group) {
			$n++;
			$sub_group_accounts = $sub_group->group_accounts();
			if(!empty($sub_group_accounts)){
				$n += count($sub_group_accounts);
			}
		}
		return $n + count($group_accounts);
	}

	public function has_accounts(){
		$this->load->model('account');
		$accounts = $this->account->get(0,0,['account_group_id'=>$this->{$this::DB_TABLE_PK}]);
		return !empty($accounts) ? $accounts : false;
	}

	public function group_accounts($with_lower_sub_groups_accs = false){
        $this->load->model('account');
        $current_assets['inventory'] =  [
			'account_id'=>'inventory',
			'bank_id'=>'',
			'account_name'=>'Inventory',
			'currency_id'=>'',
			'account_group_id'=>10,
			'account_code'=>'',
			'opening_balance'=>'',
			'description'=>'Immaginary Account'
		];
		$current_assets['receivable'] = [
			'account_id'=>'receivable',
			'bank_id'=>'',
			'account_name'=>'Accounts Receivable',
			'currency_id'=>'',
			'account_group_id'=>10,
			'account_code'=>'',
			'opening_balance'=>'',
			'description'=>'Immaginary Account'
		];
		$current_liabilities[] = [
			'account_id'=>'payable',
			'bank_id'=>'',
			'account_name'=>'Accounts Payable',
			'currency_id'=>'',
			'account_group_id'=>13,
			'account_code'=>'',
			'opening_balance'=>'',
			'description'=>'Immaginary Account'
		];
		if($with_lower_sub_groups_accs){
		    $sql = 'SELECT * FROM accounts WHERE account_group_id IN('.$this->group_family_tree_array().')';
		    $accounts = $this->db->query($sql)->result();
        } else {
            $sql = 'SELECT * FROM accounts WHERE account_group_id = '.$this->{$this::DB_TABLE_PK};
            $accounts = $this->db->query($sql)->result();
        }
		$account_options = [];
		foreach ($accounts as $account){
			$acc = new Account();
			$acc->load($account->account_id);
			$account_options[] = $acc;
		}
		if($this->group_name == 'CURRENT ASSETS') {
			$account_options += $current_assets;
		}
		if($this->group_name == 'CURRENT LIABILITIES') {
			$account_options += $current_liabilities;
		}
		return $account_options;
	}

	public function group_items(){
		$this->load->model('account');
		$sub_groups = $this->sub_groups($this->{$this::DB_TABLE_PK});
		$group_accounts = $this->group_accounts();
		$row = '';
		$row_no = 0;

		if(empty($sub_groups) && empty($group_accounts)){
			$row .= '<tr>
						<td rowspan="'.($this->rowspan() > 1 ? $this->rowspan() : 1 ).'"><strong>'.ucfirst($this->group_name) .'</strong></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
					</tr>';
		}

		if(empty($sub_groups) && !empty($group_accounts)) {
			foreach ($group_accounts as $group_account) {
				$row_no++;
				$row .= '<tr>';
				if(!is_array($group_account)){
					if ($row_no == 1) {
						$row .= '<td rowspan="' . count($group_accounts) . '"><strong>' . ucfirst($this->group_name) . '</strong></td>';
					}
					$row .= '<td>' . ucfirst($group_account->account_name) . '</td><td style="text-align: left">' . $group_account->account_code . '</td><td>' . $group_account->edit_button() . '&nbsp;&nbsp;' . $group_account->delete_button() . '</td>
						</tr>';
				} else {
					if ($row_no == 1) {
						$row .= '<td rowspan="' . count($group_accounts) . '"><strong>' . ucfirst($this->group_name) . '</strong></td>';
					}
					$row .= '<td>' . ucfirst($group_account['account_name']) . '</td><td style="text-align: left">' . $group_account['account_code'] . '</td><td></td>
						</tr>';
				}
			}
		}

		if(!empty($sub_groups) && !empty($group_accounts)) {
			foreach ($group_accounts as $group_account) {
				$row_no++;
				if(!is_array($group_account)) {
					$row .= '<tr>';
					if ($row_no == 1) {
						$row .= '<td rowspan="' . ($this->rowspan() > 1 ? $this->rowspan() : 1) . '"><strong>' . ucfirst($this->group_name) . '</strong></td>';
					}
					$row .= '<td>' . ucfirst($group_account->account_name) . '</td><td style="text-align: left">' . $group_account->account_code . '</td><td>' . $group_account->edit_button() . '&nbsp;&nbsp;' . $group_account->delete_button() . '</td>
						</tr>';
				} else {
					$row .= '<tr>';
					if ($row_no == 1) {
						$row .= '<td rowspan="' . ($this->rowspan() > 1 ? $this->rowspan() : 1) . '"><strong>' . ucfirst($this->group_name) . '</strong></td>';
					}
					$row .= '<td>' . ucfirst($group_account['account_name']) . '</td><td style="text-align: left">' . $group_account['account_code'] . '</td><td></td>
						</tr>';
				}
			}


			foreach($sub_groups as $sub_group) {
				$row_no++;
				$sub_group_accounts = $sub_group->group_accounts();
				$row .= '<tr>';
				if($row_no == 1){
					$row .= '<td rowspan="'.($this->rowspan() > 1 ? $this->rowspan() : 1 ).'"><strong>'.ucfirst($this->group_name) .'</strong></td>';
				}
				$row .= '<td colspan="3"><strong>'.$sub_group->group_name.'</strong></td>
						</tr>';
				if (!empty($sub_group_accounts)) {
					foreach ($sub_group_accounts as $group_account) {
						if(!is_array($group_account)) {
							$row .= '<tr>
									<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . ucfirst($group_account->account_name) . '</td><td style="text-align: left">' . $group_account->account_code . '</td><td>' . $group_account->edit_button() . '&nbsp;&nbsp;' . $group_account->delete_button() . '</td>
								</tr>';
						} else {
							$row .= '<tr>
									<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . ucfirst($group_account['account_name']) . '</td><td style="text-align: left">' . $group_account['account_code'] . '</td><td></td>
								</tr>';
						}
					}
				}
			}
		}

		if(!empty($sub_groups) && empty($group_accounts)) {
			foreach($sub_groups as $sub_group) {
				$row_no++;
				$sub_group_accounts = $sub_group->group_accounts();
				$row .= '<tr>';
				if($row_no == 1){
					$row .= '<td rowspan="'.($this->rowspan() > 1 ? $this->rowspan() : 1 ).'"><strong>'.ucfirst($this->group_name) .'</strong></td>';
				}
				$row .= '<td colspan="3"><strong>'.$sub_group->group_name.'</strong></td>
						</tr>';
				if (!empty($sub_group_accounts)) {
					foreach ($sub_group_accounts as $group_account) {
						$acc = new Account();
						$acc->load($group_account->account_id);
						$row .= '<tr>
									<td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. ucfirst($group_account->account_name) .'</td><td style="text-align: left">'.$group_account->account_code.'</td><td>'.$group_account->edit_button().'&nbsp;&nbsp;'.$group_account->delete_button().'</td>
								</tr>';
					}
				}
			}
		}

		return $row;
	}

	public function group_family_tree_array($names = false){
	    $parent = new self();
	    $parent->load($this->{$this::DB_TABLE_PK});
	    $tree = [];
	    $tree[] = $names ? $parent->group_name : $parent->{$parent::DB_TABLE_PK};;
	    $sub_groups = $parent->sub_groups($this->{$this::DB_TABLE_PK});
	    if(!empty($sub_groups)){
	        foreach($sub_groups as $sub_group){
	            $tree[] = $names ? $sub_group->group_name : $sub_group->{$sub_group::DB_TABLE_PK};
	            $lower_sub_groups = $sub_group->sub_groups($sub_group->{$sub_group::DB_TABLE_PK});
	            if(!empty($lower_sub_groups)){
	                foreach ($lower_sub_groups as $lower_sub_group){
                        $tree[] = $names ? $lower_sub_group->group_name : $lower_sub_group->{$lower_sub_group::DB_TABLE_PK};
                        $lowest_sub_groups = $lower_sub_group->sub_groups($lower_sub_group->{$lower_sub_group::DB_TABLE_PK});

                        if(!empty($lowest_sub_groups)){
                            foreach ($lowest_sub_groups as $lowest_sub_group){
                                $tree[] = $names ? $lowest_sub_group->group_name : $lowest_sub_group->{$lowest_sub_group::DB_TABLE_PK};
                                $lower_most_level_sub_groups = $lowest_sub_group->sub_groups($lowest_sub_group->{$lowest_sub_group::DB_TABLE_PK});

                                if(!empty($lower_most_level_sub_groups)){
                                    foreach ($lower_most_level_sub_groups as $lower_most_level_sub_group){
                                        $tree[] = $names ? $lower_most_level_sub_group->group_name : $lower_most_level_sub_group->{$lower_most_level_sub_group::DB_TABLE_PK};
                                    }
                                }
                            }
                        }
                    }
                }
            }
	        return count($tree) > 1 ? implode(',',$tree) : $tree[0];
        } else {
            return $tree[0];
        }

    }

}



