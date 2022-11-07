<div style="width: 80%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Asset Registration</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12 table-responsive">
                        <input type="hidden" name="sub_location_id" value="<?= $sub_location->{$sub_location::DB_TABLE_PK} ?>">
                        <table class="table table-bordered table-hover" style="table-layout: fixed;">
                            <thead>
                            <tr>
                                <th>Registration Date</th><th style="width: 20% ">Asset Type</th><th>Asset Code</th><th>Book Value</th><th>Salvage Value</th><th>Description</th><th>Status</th><th style="width: 5% "></th>
                            </tr>
                            <tr class="row_template" style="display: none">
                                <td>
                                    <input name="registration_date" class="form-control datepicker">
                                </td>
                                <td>
                                    <?= form_dropdown('asset_item_id', $asset_items_options, '', ' class="form-control" ') ?>
                                </td>
                                <td>
                                    <input name="asset_code" class="form-control">
                                    <input type="hidden" name="ownership" value="OWNED">
                                </td>
                                <td>
                                    <input name="book_value" class="form-control number_format">
                                </td>
                                <td>
                                    <input name="salvage_value" class="form-control number_format">
                                </td>
                                <td>
                                    <textarea name="description" class="form-control" rows="1"></textarea>
                                </td>
                                <td>
                                    <select class="form-control" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="dispose">Dispose</option>
                                    </select>
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
                                    <input name="registration_date" class="form-control datepicker">
                                </td>
                                <td>
                                    <?= form_dropdown('asset_item_id', $asset_items_options, '', ' class="form-control searchable" ') ?>
                                </td>
                                <td>
                                    <input name="asset_code" class="form-control">
                                    <input type="hidden" name="ownership" value="OWNED">
                                </td>
                                <td>
                                    <input name="book_value" class="form-control number_format">
                                </td>
                                <td>
                                    <input name="salvage_value" class="form-control number_format">
                                </td>
                                <td>
                                    <textarea name="description" class="form-control" rows="1"></textarea>
                                </td>
                                <td>
                                    <?php
                                    $options['active']='Active';
                                    $options['inactive']='Inactive';
                                    $options['disposed']='Disposed';
                                    echo form_dropdown('status',$options,'',' class="form-control searchable" ');
                                    ?>
                                </td>
                                <td></td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2">
                                    <button type="button" class="btn btn-default btn-xs pull-right row_adder">Add Row</button>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_assets_registrations">Submit</button>
            </div>
        </form>
    </div>
</div>