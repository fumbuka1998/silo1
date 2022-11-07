<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/16/2018
 * Time: 4:00 PM
 */

$payment_request_approval_id = $payment_request_approval->{$payment_request_approval::DB_TABLE_PK};

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
                <a  target="_blank" href="<?= base_url('procurements/preview_approved_purchase_order_payments/'.$payment_request_approval_id )?>">
                    <i class="fa fa-clipboard"></i> Preview
                </a>
            </li>

             <?php
             if($payment_reaquest_approval_journal_vouchers) {
                 ?>
<!--                 <li>-->
<!--                        <a target="_blank"-->
<!--                           href="--><?//= base_url('Finance/preview_journal_voucher/') ?><!--">-->
<!--                            <i class="fa fa-eye"></i> Journal Voucher-->
<!--                        </a>-->
<!--                    </li>-->

                 <?php
             }

            if(!$is_cancelled && check_privilege('Make Payment') || !$is_paid_via_journal) {
                if ($invoice_payment_voucher && $invoice_payment_approval_id == $payment_request_approval_id) { ?>

                    <li>
                        <a target="_blank" href="<?= base_url('Finance/preview_payment_voucher/' . $invoice_payment_voucher->{$invoice_payment_voucher::DB_TABLE_PK}) ?>">
                            <i class="fa fa-eye"></i> Payment Voucher
                        </a>
                    </li>

                        <?php
                } else {
                      if (!$is_paid_via_journal){
                          ?>
                            <li>
                                <a class="btn btn-block btn-xs" data-toggle="modal"
                                   data-target="#make_invoice_payment_<?= $approved_invoice_item->{$approved_invoice_item::DB_TABLE_PK} ?>">
                                    <i class="fa fa-edit"></i> Make Payment
                                </a>
                            </li>
                                  <li>
                                <a class="btn btn-block btn-xs" data-toggle="modal"
                                   data-target="#journal_entry<?= $approved_invoice_item->{$approved_invoice_item::DB_TABLE_PK} ?>">
                                    <i class="fa fa-edit"></i> Journal
                                </a>
                            </li>
                          <?php
                      }
                }
            }
            ?>
        </ul>
    </div>
</span>
<div id="make_invoice_payment_<?= $approved_invoice_item->{$approved_invoice_item::DB_TABLE_PK} ?>" class="modal fade approved_invoice_payment_form" role="dialog">
    <?php
        $this->load->view('finance/payments/approved_invoice_payment_form');
    ?>
</div>
<div id="journal_entry<?= $approved_invoice_item->{$approved_invoice_item::DB_TABLE_PK} ?>" class="modal fade journal_voucher_entry_form2" role="dialog">
    <?php
        $this->load->view('finance/payments/journal_entry_form');
    ?>
</div>

<?php } ?>
