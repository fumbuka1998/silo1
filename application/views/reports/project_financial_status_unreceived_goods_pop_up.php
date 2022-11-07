<?php
?>
<a data-toggle="modal" data-target="#unreceived_goods_pop_up_<?= $project->{$project::DB_TABLE_PK} ?>" style="cursor: pointer">
    <?= number_format($unreceived_goods[$project->project_name]['summation_base_currency'],2) ?>
</a>
<div id="unreceived_goods_pop_up_<?= $project->{$project::DB_TABLE_PK} ?>" class="modal fade" role="dialog">
    <div style="width: 80%" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<form method="post" target="_blank" action="<?= base_url('reports/print_financial_status_report_pop_ups') ?>">
					<input type="hidden" name="pop_up_type" value="unreceived_goods">
					<input type="hidden" name="as_of" value="<?= $as_of ?>">
					<input type="hidden" name="project" value="<?= urlencode(serialize($project)) ?>">
					<input type="hidden" name="project_orders" value="<?= urlencode(serialize($project_orders)) ?>">
					<input type="hidden" name="unreceived_goods" value="<?= urlencode(serialize($unreceived_goods)) ?>">
					<input type="hidden" name="native_currency" value="<?= urlencode(serialize($native_currency)) ?>">
					<button type="submit" style="margin-right: 20px" class="button btn-default pull-right btn-xs"><i class="fa fa-file-pdf-o"> PDF</i></button>
				</form>
                <h4 class="modal-title"><?= $project->project_name ?> UNRECEIVED GOODS</h4>
            </div>
            <form>
				<div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 table_container"  style="max-height: 420px; overflow-y: scroll">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Order No.</th><th>Order Value</th><th>Received Value</th><th>Paid Amount</th><th>Unreceived Value</th><th>Balance(Base Currency)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $running_balance = 0;
                                foreach($project_orders as $project_order){
                                    $running_balance += $unreceived_goods[$project->project_name][$project_order]['balance_in_base_currency'];
                                    ?>
                                    <tr>
                                        <td style="text-align: left"><?= anchor(base_url("procurements/preview_goods_received_report/".$project_order),'P.O/'.add_leading_zeros($unreceived_goods[$project->project_name][$project_order]['order_id']), 'target="_blank"') ?></td>
                                        <td style="text-align: right"><?= $unreceived_goods[$project->project_name][$project_order]['order_currency']->symbol.' '.number_format($unreceived_goods[$project->project_name][$project_order]['order_value'],2) ?></td>
                                        <td style="text-align: right"><?= $unreceived_goods[$project->project_name][$project_order]['order_currency']->symbol.' '.number_format($unreceived_goods[$project->project_name][$project_order]['received_value'],2) ?></td>
                                        <td style="text-align: right"><?= $unreceived_goods[$project->project_name][$project_order]['order_currency']->symbol.' '.number_format($unreceived_goods[$project->project_name][$project_order]['paid_amount'],2) ?></td>
                                        <td style="text-align: right"><?= $unreceived_goods[$project->project_name][$project_order]['order_currency']->symbol.' '.number_format($unreceived_goods[$project->project_name][$project_order]['unreceived_value'],2) ?></td>
                                        <td style="text-align: right"><?= $native_currency->symbol.' '.number_format($running_balance,2) ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr style="background-color: #91e8e1;">
                                    <td colspan="5" style="text-align: left"><strong>TOTAL IN BASE CURRENCY</strong></td>
                                    <td style="text-align: right;"><strong><?= $native_currency->symbol.' '.number_format($unreceived_goods[$project->project_name]['summation_base_currency'],2) ?></strong></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
