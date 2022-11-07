<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/25/2018
 * Time: 10:37 PM
 */

$sub_contract_requisition_approval_id = $sub_contract_requisition_approval->{$sub_contract_requisition_approval::DB_TABLE_PK};
if(check_privilege('Finance Actions')) {
    ?>
    <span>
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
            Actions
        </button>
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>

        <ul class="dropdown-menu" role="menu">
             <li>
                <a target="_blank"
                   href="<?= base_url('requisitions/preview_approved_sub_contract_payment_requsition/' . $sub_contract_requisition_approval_id) ?>">
                    <i class="fa fa-clipboard"></i> Preview
                </a>
            </li>
            <?php
            if (!$is_cancelled && check_privilege('Make Payment')) {
                if ($payment_voucher && $paid_certificate && $paid_approved_item) {
                    ?>
                    <li>
                        <a target="_blank"
                           href="<?= base_url('Finance/preview_payment_voucher/' . $payment_voucher->{$payment_voucher::DB_TABLE_PK}) ?>">
                            <i class="fa fa-eye"></i> Payment Voucher
                        </a>
                    </li>
                     <?php if ($payment_voucher->withholding_tax > 0 && $withholding_tax->status == "PENDING" ) {
                        ?>
                        <li>
                            <a class="btn btn-block btn-xs" data-toggle="modal"
                               data-target="#pay_withholding_<?= $sub_contract_requisition_approved_item->{$sub_contract_requisition_approved_item::DB_TABLE_PK} ?>">
                                <i class="fa fa-edit"></i> Pay Withholding Tax
                            </a>
                         </li>
                        <?php
                    }
                } else {
                    ?>
                    <li>
                        <a class="btn btn-block btn-xs" data-toggle="modal"
                           data-target="#make_sub_contract_payment_<?= $sub_contract_requisition_approved_item->{$sub_contract_requisition_approved_item::DB_TABLE_PK} ?>">
                            <i class="fa fa-edit"></i> Make Payment
                        </a>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
    </div>

</span>
    <div id="make_sub_contract_payment_<?= $sub_contract_requisition_approved_item->{$sub_contract_requisition_approved_item::DB_TABLE_PK} ?>"
         class="modal fade approved_sub_contract_payment_form" role="dialog">
        <?php
        $this->load->view('finance/sub_contract_payments/approved_sub_contract_payment_form');
        ?>
    </div>
    <?php
    if($payment_voucher && $paid_certificate && $paid_approved_item){
        if ($payment_voucher->withholding_tax > 0) {
            ?>
            <div id="pay_withholding_<?= $sub_contract_requisition_approved_item->{$sub_contract_requisition_approved_item::DB_TABLE_PK} ?>"
                 class="modal fade" role="dialog">
                <?php
                $this->load->view('finance/sub_contract_payments/approved_sub_contract_withholding_tax_payment_form'); ?>
            </div>
    <?php }
    }
} ?>