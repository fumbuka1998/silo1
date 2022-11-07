<div style="width: 80%" class="modal-dialog">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Multiple Asset Item Registration</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Name</th><th>Under</th><th>Part Number</th><th>Description</th><th></th>
                            </tr>
                            <tr style="display: none" class="row_template" >
                                <td>
                                    <input type="text" class="form-control" required name="asset_name" >
                                </td>
                                <td style="width: 20% ">
                                    <?= form_dropdown('asset_group_id', $asset_group_options, '', ' class="form-control" ') ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control" required name="part_number">
                                </td>

                                <td>
                                    <textarea name="description" class="form-control" rows="1"></textarea>
                                </td>
                                <td>
                                    <button class="btn btn-xs btn-danger row_remover">
                                        <i class="fa fa-close"></i>
                                    </button>
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" required name="asset_name" >
                                </td>
                                <td style="width: 20% ">
                                    <?= form_dropdown('asset_group_id', $asset_group_options, '', ' class="form-control searchable" ') ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control" required name="part_number">
                                </td>
                                <td>
                                    <textarea name="description" class="form-control" rows="1"></textarea>
                                </td>
                                <td></td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="4"></td>
                                <td>
                                    <button type="button" class="btn btn-default btn-xs pull-right row_adder">Add Row</button>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_multiple_asset">Submit</button>
            </div>
        </form>
    </div>
</div>