<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 6/13/2018
 * Time: 3:30 PM
 */

$project_id = $project->{$project::DB_TABLE_PK};
?>
<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table project_id="<?= $project_id ?>" class="table table-bordered table-hover miscellaneous_costs_items">
                    <thead>
                    <tr>
                        <th>Date</th><th>Reference</th><th>Description</th><th>Cost Center</th><th>Amount</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th colspan="4">TOTAL IN BASE CURRENCY</th><th class="total_miscellaneous_amount_display" style="text-align: right"></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
