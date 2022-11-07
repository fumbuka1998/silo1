<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 4/26/2018
 * Time: 11:34 PM
 */
?>
<div class="modal-dialog modal-lg">
    <form>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Lumpsum Price</h4>
            </div>
            <div class="modal-body">
                <div class='row'>

                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover" id="lumpsum_price">
                            <thead>
                            <tr>
                                <th>Description</th><th>Amount</th><th></th>
                            </tr>
                            <tr class="lumpsum_price_row_template" style="display: none">

                                <td>
                                    <input type="text" class="form-control" required name="description" id = "description" value="">
                                    <input type="hidden" name="tender_component_id" value="<?= $tender_component_id ?>">
                                    <input type="hidden" name="tender_component_lumpsum_prices_id" value="">
                                </td>
                                <td>
                                    <input type="text" class="form-control number_format" required name="amount" id = "amount" value="">
                                </td>


                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            <tbody>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" required name="description" id = "description" value="">
                                </td>
                                <td>
                                    <input type="text" class="form-control number_format" required name="amount" id = "amount" value="">
                                </td>
                                <td>
                                    <button title="Remove Row" type="button" class="btn btn-xs btn-danger remove_row"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="2"></th>
                                <th>
                                    <button type="button" class="btn btn-xs btn-default lumpsum_price_row_adder">Add Row</button>
                                </th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-default save_lumpsum_price">Save</button>
                </div>
            </div>
        </div>
    </form>
</div>


