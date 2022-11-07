<span>

<div class="btn-group">
    <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
        Actions
    </button>
    <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>

    <ul class="dropdown-menu" role="menu">

         <li>
            <a  target="_blank" href="<?= base_url('requisitions/preview_approved_requisition/'.$requisition_approval_id.'/'.$account_id)?>">
                <i class="fa fa-clipboard"></i> Preview
            </a>
        </li>
        <li>
                <a class="btn btn-block btn-xs" data-toggle="modal" data-target="#payment_voucher<?= $requisition_approval_id.'-'.$account_id ?>">
                    <i class="fa fa-edit"></i> Payment Voucher
                </a>



        </li>

        <li>
            <a class="btn btn-block btn-xs" data-toggle="modal" data-target="#imprest_voucher<?= $requisition_approval_id.'-'.$account_id ?>">
                <i class="fa fa-edit"></i> Imprest Voucher
            </a>

            
        </li>

    </ul>

</div>

</span>


    <div id="payment_voucher<?= $requisition_approval_id.'-'.$account_id ?>" class="modal fade payment_voucher_form" role="dialog">

         <?php $this->load->view('finance/account_profile/approved_cash/payment_voucher_form'); ?>
    </div>

      <div id="imprest_voucher<?= $requisition_approval_id.'-'.$account_id ?>" class="modal fade imprest_voucher_form" role="dialog">
          <?php $this->load->view('finance/account_profile/approved_cash/imprest/imprest_voucher_form'); ?>
    </div>

   

                                   
    

