<?php
?>
<a data-toggle="modal" data-target="#overheads_pop_up_<?= $project->{$project::DB_TABLE_PK} ?>" style="cursor: pointer">
    <?= number_format($overheads[$project->project_name]['total_paid_amount'],2) ?>
</a>
<div id="overheads_pop_up_<?= $project->{$project::DB_TABLE_PK} ?>" class="modal fade" role="dialog">
    <div style="width: 80%" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= wordwrap($project->project_name.' OTHER OVERHEADS',100,'<br/>') ?></h4>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 table_container"  style="max-height: 420px; overflow-y: scroll">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Approved Date</th><th>Requisition No.</th><th>Requested Amount</th><th>Approved Amount</th><th>Paid Amount</th><th>Paid Amount(Base Currency)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if(!empty($overheads_arr)) {
                                    foreach ($overheads_arr as $overhead) {
                                        ?>
                                        <tr>
                                            <td style="text-align: left"><?= set_date($overheads[$project->project_name][$overhead->{$overhead::DB_TABLE_PK}]['approved_date']) ?></td>
                                            <td style="text-align: left"><?= anchor(base_url('requisitions/preview_requisition/'.$overheads[$project->project_name][$overhead->{$overhead::DB_TABLE_PK}]['requisition_id']),'R.Q/'.add_leading_zeros($overheads[$project->project_name][$overhead->{$overhead::DB_TABLE_PK}]['requisition_id']),'target="_blank"') ?></td>
                                            <td style="text-align: right"><?= $overheads[$project->project_name][$overhead->{$overhead::DB_TABLE_PK}]['request_currency']->symbol . ' ' . number_format($overheads[$project->project_name][$overhead->{$overhead::DB_TABLE_PK}]['requested_amount'], 2) ?></td>
                                            <td style="text-align: right"><?= $overheads[$project->project_name][$overhead->{$overhead::DB_TABLE_PK}]['request_currency']->symbol . ' ' . number_format($overheads[$project->project_name][$overhead->{$overhead::DB_TABLE_PK}]['approved_amount'], 2) ?></td>
                                            <td style="text-align: right"><?= anchor(base_url('finance/preview_payment_voucher/' . $overheads[$project->project_name][$overhead->{$overhead::DB_TABLE_PK}]['payment_voucher_id']), $overheads[$project->project_name][$overhead->{$overhead::DB_TABLE_PK}]['payment_currency']->symbol . ' ' . number_format($overheads[$project->project_name][$overhead->{$overhead::DB_TABLE_PK}]['paid_amount'], 2), 'target="_blank"') ?></td>
                                            <td style="text-align: right"><?= $native_currency->symbol . ' ' . number_format($overheads[$project->project_name][$overhead->{$overhead::DB_TABLE_PK}]['paid_amount_base_currency'], 2) ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                <tr style="background-color: #91e8e1;">
                                    <td colspan="2" style="text-align: left"><strong>TOTAL IN BASE CURRENCY</strong></td>
                                    <td style="text-align: right"><strong><?= $native_currency->symbol.' '.number_format($overheads[$project->project_name]['total_requested_amount'],2) ?></strong></td>
                                    <td style="text-align: right"><strong><?= $native_currency->symbol.' '.number_format($overheads[$project->project_name]['total_approved_amount'],2) ?></strong></td>
                                    <td style="text-align: right">&nbsp;</td>
                                    <td style="text-align: right"><strong><?= $native_currency->symbol.' '.number_format($overheads[$project->project_name]['total_paid_amount'],2) ?></strong></td>
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
