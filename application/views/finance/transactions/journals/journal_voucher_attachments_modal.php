<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 05/05/2019
 * Time: 07:27
 */

?>

<div class="modal-dialog" style="width: 60%">
    <form>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $jv_transaction->jv_number() ?> Attachments</h4>
            </div>
            <div class="modal-body" style="overflow:auto;">
                <?php if($jv_transaction->created_by == $this->session->userdata('employee_id') || check_permission('Administrative Actions')){ ?>
                    <form class="jv_attachment_form">
                        <div class="form-group col-md-4">
                            <input type="file" name="file" class="form-control">
                            <input type="hidden" name="journal_voucher_id" value="<?= $jv_transaction->{$jv_transaction::DB_TABLE_PK} ?>">
                        </div>
                        <div class="form-group col-md-7">
                            <input type="text" name="caption" class="form-control col-md-6" placeholder="Caption">
                        </div>
                        <button type="button" class="btn btn-primary btn-sm jv_attach">
                            <i class="fa fa-upload"></i> Upload
                        </button>
                    </form>
                    <hr/>
                <?php } ?>
                <div class="jv_attachments_container table-responsive col-xs-12" journal_voucher_id ="<?= $jv_transaction->{$jv_transaction::DB_TABLE_PK} ?>">
                    <?php $this->load->view('finance/transactions/journals/journal_voucher_attachments',$jv_transaction); ?>
                </div>
            </div>

            <div class="modal-footer">
            </div>
        </div>
    </form>
</div>
