

<div class="modal-dialog modal-lg">

    <div class="modal-content">
        <form>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">New Tax Rates</h4>
            </div>


            <div class="modal-body">
                <div class='row'>

                    <div class="col-xs-12">
                        <div class="form-group col-md-6">
<!--                            <input type="text" name="tax_table_id" value=" ">-->
                            <label for="start_date" class="control-label">Start Date</label>
                            <input type="text" class="form-control datepicker" required name="start_date" value=" ">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="start_date" class="control-label">End Date</label>
                            <input type="text" class="form-control datepicker" required name="end_date" value=" ">
                        </div>
                        <hr>
                        <table class="table table striped table-hover table-bordered " >
                            <thead>
                            <tr>
                                <th>From</th>
                                <th>To</th>
                                <th>Rate(%)</th>
                                <th>Additional amount</th>
                                <th></th>
                            </tr>
                            <tr style="display: none" class="row_template">
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
                                <td>
                                    <button type="button" class="btn btn-danger btn-xs row_remover"><i class="fa fa-close"></i></button type>
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <input type="text" name="minimum" value=" " class="form-control input-sm number_format"  required>
                                </td>
                                <td>  <input type="text" name="maximum" value=" " class="form-control input-sm number_format"  required>
                                </td>
                                <td>
                                    <input type="text" name="rate" value=" " class="form-control input-sm"  required>
                                </td>
                                <td>
                                    <input type="text" name="additional_amount" value=" " class="form-control input-sm number_format"  required>
                                </td>
                                <td>
                                 </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="5" style="text-align: right;">
                                    <div><button type="button" class="row_adder btn btn-default btn-xs"><i class="fa fa-plus-circle"></i>&nbsp; Add</button></div>
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