<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 5/24/2018
 * Time: 4:52 PM
 */
?>

<div class="modal-dialog">

    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Stock Sales Invoice Form</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
                            <label for="disposal_date control_label">Sales Invoiced Date</label>
                            <input type="text" class="form-control datepicker" required name="sales_invoiced_date" value="" >
                            <input type="hidden" name="sales_id" value="<?= $sale->{$sale::DB_TABLE_PK} ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="requirement_name" class="control-label">VAT Percentage</label>
                            <input type="text" class="form-control" name="vat_percentage" value="18">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="description" class="control-label">Bank Details</label>
                            <textarea name="remarks" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm save_stock_sales_invoice_form">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

