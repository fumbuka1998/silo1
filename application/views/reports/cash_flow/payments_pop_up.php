<?php
/**
 * Created by PhpStorm.
 * User: Bizy Tech
 * Date: 4/15/2019
 * Time: 11:19 AM
 */

?>

<a style="cursor: pointer" data-toggle="modal" data-target="#project_payments_<?= $project_id ?>">
    <?= $currency_symbol.' '.number_format($project_total_expenses) ?>
</a>
<div id="project_payments_<?= $project_id ?>"  class="modal fade location_sales_form" role="dialog">
    <div style="width: 80%" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <form method="post" target="_blank" action="<?= base_url('reports/cash_flow') ?>">
                    <input type="hidden" name="from" value="<?= $from ?>">
                    <input type="hidden" name="to" value="<?= $to ?>">
                    <input type="hidden" name="project_ids" value="<?= $project_id ?>">
                    <input type="hidden" name="payment_sub_sheet" value="true">
                    <input type="hidden" name="title" value="payments">
                    <input type="hidden" name="project_name" value="<?= $project_name ?>">
                    <button type="submit" style="margin-right: 20px" class="button btn-primary pull-right btn-xs"><i class="fa fa-file-pdf-o"> PDF</i></button>
                </form>
                <h4 class="modal-title"><?= $project_name ?> PAYMENTS</h4>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 table_container"  style="max-height: 320px; overflow-y: scroll">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>Approval Date</th>
                                    <th style="width: 40%">Comments</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $bg = '#efefef';
                                $total_amount = 0;
                                foreach($project_payments as  $payment) {
                                    ?>
                                    <tr style="background: <?= $bg ?>">
                                        <td style="text-align: left"><?= $payment['approved_date'] ?></td>
                                        <td style="text-align: left"><?= wordwrap($payment['comments'],75,'<br/>') ?></td>
                                        <td style="text-align: left"><?= $payment['reference'] ?></td>
                                        <td style="text-align: right"><?= 'TSH '.number_format($payment['amount']) ?></td>
                                    </tr>
                                    <?php
                                    $total_amount += $payment['amount'];
                                    $bg = $bg == '#efefef' ? '#ffffff' : '#efefef';
                                }
                                ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td style="font-weight: bold; text-align: left" colspan="3">TOTAL</td>
                                    <td style="font-weight: bold; text-align: right"><?= number_format($total_amount,2)?></td>
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

