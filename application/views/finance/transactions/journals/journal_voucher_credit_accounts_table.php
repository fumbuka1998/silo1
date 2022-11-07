<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 4/25/2019
 * Time: 3:20 PM
 */

?>
<table  width="100%" class="table " id="credit_table">
        <thead>
        <tr>
            <th></th>
            <th style="width: 25%">Amount</th>
            <th>Narration</th>
        </tr>
        </thead>
        <tbody>
            <tr style="display: none" class="jv_credit_row_template">
                <td>
                    <?= form_dropdown('jv_credit_account_id',$account_options,'','class="form-control"')?>
                </td>
                <td>
                    <input type="text" name="amount" value="" class="form-control">
                </td>
                <td>
                    <input type="text" name="narration" value="" class="form-control">
                </td>
                <td>
                    <button title="Remove Row" type="button" class="btn btn-sm row_remover">x</button>
                </td>
            </tr>
        </tbody>

        <tfoot>
        <tr class="text_styles">
            <th>TOTAL</th>
            <th class="number_format total_amount_display" style="text-align: right"></th>
        </tr>
        </tfoot>
    </table>
