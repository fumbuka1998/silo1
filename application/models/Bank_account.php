<?php

class Bank_account extends MY_Model
{
    const DB_TABLE = 'bank_accounts';
    const DB_TABLE_PK = 'id';

    public $account_id;
    public $bank_id;
    public $account_number;
    public $branch;
    public $swift_code;
    public $created_by;

    public function bank_details($account_id)
    {
        $sql = 'SELECT bank_accounts.*, bank_name FROM bank_accounts
                LEFT JOIN banks ON bank_accounts.bank_id = banks.id
                WHERE account_id = '.$account_id;
        $query = $this->db->query($sql);
        $results = $query->result();

        $bank_detail = '';

        if($results){
            foreach ($results as $result){
                $bank_detail = ucfirst($result->bank_name)."\n".$result->account_number."\n".ucfirst($result->branch)."\nSWIFT CODE: ".$result->swift_code;
            }
        }else{
            $bank_detail = 'No bank details, Cash payment is advised.';
        }

        return $bank_detail;
    }

	public function bank_account_options(){
		$this->load->model('bank');

		$bank_options = new Bank();
		$bank_options = $this->bank->get();

		$options = [];
		foreach($bank_options as $bank_option){
			$options[$bank_option->id] = $bank_option->bank_name;
		}
		return $options;
	}
}
