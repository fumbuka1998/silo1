<?php ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Attachments</h4>
        </div>
        <div class="modal-body" style="overflow:auto;">
            <?php if ($reffering_object->creator() == $this->session->userdata('employee_id') || check_permission('Administrative Actions')) { ?>
                <form class="procurements_attachment_form">
                    <div class="form-group col-md-4">
                        <input type="file" name="file" class="form-control">
                        <input type="hidden" name="reffering_id" value="<?= $reffering_object->{$reffering_object::DB_TABLE_PK} ?>">
                        <input type="hidden" name="reffering_to" value="<?= $reffering_to ?>">
                    </div>
                    <div class="form-group col-md-7">
                        <input type="text" name="caption" class="form-control col-md-6" placeholder="Caption">
                    </div>
                    <button type="button" class="btn btn-primary btn-sm procurements_attach">
                        <i class="fa fa-upload"></i> Upload
                    </button>
                </form>
                <hr />
            <?php } ?>
            <div class="procurements_attachments_container table-responsive col-xs-12" reffering_id="<?= $reffering_object->{$reffering_object::DB_TABLE_PK} ?>">
                <?php $this->load->view('attachments/purchase_order_related/attachment_table'); ?>
            </div>
        </div>

        <div class="modal-footer">

        </div>
    </div>
</div>