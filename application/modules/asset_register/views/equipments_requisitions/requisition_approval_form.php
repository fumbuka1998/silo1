<?php
    $source_types_options = [
        '' => '',
        'vendor' => 'Vendor',
        'cash' => 'Cash',
        'store' => 'Store'
    ];

    $last_approval_id =  $last_approval ? $last_approval->{$last_approval::DB_TABLE_PK} : 0;

?>
<div class="modal-dialog" style="width: 90%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Requisition Approval</h4>
        </div>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">
                    <div class="form-group col-md-3">
                        <label for="approve_date" class="control-label">Approve Date</label>
                        <input type="hidden" name="requisition_id" value="<?= $requisition->{$requisition::DB_TABLE_PK} ?>">
                        <input type="hidden" name="has_sources" value="<?= $current_approval_level->change_source ? 'true' : 'false' ?>">
                        <input type="hidden" name="approval_chain_level_id" value="<?= $current_approval_level->{$current_approval_level::DB_TABLE_PK} ?>">
                        <input type="text" class="form-control datepicker" required name="approve_date" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="col-xs-12 table-responsive">
                    <?php
                    $material_items = $requisition->material_items();
                    $cash_items = $requisition->cash_items();

                    if($current_approval_level->change_source){ ?>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Item Information</th><th>Sources Information</th>
                            </tr>
                            <tr class="material_source_row_template" style="display: none">
                                <td>
                                    <?= form_dropdown('source_type',$source_types_options,'',' class="form-control" ') ?>
                                </td>
                                <td>
                                    <?= form_dropdown('source',$vendor_options,'',' class="form-control" ') ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control source_approved_quantity" required name="quantity" value="">
                                </td>
                                <td>
                                    <input type="text" class="form-control number_format" required name="rate" value="">
                                </td>
                                <td>
                                    <?= form_dropdown('currency_id',$currency_options,'',' class="form-control" ') ?>
                                </td>
                                <td>
                                    <button class="btn btn-xs btn-default row_remover"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            <tr class="cash_source_row_template" style="display: none">
                                <td>
                                    <?= form_dropdown('source',$account_options,'',' class="form-control" ') ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control source_approved_quantity" required name="quantity" value="">
                                </td>
                                <td>
                                    <input type="text" class="form-control number_format" required name="rate" value="">
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
                                    <?= form_dropdown('cashbook_options',$account_options,'') ?>
                                    <?= form_dropdown('vendor_options',$vendor_options,'') ?>
                                    <?= form_dropdown('main_location_options',$main_location_options,'') ?>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach ($material_items as $item){
                           ?>
                                <tr>
                                    <td>
                                        <?= $item->material_item()->item_name ?><hr/>
                                        <div class="form-group col-xs-12">
                                            <label for="expense_account_id" class="control-label">Expense Account</label>
                                            <?= form_dropdown('expense_account_id', $expense_accounts_options, $item->expense_account_id, ' class="form-control searchable"') ?>
                                            <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                            <input type="hidden" name="item_type" value="material">
                                        </div>
                                    </td>
                                    <td width="75%">
                                        <table class="table table-bordered table-hover sources_table">
                                            <thead>
                                                <tr>
                                                    <th>Source Type</th><th width="40%">Source/Vendor</th><th>Quantity</th><th>Rate</th>
                                                    <th>Currency</th>
                                                    <th>
                                                        <button title="Add Source" class="btn btn-default btn-xs pull-right material_source_adder"><i class="fa fa-plus"></i></button>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            if($last_approval) {
                                                $approved_material_items = $last_approval->material_items($item->{$item::DB_TABLE_PK});
                                                foreach ($approved_material_items as $approved_material_item){
                                                    if($approved_material_item->source_type == 'store'){
                                                        $source_id = $approved_material_item->location_id;
                                                    } else if($approved_material_item->source_type == 'vendor'){
                                                        $source_id = $approved_material_item->vendor_id;
                                                    } else {
                                                        $source_id = $approved_material_item->account_id;
                                                    }

                                                    $approved_item = $item->approved_item($last_approval_id,$source_id,$approved_material_item->source_type);
                                                    $quantity = $approved_item->approved_quantity;
                                                    $rate = $approved_item->approved_rate;
                                                    $currency_id = $approved_item->currency_id;
                                                    $source_options = [];
                                                    $source_id = '';
                                                    if($approved_item->source_type == 'vendor'){
                                                        $source_options = $vendor_options;
                                                        $source_id = $approved_item->vendor_id;
                                                    } else if($approved_item->source_type == 'cash'){
                                                        $source_options = $account_options;
                                                        $source_id = $approved_item->account_id;
                                                    } else if($approved_item->source_type == 'store'){
                                                        $source_options = $main_location_options;
                                                        $source_id = $approved_item->location_id;
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?= form_dropdown('source_type',$source_types_options,$approved_item->source_type,' class="form-control" ') ?>
                                                        </td>
                                                        <td class="sources_container">
                                                            <?= form_dropdown('source',$source_options,$source_id,' class="form-control searchable" ') ?>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control source_approved_quantity" required name="quantity" value="<?= $approved_item->approved_quantity?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control money" required name="rate" value="<?= $approved_item->approved_rate?>">
                                                        </td>
                                                        <td>
                                                            <?= form_dropdown('currency_id',$currency_options,$approved_item->currency_id,' class="form-control" ') ?>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?= form_dropdown('source_type',$source_types_options,'vendor',' class="form-control" ') ?>
                                                    </td>
                                                    <td class="sources_container">
                                                        <?= form_dropdown('source',$vendor_options,$item->requested_vendor_id,' class="form-control searchable" ') ?>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control source_approved_quantity" required name="quantity" value="<?= $item->requested_quantity ?>">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control money" required name="rate" value="<?= $item->requested_rate ?>">
                                                    </td>
                                                    <td>
                                                        <?= form_dropdown('currency_id',$currency_options,$item->requested_currency_id,' class="form-control" ') ?>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                        <?php
                            }

                            foreach ($cash_items as $item){
                                ?>
                                <tr>
                                    <td>
                                        <?= $item->description ?><hr/>
                                        <div class="form-group col-xs-12">
                                            <label for="expense_account_id" class="control-label">Expense Account</label>
                                            <?= form_dropdown('expense_account_id', $expense_accounts_options, $item->expense_account_id, ' class="form-control searchable"') ?>
                                            <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                            <input type="hidden" name="item_type" value="cash">
                                        </div>
                                    </td>
                                    <td width="75%">
                                        <table class="table table-bordered table-hover sources_table">
                                            <thead>
                                            <tr>
                                                <th width="40%">Source Account</th><th>Quantity</th><th>Rate</th>
                                                <th>Currency</th>
                                                <th>
                                                    <button title="Add Source" class="btn btn-default btn-xs pull-right cash_source_adder"><i class="fa fa-plus"></i></button>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            if($last_approval) {
                                                $approved_cash_items = $last_approval->cash_items($item->{$item::DB_TABLE_PK});
                                                foreach ($approved_cash_items as $approved_cash_item){
                                                    $approved_item = $item->approved_item($last_approval_id,$approved_cash_item->account_id);

                                                    ?>
                                                    <tr>
                                                        <td class="sources_container">
                                                            <?= form_dropdown('source',$account_options,$approved_cash_item->account_id,' class="form-control searchable" ') ?>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control source_approved_quantity" required name="quantity" value="<?= $approved_item->approved_quantity ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control number_format" required name="rate" value="<?= $approved_item->approved_rate ?>">
                                                        </td>
                                                        <td>
                                                            <?= form_dropdown('currency_id',$currency_options,$approved_item->currency_id,' class="form-control" ') ?>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-xs btn-default row_remover">
                                                                <i class="fa fa-close"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td class="sources_container">
                                                        <?= form_dropdown('source',$account_options,'',' class="form-control searchable" ') ?>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control source_approved_quantity" required name="quantity" value="<?= $item->requested_quantity?>">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control number_format" required name="rate" value="<?= $item->requested_rate ?>">
                                                    </td>
                                                    <td>
                                                        <?= form_dropdown('currency_id',$currency_options,$item->requested_currency_id,' class="form-control" ') ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-xs btn-default row_remover">
                                                            <i class="fa fa-close"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                        <?php
                            }
                        ?>
                        </tbody>
                    </table>
                    <?php } else {
                        ?>
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Item Description</th><th>Quantity</th><th>Price</th><th>Amount</th><th>Currency</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                foreach ($material_items as $item){
                                    if($last_approval) {
                                        $approved_item = $item->approved_item($last_approval_id);
                                        $quantity = $approved_item->approved_quantity;
                                        $rate = $approved_item->approved_rate;
                                        $currency_id = $approved_item->currency_id;
                                    } else {
                                        $quantity = $item->requested_quantity;
                                        $rate = $item->requested_rate;
                                        $currency_id = $item->requested_currency_id;
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <?= $item->material_item()->item_name ?>
                                            <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                            <input type="hidden" name="item_type" value="material">
                                        </td>
                                        <td><input name="quantity" class="form-control" value="<?= $quantity ?>"></td>
                                        <td><input name="rate" class="number_format form-control" value="<?= $rate ?>"></td>
                                        <td><input readonly name="amount" class="number_format form-control" value="<?= $rate*$quantity ?>"></td>
                                        <td><?= form_dropdown('currency_id', $currency_options, $currency_id, ' class="form-control searchable" ') ?></td>
                                    </tr>
                            <?php
                                }

                            foreach ($cash_items as $item){
                                if($last_approval) {
                                    $approved_item = $item->approved_item($last_approval_id);
                                    $quantity = $approved_item->approved_quantity;
                                    $rate = $approved_item->approved_rate;
                                    $currency_id = $approved_item->currency_id;
                                } else {
                                    $quantity = $item->requested_quantity;
                                    $rate = $item->requested_rate;
                                    $currency_id = $item->requested_currency_id;
                                }
                                ?>
                                <tr>
                                    <td>
                                        <?= $item->description ?>
                                        <input type="hidden" name="item_id" value="<?= $item->{$item::DB_TABLE_PK} ?>">
                                        <input type="hidden" name="item_type" value="cash">
                                    </td>
                                    <td><input name="quantity" class="form-control" value="<?= $quantity ?>"></td>
                                    <td><input name="rate" class="number_format form-control" value="<?= $rate ?>"></td>
                                    <td><input name="amount" readonly class="number_format form-control" value="<?= $rate*$quantity ?>"></td>
                                    <td><?= form_dropdown('currency_id', $currency_options, $currency_id, ' class="form-control searchable" ') ?></td>
                                </tr>
                            <?php
                            }


                            ?>
                            </tbody>
                        </table>
                    <?php
                    } ?>
                </div>
                <div class="col-xs-12">
                    <div class="form-group">
                        <label for="comments" class="control-label">Approving Comments</label>
                        <textarea name="comments" class="form-control"><?= $last_approval ? $last_approval->approving_comments : $requisition->requesting_comments ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm approve_requisition">Submit Approval</button>
        </div>
    </div>
</div>