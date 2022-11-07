<?php
    $vendor_options = $vendor_options;
    $source_types_options = [
        'vendor' => 'Vendor',
        'cash' => 'Cash'
    ];

    if($location->project_id != null){
        $source_types_options = $source_types_options + [
                'store' => 'Store'
            ];
    }

?>
<div class="modal-dialog" style="width: 90%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Material Requisition Approval</h4>
        </div>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-3">
                        <label for="approve_date" class="control-label">Approve Date</label>
                        <input type="hidden" name="requisition_id" value="<?= $requisition->{$requisition::DB_TABLE_PK} ?>">
                        <input type="text" class="form-control datepicker" required name="approve_date" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="col-xs-12 table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Item Information</th><th>Sources Information</th>
                            </tr>
                            <tr class="source_row_template" style="display: none">
                                <td>
                                    <?= form_dropdown('source_type',$source_types_options,'',' class="form-control" ') ?>
                                </td>
                                <td>
                                    <?= form_dropdown('source',$vendor_options,'',' class="form-control" ') ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control number_format source_approved_quantity" required name="source_approved_quantity" value="">
                                </td>
                                <td>
                                    <input type="text" class="form-control number_format" required name="approved_price" value="">
                                </td>
                                <td>
                                    <?= form_dropdown('currency_id',$currency_options,'',' class="form-control" ') ?>
                                </td>
                                <td>
                                    <button class="btn btn-xs btn-default row_remover"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            <tr class="sources_options_templates" style="display: none">
                                <td>
                                    <?= form_dropdown('cashbook_options',$cashbook_options,'') ?>
                                    <?= form_dropdown('vendor_options',$vendor_options,'') ?>
                                    <?= form_dropdown('main_location_options',$main_location_options,'') ?>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $material_items = $requisition->material_items();
                            foreach ($material_items as $item){
                           ?>
                                <tr>
                                    <td>
                                        <?= $item->material_item()->item_name ?><hr/>
                                        <div class="form-group col-xs-6">
                                            <label for="requested_quantity" class="control-label">Requested Quantity</label>
                                            <input name="requested_quantity" readonly value="<?= $item->requested_quantity ?>" class="form-control">
                                            <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                        </div>
                                        <div class="form-group col-xs-6">
                                            <label for="approved_quantity" class="control-label">Approved Quantity</label>
                                            <input name="approved_quantity" value="<?= $item->requested_quantity ?>" class="form-control">
                                        </div>
                                    </td>
                                    <td width="75%">
                                        <table class="table table-bordered table-hover sources_table">
                                            <thead>
                                                <tr>
                                                    <th>Source Type</th><th width="40%">Source/Vendor</th><th>Quantity</th><th>Price</th>
                                                    <th>Currency</th>
                                                    <th>
                                                        <button title="Add Source" class="btn btn-default btn-xs pull-right source_adder"><i class="fa fa-plus"></i></button>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <?= form_dropdown('source_type',$source_types_options,'',' class="form-control" ') ?>
                                                    </td>
                                                    <td class="sources_container">
                                                        <?= form_dropdown('source',$vendor_options,$item->requested_vendor_id,' class="form-control searchable" ') ?>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control number_format source_approved_quantity" required name="source_approved_quantity" value="">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control number_format" required name="approved_price" value="<?= $item->requested_price ?>">
                                                    </td>
                                                    <td>
                                                        <?= form_dropdown('currency_id',$currency_options,$item->requested_vendor_id,' class="form-control" ') ?>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                        <?php
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-xs-12">
                    <div class="form-group">
                        <label for="comments" class="control-label">Approving Comments</label>
                        <textarea name="comments" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm approve_requisition">Submit Approval</button>
        </div>
    </div>
</div>