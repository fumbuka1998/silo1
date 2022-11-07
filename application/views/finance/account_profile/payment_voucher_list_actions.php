<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 11/22/2016
 * Time: 3:06 PM
 */

?>

<span class="pull-right">
    <a class="btn btn-xs btn-default" target="_blank" href="<?= base_url('finance/preview_payment_voucher/'.$payment_voucher->{$payment_voucher::DB_TABLE_PK}) ?>">
        <i class="fa fa-eye"></i>
    </a>
<?php

if($payment_voucher->employee_id == $this->session->userdata('employee_id') || check_permission('Administrative Actions')) {
    ?>
    <!--<button title="edit" data-toggle="modal"
            data-target="#edit_payment_voucher_<?/*= $payment_voucher->{$payment_voucher::DB_TABLE_PK} */?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i>
    </button>
    <div id="edit_payment_voucher_<?/*= $payment_voucher->{$payment_voucher::DB_TABLE_PK} */?>" class="modal fade" role="dialog">
        <?php
/*        $pv_form = $payment_voucher->payment_voucher_type == 'VENDOR' ? 'vendor_payment_voucher_form' : 'expense_payment_voucher_form';
        $this->load->view('finance/account_profile/' . $pv_form);
        */?>
    </div>-->
    <button title="delete" class="btn btn-danger btn-xs delete_payment_voucher"
            payment_voucher_id="<?= $payment_voucher->{$payment_voucher::DB_TABLE_PK} ?>">
        <i class="fa fa-trash"></i>
    </button>
    <?php
}
?>
</span>
