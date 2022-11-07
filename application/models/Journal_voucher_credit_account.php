<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 4/4/2019
 * Time: 4:29 PM
 */

class Journal_voucher_credit_account extends MY_Model{
    const DB_TABLE = 'journal_voucher_credit_accounts';
    const DB_TABLE_PK = 'id';

    public $account_id;
    public $stakeholder_id;
    public $journal_voucher_id;
    public $amount;
    public $narration;


    public function credit_account(){
        $this->load->model('account');
        $account = new Account();
        $account->load($this->account_id);
        return $account;
    }

    public function stakeholder(){
        $this->load->model('stakeholder');
        $stakeholder = new Stakeholder();
        $stakeholder->load($this->stakeholder_id);
        return $stakeholder;
    }

	public function account($item){
		$this->load->model(['stakeholder','account']);
		$stakeholder_id = $this->stakeholder_id != '' ? $this->stakeholder_id : null;
		$account_id = $this->account_id != '' ? $this->account_id : null;
		if(is_null($account_id) && !is_null($stakeholder_id)){
			$stakeholder = new Stakeholder();
			$stakeholder->load($this->stakeholder_id);
			if($item == 'name') {
				$account_name_or_id = $stakeholder->stakeholder_name;
			} else {
				$account_name_or_id = 'stakeholder_'.$stakeholder->{$stakeholder::DB_TABLE_PK};
			}
		}
		if(!is_null($account_id) && is_null($stakeholder_id)){
			$account = new Account();
			$account->load($this->account_id);
			if($item == 'name') {
				$account_name_or_id = $account->account_name;
			} else {
				$account_name_or_id = 'real_'.$account->{$account::DB_TABLE_PK};
			}
		}
		return $account_name_or_id;
	}



}
