<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/23/2018
 * Time: 8:04 AM
 */

class Sub_contract_payment_requisition_approval extends MY_Model{
    const DB_TABLE = 'sub_contract_payment_requisition_approvals';
    const DB_TABLE_PK = 'id';

    public $sub_contract_requisition_id;
    public $approval_date;
    public $approving_comments;
    public $approval_chain_level_id;
    public $returned_chain_level_id;
    public $currency_id;
    public $vat_inclusive;
    public $vat_percentage;
    public $forward_to;
    public $is_final;
    public $created_by;


    public function sub_contract_requisition(){
        $this->load->model('sub_contract_payment_requisition');
        $requisition = new Sub_contract_payment_requisition();
        $requisition->load($this->sub_contract_requisition_id);
        return $requisition;
    }

    public function approval_chain_level(){
        $this->load->model('approval_chain_level');
        $approval_chain_level = new Approval_chain_level();
        $approval_chain_level->load($this->approval_chain_level_id);
        return $approval_chain_level;
    }

    public function created_by()
    {
        $this->load->model('employee');
        $created_by = new Employee;
        $created_by->load($this->created_by);
        return $created_by;
    }

    public function currency(){
        $this->load->model('currency');
        $currency = new Currency;
        $currency->load($this->currency_id);
        return $currency;
    }

    public function is_cancelled(){
        $sub_contract_requisition_approval_id = $this->{$this::DB_TABLE_PK};
        return in_array($sub_contract_requisition_approval_id,$this->cancelled_approved_payment()) ? true : false;
    }

    public function cancelled_approved_payment(){
        $this->load->model('approved_sub_contract_payment_cancellation');
        $where['sub_contract_payment_requisition_approval_id'] = $this->{$this::DB_TABLE_PK};
        $cancelled_payments = $this->approved_sub_contract_payment_cancellation->get(0,0,$where);
        $options = [];
        foreach($cancelled_payments as $cancelled_payment){
            $options[] = $cancelled_payment->sub_contract_payment_requisition_approval_id;
        }
        return $options;
    }

    public function requisition_approval_items($sub_contract_requisition_item_id = null){
        $this->load->model('sub_contract_payment_requisition_approval_item');
        $where = [
            'sub_contract_payment_requisition_approval_id' => $this->{$this::DB_TABLE_PK},
            'sub_contract_payment_requisition_item_id'=> $sub_contract_requisition_item_id
        ];
        $approval_items = $this->sub_contract_payment_requisition_approval_item->get(0,0,$where);
        return !empty($approval_items) ? array_shift($approval_items): false;
    }

    public function approval_items($amount = false){
        $this->load->model('sub_contract_payment_requisition_approval_item');
        $approved_items = $this->sub_contract_payment_requisition_approval_item->get(0,0,['sub_contract_payment_requisition_approval_id'=>$this->{$this::DB_TABLE_PK}]);
        if($amount) {
            $total_approved_amount = 0;
            foreach ($approved_items as $approved_item) {
                if($this->vat_inclusive == 1){
                    $total_approved_amount += $approved_item->approved_amount*1.18;
                } else {
                    $total_approved_amount += $approved_item->approved_amount;
                }
            }
            return $total_approved_amount;
        } else {
            return $approved_items;
        }

    }

    public function payment_voucher(){
        $this->load->model(['sub_contract_payment_requisition_approval_payment_voucher','payment_voucher']);
        $where['sub_contract_payment_requisition_approval_id'] = $this->{$this::DB_TABLE_PK};
        $approval_payment_vouchers = $this->sub_contract_payment_requisition_approval_payment_voucher->get(0,0,$where,'id DESC');
        if(!empty($approval_payment_vouchers)){
            foreach($approval_payment_vouchers as $approval_payment_voucher){
                $payment_voucher = new Payment_voucher();
                $payment_voucher->load($approval_payment_voucher->payment_voucher_id);
                return $payment_voucher;
            }
        } else {
            return false;
        }
    }

	public function journal_voucher(){
		$this->load->model(['sub_contract_payment_requisition_approval_journal_voucher','journal_voucher']);
		$journal_voucher_entries = $this->sub_contract_payment_requisition_approval_journal_voucher->get(0,0,['sub_contract_payment_requisition_approval_id'=>$this->{$this::DB_TABLE_PK}]);
		if(!empty($journal_voucher_entries)){
			foreach($journal_voucher_entries as $journal_voucher_entry){
				$journal_voucher = new Journal_voucher();
				$journal_voucher->load($journal_voucher_entry->journal_voucher_id);
				return $journal_voucher;
			}
		} else {
			return false;
		}
	}

    public function payment_vouchers(){
        $this->load->model(['sub_contract_payment_requisition_approval_payment_voucher','payment_voucher']);
        $where['sub_contract_payment_requisition_approval_id'] = $this->{$this::DB_TABLE_PK};
        $approval_payment_vouchers = $this->sub_contract_payment_requisition_approval_payment_voucher->get(0,0,$where,'id DESC');
        $payment_vouchers = [];
        if(!empty($approval_payment_vouchers)){
            foreach($approval_payment_vouchers as $approval_payment_voucher){
                $pv = new Payment_voucher();
                $pv->load($approval_payment_voucher->payment_voucher_id);
                $payment_vouchers[] = $pv;
            }
        }
        return $payment_vouchers;
    }

    public function total_paid_amount(){
        $this->load->model(['sub_contract_payment_requisition_approval_payment_voucher','payment_voucher']);
        $where['sub_contract_payment_requisition_approval_id'] = $this->{$this::DB_TABLE_PK};
        $approval_payment_vouchers = $this->sub_contract_payment_requisition_approval_payment_voucher->get(0,0,$where,'id DESC');
        $total_amount = 0;
        if(!empty($approval_payment_vouchers)){
            foreach($approval_payment_vouchers as $approval_pv){
                $approval_payment_voucher = new Sub_contract_payment_requisition_approval_payment_voucher();
                $approval_payment_voucher->load($approval_pv->id);
                $total_amount += $approval_payment_voucher->payment_voucher()->amount();
            }
        }
        return $total_amount;
    }

    public function total_approved_amount(){
		$this->load->model(['sub_contract_payment_requisition_approval_item']);
		$where = [
			'sub_contract_payment_requisition_approval_id' => $this->{$this::DB_TABLE_PK}
		];
		$approval_items = $this->sub_contract_payment_requisition_approval_item->get(0,0,$where);
		$total_approved_amount = 0;
		foreach($approval_items as $item){
			$approval_item = new Sub_contract_payment_requisition_approval_item();
			$approval_item->load($item->id);
			$total_approved_amount += $approval_item->approved_amount;
		}
		$total_approved_amount = $this->vat_inclusive == 1 ? 1.18 * $total_approved_amount : $total_approved_amount;
    	return $total_approved_amount;
	}

}
