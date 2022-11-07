<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Attachments</h4>
        </div>
        <div class="modal-body" style="overflow:auto;">
            <form class="requisition_attachment_form">
                <div class="form-group col-md-4">
                    <input type="file" name="file" class="form-control">
                    <input type="hidden" name="requisition_id" value="<?= $requisition->{$requisition::DB_TABLE_PK} ?>">
                </div>
                <div class="form-group col-md-7">
                    <input type="text" name="caption" class="form-control col-md-6" placeholder="Caption">
                </div>
                <button type="button" class="btn btn-primary btn-sm requisition_attach">
                    <i class="fa fa-upload"></i> Upload
                </button>
            </form>
            <hr/>
            <div class="requisition_attachments_container table-responsive col-xs-12" requisition_id="<?= $requisition->{$requisition::DB_TABLE_PK} ?>">
                <?php $this->load->view('requisitions/requisition_attachments',$requisition); ?>
            </div>
        </div>

        <div class="modal-footer">

        </div>
    </div>
</div>