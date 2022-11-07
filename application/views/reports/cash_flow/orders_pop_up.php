<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 20/04/2019
 * Time: 07:47
 */
?>
<a data-toggle="modal" data-target="#orders_pop_up_<?= $project_id ?>" style="cursor: pointer">
    <?= $currency_symbol.' '. number_format($purchase_order_commitments) ?>
</a>
<div id="orders_pop_up_<?= $project_id ?>" class="modal fade" role="dialog">
    <div style="width: 80%" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <form method="post" target="_blank" action="<?= base_url('reports/cash_flow') ?>">
                    <input type="hidden" name="from" value="<?= $from ?>">
                    <input type="hidden" name="to" value="<?= $to ?>">
                    <input type="hidden" name="project_ids" value="<?= $project_id ?>">
                    <input type="hidden" name="order_sub_sheet" value="true">
                    <input type="hidden" name="title" value="order">
                    <input type="hidden" name="project_name" value="<?= $project_name ?>">
                    <button type="submit" style="margin-right: 20px" class="button btn-primary pull-right btn-xs"><i class="fa fa-file-pdf-o"> PDF</i></button>
                </form>
                <h4 class="modal-title"><?= $project_name ?> ORDERS</h4>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 table_container"  style="max-height: 420px; overflow-y: scroll">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Order Number</th><th rowspan="2">Supplier</th><th colspan="2">Order Value</th><th colspan="2">Other Charges</th><th colspan="2">Paid Amount</th><th colspan="2">Balance</th>
                                    </tr>
                                    <tr>
                                        <th>Order Currency</th><th><?= $currency_symbol ?></th>
                                        <th>Charges Currency</th><th><?= $currency_symbol ?></th>
                                        <th>Order Currency</th><th><?= $currency_symbol ?></th>
                                        <th>Order Currency</th><th><?= $currency_symbol ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $total_order_value = $total_paid_amount = $total_other_charges = $total_balance = 0;
                                    foreach ($orders_with_balance as $order_item){
                                        $total_order_value += $order_item['value_in_current_currency'];
                                        $total_other_charges += $order_item['other_charges_in_current_currency'];
                                        $total_paid_amount += $order_item['paid_amount_in_current_currency'];
                                        $total_balance += $order_item['balance_in_current_currency'];
                                        ?>
                                        <tr>
                                            <td style="text-align: left"><?= anchor(base_url('procurements/preview_purchase_order/' . $order_item['order_id']), $order_item['order_number'],' target="_blank" ') ?></td>
                                            <td style="text-align: left"><?= anchor(base_url('procurements/vendor_profile/' . $order_item['vendor_id']), $order_item['vendor_name'],' target="_blank" ') ?></td>
                                            <td style="text-align: right"><?= $order_item['order_currency_symbol'] . ' ' . number_format($order_item['order_value']) ?></td>
                                            <td style="text-align: right"><?= $currency_symbol . ' ' . number_format($order_item['value_in_current_currency']) ?></td>
                                            <td style="text-align: right"><?= $order_item['other_charges_currency_symbol'] . ' ' . number_format($order_item['order_other_charges']) ?></td>
                                            <td style="text-align: right"><?= $currency_symbol . ' ' . number_format($order_item['other_charges_in_current_currency']) ?></td>
                                            <td style="text-align: right"><?= $order_item['order_currency_symbol'] . ' ' . number_format($order_item['paid_amount']) ?></td>
                                            <td style="text-align: right"><?= $currency_symbol . ' ' . number_format($order_item['paid_amount_in_current_currency']) ?></td>
                                            <td style="text-align: right"><?= $order_item['order_currency_symbol'] . ' ' . number_format($order_item['balance']) ?></td>
                                            <td style="text-align: right"><?= $currency_symbol . ' ' . number_format($order_item['balance_in_current_currency']) ?></td>
                                        </tr>
                                <?php
                                    }
                                ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">TOTAL</th>
                                        <th style="text-align: right" colspan="2"><?= $currency_symbol.' '. number_format($total_order_value) ?></th>
                                        <th style="text-align: right" colspan="2"><?= $currency_symbol.' '. number_format($total_other_charges)?></th>
                                        <th style="text-align: right" colspan="2"><?= $currency_symbol.' '. number_format($total_paid_amount) ?></th>
                                        <th style="text-align: right" colspan="2"><?= $currency_symbol.' '. number_format($total_balance) ?></th>
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
