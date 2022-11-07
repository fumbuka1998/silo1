
<?php
class Settings extends CI_Controller
{

    public function __construct ()
    {
        parent::__construct();
        check_login();
    }

    /*
     *  BANKS
     *
     */

    public function banks_list ()
    {
        $this->load->model('Bank');
        $posted_params = dataTable_post_params();
        echo $this->Bank->bank_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);

    }

    public function save_bank ()
    {

        $this->load->model('Bank');
        $bank = new Bank();
        $edit = $bank->load($this->input->post('bank_id'));
        $bank->bank_name = $this->input->post('bank_name');
        $bank->description = $this->input->post('description');
        $bank->save();
    }


    public function delete_bank ()
    {
        $this->load->model('Bank');
        $bank = new Bank();
        if ($bank->load($this->input->post('delete_bank_id'))) {
            $bank->delete();
        }
    }

    public function list_banks ()
    {
        $this->load->model('Bank');
        $thebank = $this->Bank->get();
        foreach ($thebank as $bank) {
            inspect_object($bank);
        }
    }


    //**** ALLOWANCES ***** //

    public function allowances_list ()
    {
    $this->load->model('allowance');
    $posted_params = dataTable_post_params();
    echo $this->allowance->allowances_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function save_allowance ()
    {
        $this->load->model('allowance');
        $allowance = new Allowance();
        $edit = $allowance->load($this->input->post('allowance_id'));
        $allowance->allowance_name = $this->input->post('allowance_name');
        $allowance->description = $this->input->post('description');
        $allowance->created_by = $this->session->userdata('employee_id');
        $allowance->save();
    }

    public function delete_allowance ()
    {
        $this->load->model('allowance');
        $allowance = new Allowance();
        if ($allowance->load($this->input->post('allowance_id'))) {
            $allowance->delete();
        }
    }

    //******* LOANS ************////
    public function loan_type_list()
    {
        $this->load->model('loan');
        $posted_params = dataTable_post_params();
        echo $this->loan->loan_type_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function save_loan_type()
    {
        $this->load->model('loan');
        $loan_type = new Loan();
        $edit = $loan_type->load($this->input->post('loan_type_id'));
        $loan_type->loan_type = $this->input->post('loan_type');
        $loan_type->description = $this->input->post('description');
        $loan_type->created_by = $this->session->userdata('employee_id');
        $loan_type->save();
    }

    public function delete_loan_type ()
    {
        $this->load->model('loan');
        $loan = new Loan();
        if ($loan->load($this->input->post('loan_type_id'))) {
            $loan->delete();
        }
    }

    /*
     *  BRANCHES
     *
     */
    public function branches ()
    {
        $this->load->model('Branch');
        $data['title'] = 'Branches List';
        //print_r( $data['title']); exit();
        $this->load->view('settings/branches/branches_list', $data);

    }

    public function branches_list ()
    {
        $this->load->model('Branch');
        $posted_params = dataTable_post_params();
        echo $this->Branch->branch_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);

    }

    public function save_branch ()
    {

        $this->load->model('Branch');
        $branch = new Branch();
        $edit = $branch->load($this->input->post('branch_id'));
        $branch->branch_name = $this->input->post('branch_name');
        //$branch->created_at = $this->datetime();
        $branch->created_by = $this->session->userdata('employee_id');
        $branch->save();
    }


    public function delete_branch ()
    {
        $this->load->model('Branch');
        $branch = new Branch();
        if ($branch->load($this->input->post('delete_branch_id'))) {
            $branch->delete();
        }
    }

    public function list_branches ()
    {
        $this->load->model('Branch');
        $thebranch = $this->Branch->get();
        foreach ($thebranch as $branch) {
            inspect_object($branch);
        }
    }


    /**************
     * SSFS
     ***************/
    public function ssfs_list ()
    {
        $this->load->model('ssf');
        $posted_params = dataTable_post_params();
        echo $this->ssf->ssf_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function save_ssf ()
    {

        $this->load->model('ssf');
        $ssf = new ssf();
        $edit =  $ssf->load($this->input->post('ssf_id'));

        if($edit){
            $ssf->{$ssf::DB_TABLE_PK} = $this->input->post('ssf_id');
        }
        $ssf->ssf_name = $this->input->post('ssf_name');
        $ssf->employer_deduction_percent = $this->input->post('employer_deduction_percent');
        $ssf->employee_deduction_percent = $this->input->post('employee_deduction_percent');
        $ssf->created_by = $this->session->userdata('employee_id');
        $ssf->save();
    }

    public function delete_ssf ()
    {
        $this->load->model('ssf');
        $ssf = new ssf();
        if ($ssf->load($this->input->post('ssf_id'))) {
            $ssf->delete();
        }
    }

    public function list_ssfs ()
    {
        $this->load->model('ssf');
        $thessf = $this->ssf->get();
        foreach ($thessf as $ssf) {
            inspect_object($ssf);
        }
    }


    /**************
     * HIFS
     ***************/
    public function hifs_list ()
    {
        $this->load->model('hif');
        $posted_params = dataTable_post_params();
        echo $this->hif->hif_list($posted_params['limit'], $posted_params['start'], $posted_params['keyword'], $posted_params['order']);
    }

    public function save_hif ()
    {
        $this->load->model('hif');
        $hif = new hif();
        $edit = $hif->load($this->input->post('hif_id'));
        if($edit){
            $hif->{$hif::DB_TABLE_PK} = $this->input->post('hif_id');
        }

        $hif->hif_name = $this->input->post('hif_name');
        $hif->employer_deduction_percent = $this->input->post('employer_deduction_percent');
        $hif->employee_deduction_percent = $this->input->post('employee_deduction_percent');
        $hif->created_by = $this->session->userdata('employee_id');

        $hif->save();
    }

    public function delete_hif ()
    {
        $this->load->model('hif');
        $hif = new hif();
        if ($hif->load($this->input->post('hif_id'))) {
            $hif->delete();
        }
    }

    public function list_hifs ()
    {
        $this->load->model('hif');
        $thehif = $this->hif->get();
        foreach ($thehif as $hif) {
            inspect_object($hif);
        }
    }

    /**************
     * TAX TABLE
     ***************/

/*
    public function tax_rates_dates ()
    {
        $this->load->model('Tax_table');
        $tax_table=new Tax_table();
        $data['title'] = 'Rate List';
        $data['tax_table_data'] = $this->Tax_table->tax_table_rates();
       print_r( $data['tax_table_data']); exit();
        $this->load->view('settings/tax_tables/tax_tables_list','settings/tax_tables/tax_tables_list_actions', $data);

    }
*/
/*
    public function save_tax_rates ()
    {
        $this->load->model('Tax_table');
        $taxtable = new Tax_table();
        $edit=$taxtable->load($this->input->post('tax_table_id'));
        $taxtable->start_date = $this->input->post('start_date');
        $taxtable->end_date = $this->input->post('end_date');
        $taxtable->created_by = $this->session->userdata('employee_id');

        if ($taxtable->save()) {

            $this->load->model('Tax_table_item');
            $taxtable_item = new Tax_table_item();
            $edit_item = $taxtable_item->load($this->input->post('tax_item_id'));

            $rates = $this->input->post('rates');
            $minimums = $this->input->post('minimums');
            $maximums = $this->input->post('maximums');
            $additional_amounts = $this->input->post('additional_amounts');

            foreach ($rates as $index => $rate) {

                $taxtable_item->minimum = $minimums[$index];
                $taxtable_item->maximum = $maximums[$index];
                $taxtable_item->rate = $rates[$index];
                $taxtable_item->additional_amount = $additional_amounts[$index];
                $taxtable_item->tax_table_id = $taxtable->{$taxtable::DB_TABLE_PK};
                $taxtable_item->save();

            }

        }
    }
*/

    public function save_tax_rates ()
    {
        $this->load->model('Tax_table');
        $taxtable = new Tax_table();
        $edit=$taxtable->load($this->input->post('tax_table_id'));
        $taxtable->start_date = $this->input->post('start_date');
        $taxtable->end_date = $this->input->post('end_date');
        $taxtable->created_by = $this->session->userdata('employee_id');

        if ( $taxtable->save()){

            if($edit){
                 $this->load->model('Tax_table_item');
                //$Tax_table_item=new Tax_table_item();
                // $edit=$Tax_table_item->load($this->input->post('tax_table_id'));
                $deleted_item = $this->Tax_table_item->get(0, 0, ['tax_table_id' => $this->input->post('tax_table_id')], ' id desc');
                $Tax_table_item = $deleted_item;
                foreach ($Tax_table_item as $item){
                    $item->delete();
                }
            }


                        $this->load->model('Tax_table_item');

                        $rates = $this->input->post('rates');
                        $minimums = $this->input->post('minimums');
                        $maximums = $this->input->post('maximums');
                        $additional_amounts = $this->input->post('additional_amounts');

                        foreach ($rates as $index => $rate) {
                            $tax_table_item = new Tax_table_item();
                            $tax_table_item->minimum = $minimums[$index];
                            $tax_table_item->maximum = $maximums[$index];
                            $tax_table_item->rate = $rates[$index];
                            $tax_table_item->additional_amount = $additional_amounts[$index];
                            $tax_table_item->tax_table_id = $taxtable->{$taxtable::DB_TABLE_PK};
                            $tax_table_item->save();

                        } //save

                //save
    }

} //fn






    /*
     *
     *  if ($edit) {
                    $this->load->model('Tax_table_item');
                    $deleted_item = $this->Tax_table_item->get(0, 0, ['tax_table_id' => $this->input->post('tax_table_id')], ' id desc');
                    $Tax_table_item = $deleted_item;

                    foreach ($Tax_table_item as $item) {

                        $item->delete();

                    }
                }
     *
     *
     *
     *
     *
     *
    public function activate_employee_contract(){
        $this->load->model('Employee_contract');
        $contract = new Employee_contract();
        $edit = $contract->load($this->input->post('contract_id')); //update
        $contract->status='active';
        if($contract->save()){
            $this->load->model('Employee_contract_close');     //delete
            $closed_contract=$this->Employee_contract_close->get(1,0,['employee_contract_id'=>$this->input->post('contract_id')],' id desc');
            $Employee_contract_close = array_shift($closed_contract);
            $Employee_contract_close->delete();
        }
    }

*/



} //end class