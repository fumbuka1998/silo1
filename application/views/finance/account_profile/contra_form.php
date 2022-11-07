<?php
    $edit = isset($contra)
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Contra Form</h4>
        </div>
        <form>
        <div class="modal-body">
            <div class='row'>
                <div class="col-xs-12">

                    <div class="form-group col-md-4">
                        <label for="contra_date" class="control-label">Contra Date</label>
                        <input type="text" class="form-control datepicker" required name="contra_date" value="<?= $edit ? $contra->contra_date : date('Y-m-d') ?>">
                        <input type="hidden" name="credit_account_id" value="<?= $edit ? $contra->credit_account_id : $account->{$account::DB_TABLE_PK} ?>">
                        <input type="hidden" name="contra_id" value="<?= $edit ? $contra->{$contra::DB_TABLE_PK} : '' ?>">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="reference" class="control-label">Reference</label>
                        <input type="text" class="form-control" name="reference" value="<?= $edit ? $contra->reference : '' ?>">
                    </div>

                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="40%">Debit Account</th><th width="20%">Amount</th><th>Description</th><th></th>
                            </tr>
                            <tr style="display: none" class="row_template">
                                <td><?= form_dropdown('debit_account_id',$contra_debit_account_options,'',' class="form-control"') ?></td>
                                <td><input name="amount" class="form-control number_format"></td>
                                <td><textarea name="description" rows="1" class="form-control"></textarea></td>
                                <td>
                                    <span class="pull-right">
                                        <button type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                    </span>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if(!$edit) {
                                ?>
                                <tr>
                                    <td><?= form_dropdown('debit_account_id', $contra_debit_account_options, '', ' class="form-control searchable"') ?></td>
                                    <td><input name="amount" class="form-control number_format"></td>
                                    <td><textarea name="description" rows="1" class="form-control"></textarea></td>
                                    <td></td>
                                </tr>
                                <?php
                            } else {
                                $items = $contra->contra_items();
                                
                                foreach ($items as $item) {
                                    $debit_account = $item->debit_account()
                                    ?>
                                    <tr>
                                        <td>
                                            <?= form_dropdown('debit_account_id',[$debit_account->{$debit_account::DB_TABLE_PK} => $debit_account->account_name],$item->debit_account_id,' class="form-control"') ?>
                                        </td>
                                        <td><input type="text" class="number_format form-control" name="amount" value="<?= number_format($item->amount) ?>"></td>
                                        <td><textarea class="form-control" name="description" rows="1"><?= $item->description ?></textarea></td>
                                        <td>
                                            <span class="pull-right">
                                                <button type="button" class="btn btn-xs btn-danger row_remover"><i class="fa fa-close"></i></button>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>TOTAL</th><th></th><th></th>
                                <th>
                                    <button  type="button" class="btn btn-xs btn-default row_adder"><i class="fa fa-plus"></i> Add Row</button>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="form-group col-xs-12">
                    <label for="remarks" class="control-label">Remarks</label>
                    <textarea name="remarks" class="form-control"><?= $edit ? $contra->remarks : '' ?></textarea>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-default save_contra">Submit</button>
        </div>
        </form>
    </div>
</div>