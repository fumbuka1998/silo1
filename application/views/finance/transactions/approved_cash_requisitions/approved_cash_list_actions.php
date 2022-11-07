<?php if(check_privilege('Finance Actions')){ ?>
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
                <a  target="_blank" href="<?= base_url('requisitions/preview_approved_cash_requisition/'.$requisition_approval_id.'/'.$account_id)?>">
                    <i class="fa fa-clipboard"></i> Preview
                </a>
            </li>
            <?php
            $imprest_voucher_id = $requisition_approval->imprest_voucher($requisition_approval_id);
            if($requisition_approval_payment_voucher && check_privilege('Make Payment')){
                ?>
                <li>
                <a  target="_blank" href="<?= base_url('finance/preview_payment_voucher/'.$requisition_approval_payment_voucher->payment_voucher_id)?>">
                    <i class="fa fa-eye"></i> Payment Voucher
                </a>
            </li>
                <?php
                $payment_voucher = $requisition_approval_payment_voucher->payment_voucher();
            } else if(!is_null($imprest_voucher_id) && check_privilege('Make Payment')){
                ?>
                <li>
                <a  class="btn btn-default btn-xs" target="_blank" href="<?= base_url('finance/preview_imprest_voucher/'.$imprest_voucher_id ) ?>">
                    <i class="fa fa-clipboard"></i>Sheet
                </a>
            </li>
                <?php
            } else {
                if(!$is_cancelled && check_privilege('Make Payment')){
                    if(!$has_stock_items){
                        ?>
                        <li>
                <a class="btn btn-block btn-xs" data-toggle="modal" data-target="#payment_voucher_<?= $requisition_approval_id.'_'.$account_id ?>">
                    <i class="fa fa-edit"></i> Make Payment
                </a>
            </li>
                        <?php
                    } ?>
                <li>
                    <a class="btn btn-block btn-xs" data-toggle="modal" data-target="#imprest_voucher_<?= $requisition_approval_id ?>">
                        <i class="fa fa-exchange"></i> Create Imprest
                    </a>
                </li>
                    <?php
                }
            }

            if($imprest_voucher_id && isset($retirements) && check_privilege('Make Payment')) {?>
                <li>
                        <a class="btn btn-block btn-xs" data-toggle="modal" data-target="#retirement_examination_modal_<?= $imprest_voucher_id ?>">
                            <i class="fa fa-reorder"></i> Examine Retirement
                        </a>
                    </li>
                <?php
            }
            ?>
        </ul>

    </div>
</span>
    <?php if(!$requisition_approval_payment_voucher) { ?>
        <div id="payment_voucher_<?= $requisition_approval_id . '_' . $account_id ?>"
             class="modal fade payment_voucher_form" role="dialog">
            <?php $this->load->view('finance/transactions/approved_cash_requisitions/payment_voucher_form'); ?>
        </div>
        <div id="imprest_voucher_<?= $requisition_approval_id ?>" class="modal fade imprest_voucher_form" role="dialog">
            <?php
            $this->load->view('finance/transactions/approved_cash_requisitions/imprest/imprest_voucher_form');
            ?>
        </div>
        <?php
        if (isset($retirements)) {
            ?>
            <div id="retirement_examination_modal_<?= $imprest_voucher_id ?>"
                 class="modal fade retirement_examination_modal" role="dialog">
                <?php
                $this->load->view('finance/transactions/approved_cash_requisitions/imprest/retirement_examination_modal');
                ?>
            </div>
        <?php } ?>
        <?php
    }
} ?>






