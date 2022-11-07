<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/25/2018
 * Time: 1:47 PM
 */

?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Attachments</h4>
        </div>
        <div class="modal-body" style="overflow:auto;">
            <?php if($requisition->requester_id == $this->session->userdata('employee_id') || check_permission('Administrative Actions')){ ?>
            <form class="requisition_attachment_form">
                <div class="form-group col-md-4">
                    <input type="file" name="file" class="form-control">
                    <input type="hidden" name="sub_contract_requisition_id" value="<?= $requisition->{$requisition::DB_TABLE_PK} ?>">
                </div>
                <div class="form-group col-md-7">
                    <input type="text" name="caption" class="form-control col-md-6" placeholder="Caption">
                </div>
                <button type="button" class="btn btn-primary btn-sm sub_contract_requisition_attach">
                    <i class="fa fa-upload"></i> Upload
                </button>
            </form>
            <hr/>
            <?php } ?>
            <div class="srq_attachments_container table-responsive col-xs-12" sub_contract_requisition_id="<?= $requisition->{$requisition::DB_TABLE_PK} ?>">
                <?php $this->load->view('requisitions/requisitions_list/requisition_attachments',$requisition); ?>
            </div>
        </div>

        <div class="modal-footer">

        </div>
    </div>
</div>