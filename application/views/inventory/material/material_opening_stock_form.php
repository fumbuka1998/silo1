<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Material Opening Stock</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="project_id" class="control-label">Project</label>
                            <?= form_dropdown('project_id',$project_options,'','  " sub_location_id = "'.$sub_location->{$sub_location::DB_TABLE_PK}.'"  class="form-control  searchable opening_stock_project_selector"') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="date" class="control-label">Date</label>
                            <input type="text" class="form-control datetime_picker" required name="date" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-bordered table-hover" style="table-layout: fixed;">
                                <thead>
                                <tr>
                                    <th style="width: 40%">Material</th><th>Quantity</th><th style="width: 8%">Unit</th><th>Price</th><th>Remarks</th><th style="width: 5%"></th>
                                </tr>
                                <tr style="display: none" class="row_template">
                                    <td>
                                        <?= form_dropdown('item_id',['' => '&nbsp;'],'', 'class=" form-control" ') ?>
                                    </td>
                                    <td><input type="text" class="form-control" name="quantity" value=""></td>
                                    <td class="unit_display"></td>
                                    <td><input type="text" class="form-control number_format" name="rate" value=""></td>
                                    <td><textarea name="remarks" rows="1" class="form-control"></textarea></td>
                                    <td>
                                        <button class="btn btn-xs btn-danger row_remover">
                                            <i class="fa fa-close"></i>
                                        </button>
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?= form_dropdown('item_id',[],'',' class="material_selector form-control searchable"') ?></td>
                                    <td><input type="text" class="form-control" name="quantity" value=""></td>
                                    <td class="unit_display"></td>
                                    <td><input type="text" class="form-control number_format" name="rate" value=""></td>
                                    <td><textarea name="remarks" rows="1" class="form-control"></textarea></td>
                                    <td></td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="4"></th>
                                    <td colspan="2">
                                        <span class="pull-right">
                                            <button type="button" class="btn btn-default btn-xs row_adder">
                                                <i class="fa fa-plus"></i> Add Row
                                            </button>
                                        </span>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default save_material_opening_stock">Submit</button>
            </div>
        </form>
    </div>
</div>