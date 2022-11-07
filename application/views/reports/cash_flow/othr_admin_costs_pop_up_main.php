<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 23/05/2019
 * Time: 09:14
 */

$cost_center_id = $cost_center->{$cost_center::DB_TABLE_PK};
?>


<a data-toggle="modal" data-target="#cost_center_main_pop_up_<?= $cost_center_id ?>" style="cursor: pointer">
    <?= 'TSH '. number_format($total_per_cc,2) ?>
</a>
<div id="cost_center_main_pop_up_<?= $cost_center_id ?>" class="modal fade" role="dialog">
    <div style="width: 80%" class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <form method="post" target="_blank" action="<?= base_url('reports/cash_flow') ?>">
                <input type="hidden" name="cost_center_name" value="<?= $cost_center->cost_center_name ?>">
                <input type="hidden" name="cost_center_id" value="<?= $cost_center_id ?>">
                <input type="hidden" name="from" value="<?= $from ?>">
                <input type="hidden" name="to" value="<?= $to ?>">
                <input type="hidden" name="othr_admin_costs_sheet" value="true">
                <button  name="print" type="submit" value="true" type="submit" style="margin-right: 20px" class="button btn-primary pull-right btn-xs"><i class="fa fa-file-pdf-o"> PDF</i></button>
            </form>
            <h4 class="modal-title"><?= strtoupper($cost_center->cost_center_name) ?> EXPENSES</h4>
        </div>
        <form>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 table_container"  style="max-height: 420px; overflow-y: scroll">
                        <table class="table table-bordered table-hover table-striped" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>Descriptions</th><th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_per_cc = 0;
                            if(array_key_exists($cost_center->cost_center_name,$cost_center_payments)) {
                                if(!empty($cost_center_payments[$cost_center->cost_center_name])) {
                                    foreach ($cost_center_payments[$cost_center->cost_center_name] as $othr_admin_cost) {
                                        $total_per_cc += $othr_admin_cost['amount_in_basecurrency'];
                                        ?>
                                        <tr>
                                            <td style="text-align: left"><?= explode('-',$othr_admin_cost['cost_type'])[0] ?></td>
                                            <td style="text-align: right"><?= $print ? number_format($othr_admin_cost['amount_in_basecurrency'], 2) : $othr_admin_cost['othr_admin_costs_pop_up'] ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                            ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="text-align: right; font-weight: bold;">SUB TOTAL</td>
                                    <td style="text-align: right; font-weight: bold;"><?= number_format($total_per_cc, 2) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>


<?php

