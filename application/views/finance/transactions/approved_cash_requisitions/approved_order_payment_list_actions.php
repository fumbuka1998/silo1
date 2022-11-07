<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 12/06/2018
 * Time: 13:00
 */

if(check_privilege('Finance Actions')){
?>
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
                <a  target="_blank" href="<?= base_url('procurements/preview_approved_purchase_order_payments/'.$requisition_approval_id)?>">
                    <i class="fa fa-clipboard"></i> Preview
                </a>
            </li>
            <?php
            if(!$is_cancelled && check_privilege('Make Payment')) {
                if (!$approval_payment_voucher) { ?>
                    <li>
                        <a class="btn btn-block btn-xs" data-toggle="modal"
                           data-target="#payment_voucher_<?= $requisition_approval_id . '_' . $account_id ?>">
                            <i class="fa fa-edit"></i> Make Payment
                        </a>
                    </li>
                <?php } else {
                    ?>
                    <li>
                        <a target="_blank"
                           href="<?= base_url('finance/preview_payment_voucher/' . $approval_payment_voucher->payment_voucher_id) ?>">
                            <i class="fa fa-eye"></i> Payment Voucher
                        </a>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>

    </div>
</span>
<?php  if(!$approval_payment_voucher){ ?>
    <div id="payment_voucher_<?= $requisition_approval_id.'_'.$account_id ?>" class="modal fade payment_request_payment_voucher_form" role="dialog">
        <?php
        $data['payment_request_approval'] = $payment_request_approval;
        $this->load->view('finance/transactions/approved_cash_requisitions/payment_request_payment_voucher_form',$data);
        ?>
    </div>
<?php  }
} ?>
