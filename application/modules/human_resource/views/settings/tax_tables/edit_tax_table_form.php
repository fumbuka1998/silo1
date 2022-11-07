

<?php
$edit = isset($tax_table_rate);
$edit_item = isset($tax_rate_items);
$modal_heading = $edit ? $tax_table_rate->start_date : 'New Tax Rates';
?>

<div class="modal-dialog modal-lg">

    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo 'Tax Rates From: &nbsp;'.custom_standard_date($modal_heading )?></h4>
            </div>


            <div class="modal-body">
                <div class='row'>

                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <input type="hidden" name="tax_table_id" value="<?= $edit ? $tax_table_rate->id : '' ?>">

                            <label for="start_date" class="control-label">Start Date</label>
                            <input type="text" class="form-control datepicker" required name="start_date" value="<?= $edit ? $tax_table_rate->start_date : '' ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="start_date" class="control-label">End Date</label>
                            <input type="text" class="form-control datepicker" required name="end_date" value="<?= $edit ? $tax_table_rate->end_date : '' ?>">
                        </div>
                        <hr>
                        <table class="table table striped table-hover table-bordered " >
                            <thead>
                            <tr>
                                <th></th>
                                <th>From</th>
                                <th>To</th>
                                <th>Rate(%)</th>
                                <th>Additional amount</th>
                            </tr>
                            <tr style="display: none" class="row_template">
                                <td>
                                    <button type="button" class="btn btn-danger btn-xs row_remover"><i class="fa fa-close"></i></button type>
                                </td>
                                <td>
                                    <input type="hidden" name="tax_table_id" value="" class="form-control input-sm"  required>

                                    <input type="text" name="minimum" value="" class="form-control input-sm number_format"  required>
                                </td>
                                <td>  <input type="text" name="maximum" value="" class="form-control input-sm number_format"  required>
                                </td>
                                <td>
                                    <input type="text" name="rate" value="" class="form-control input-sm"  required>
                                </td>
                                <td>
                                    <input type="text" name="additional_amount" value="" class="form-control input-sm number_format"  required>
                                </td>
                            </tr>
                            </thead>
                            <tbody>

                            <?php foreach($tax_rate_items as $tax_rate_item){?>

                            <tr>
                                <td>
                                    <button type="button" class="btn btn-danger btn-xs row_remover"><i class="fa fa-close"></i></button type>
                                </td>
                                <td>
                                    <input type="hidden" name="tax_table_id" value="<?= $edit_item ? $tax_rate_item->tax_table_id: '' ?>" class="form-control input-sm">
                                    <input type="hidden" name="tax_item_id" value="<?= $edit_item ? $tax_rate_item->id: '' ?>" class="form-control input-sm">
                                    <input type="text" name="minimum" value="<?= $edit_item ? $tax_rate_item->minimum: '' ?>" class="form-control input-sm number_format"  required>
                                </td>
                                <td>  <input type="text" name="maximum" value="<?=  $edit_item ? $tax_rate_item->maximum:'' ?>" class="form-control input-sm number_format"  required>
                                </td>
                                <td>
                                    <input type="text" name="rate" value="<?=  $edit_item ? $tax_rate_item->rate:'' ?>" class="form-control input-sm"  required>
                                </td>
                                <td>
                                    <input type="text" name="additional_amount" value="<?=  $edit_item ? $tax_rate_item->additional_amount:'' ?>" class="form-control input-sm number_format"  required>
                                </td>
                            </tr>
                            <?php }?><!--end foreach-->
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="5" style="text-align: right;">
                                    <div><button type="button" class="row_adder btn btn-default btn-xs" ><i class="fa fa-plus-circle"></i>&nbsp; Add</button></div>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm  save_tax_rate">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>