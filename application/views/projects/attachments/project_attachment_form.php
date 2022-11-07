
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Upload Attachments </h4>
        </div>
        <form enctype="multipart/form-data" method="post" action="">
            <div class="modal-body" style="overflow:auto;">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>File</th><th>Caption</th><th></th>
                    </tr>
                    <tr class="row_template hidden">
                        <td><input type="file" class="form-control" name="files[]"></td>
                        <td><input type="text" class="form-control" name="captions[]" placeholder="Caption"></td>

                        <td>
                            <button type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <input type="hidden" name="project_id" value="<?= $project->{$project::DB_TABLE_PK} ?>">
                            <input type="file" class="form-control" name="files[]">
                        </td>
                        <td><input type="text" class="form-control" name="captions[]" placeholder="Caption"></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="add_upload_file btn btn-default btn-sm">
                    <i class="fa fa-plus"></i> Add File
                </button>
                <button type="button" attachment_id="" class="btn btn-default btn-sm project_attach">
                    <i class="fa fa-upload"></i> Upload
                </button>
            </div>
        </form>
        <hr/>

    </div>
</div>