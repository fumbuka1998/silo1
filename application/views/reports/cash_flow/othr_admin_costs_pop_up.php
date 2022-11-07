<?php
/**
 * Created by PhpStorm.
 * User: kasobokihuna
 * Date: 16/05/2019
 * Time: 13:30
 */

$cost_center_id = $cost_center->{$cost_center::DB_TABLE_PK};
?>

<a data-toggle="modal" data-target="#othr_admin_costs_pop_up_<?= $dbt_account->{$dbt_account::DB_TABLE_PK}.'_'.$cost_center_id ?>" style="cursor: pointer">
    <?= 'TSH '. number_format($amount_in_basecurrency) ?>
</a>
<div id="othr_admin_costs_pop_up_<?= $dbt_account->{$dbt_account::DB_TABLE_PK}.'_'.$cost_center_id ?>" class="modal fade" role="dialog">
    <div style="width: 50%" class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=  strtoupper($cost_center->cost_center_name.' - '.explode('-',$dbt_account->account_name)[0]) ?><?php if(preg_match('/EXPENSES/',strtoupper($dbt_account->account_name))){ '';} else { ?> EXPENSES<?php } ?></h4>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 table_container"  style="max-height: 420px; overflow-y: scroll">
                            <table class="table table-bordered table-hover">
                                <thead>
                                   <tr>
                                       <th>Payment Date</th>
                                       <th>Reference</th>
                                       <th>Amount</th>
                                       <th>Amount(Base Currency)</th>
                                   </tr>
                                <tbody>
                                  <?php
                                  $total_amount = 0;
                                    $payments = $dbt_account->cost_center_expenses($cost_center_id,$from, $to);
                                    if($payments) {
                                        foreach ($payments as $payment) {
                                            $total_amount += $payment->amount_in_basecurrency;
                                            ?>
                                            <tr>
                                                <td style="text-align: left"><?= custom_standard_date($payment->payment_date) ?></td>
                                                <td style="text-align: left"><?= anchor(base_url('finance/preview_payment_voucher/'.$payment->payment_voucher_id),'PV/'.add_leading_zeros($payment->payment_voucher_id),'target="_blank"') ?></td>
                                                <td style="text-align: right"><?= $payment->symbol.' '.number_format($payment->amount,2) ?></td>
                                                <td style="text-align: right"><?= 'TSH '.number_format($payment->amount_in_basecurrency,2) ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                  ?>
                                </tbody>
                                <tfoot>
                                  <tr>
                                      <th colspan="3">TOTAL</th>
                                      <th style="text-align: right"><?= 'TSH  '. number_format($total_amount,2) ?></th>
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
