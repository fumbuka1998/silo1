<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/6/2018
 * Time: 12:06 PM
 */

?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Attachments</h4>
        </div>
        <div class="modal-body" style="overflow:auto;">
            <?php if($payment_request->requester_id == $this->session->userdata('employee_id') || check_permission('Administrative Actions')){ ?>
            <form class="purchase_order_payment_request_attachment_form">
                <div class="form-group col-md-4">
                    <input type="file" name="file" class="form-control">
                    <input type="hidden" name="purchase_order_payment_request_id" value="<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>">
                </div>
                <div class="form-group col-md-7">
                    <input type="text" name="caption" class="form-control col-md-6" placeholder="Caption">
                </div>
                <button type="button" class="btn btn-primary btn-sm purchase_order_payment_request_attach">
                    <i class="fa fa-upload"></i> Upload
                </button>
            </form>
            <hr/>
            <?php } ?>
            <div class="payment_request_attachments_container table-responsive col-xs-12" purchase_order_payment_request_id="<?= $payment_request->{$payment_request::DB_TABLE_PK} ?>">
                <?php $this->load->view('procurements/order_payment_requests/purchase_order_payment_request_attachments_table',['payment_request'=>$payment_request]); ?>
            </div>
        </div>

        <div class="modal-footer">

        </div>
    </div>
</div>