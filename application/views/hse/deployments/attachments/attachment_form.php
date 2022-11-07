<?php
?>
<div class="modal-dialog" style="width: 70%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Upload Attachments </h4>
        </div>
        <form enctype="multipart/form-data" method="post" action="">
            <div class="modal-body" style="overflow:auto;">
                <table class="table table-hover" style="table-layout: fixed">
                    <thead>
                    <tr>
                        <th style="width: 30%">File</th><th>Caption</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <input type="hidden" name="deployment_id" value="<?= $deployment->{$deployment::DB_TABLE_PK} ?>">
                            <input type="file" class="form-control" name="files[]">
                        </td>
                        <td><textarea rows="1" type="text" class="form-control" name="captions[]" placeholder="Caption"></textarea></td>
                    </tr>
                    </tbody>
                </table>
                <div class=" table-responsive col-xs-12" deployment_id="<?= $deployment->{$deployment::DB_TABLE_PK} ?>">
                    <?php $this->load->view('hse/deployments/attachments/attachments',$deployment); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" attachment_id="" class="btn btn-default btn-sm deployment_attachment">
                    <i class="fa fa-upload"></i> Upload
                </button>
            </div>
        </form>
        <hr/>

    </div>
</div>
