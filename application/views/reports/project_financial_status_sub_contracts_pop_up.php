<?php
?>
<a data-toggle="modal" data-target="#sub_contract_details_pop_up_<?= $project->{$project::DB_TABLE_PK} ?>" style="cursor: pointer">
    <?= number_format($sub_contract_details[$project->project_name]['total_paid_amount'],2) ?>
</a>
<div id="sub_contract_details_pop_up_<?= $project->{$project::DB_TABLE_PK} ?>" class="modal fade" role="dialog">
    <div style="width: 80%" class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= wordwrap($project->project_name.' SUB CONTRACT CERTIFICATES',100,'<br/>') ?></h4>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 table_container"  style="max-height: 420px; overflow-y: scroll">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Certificate Date</th><th>Contract</th><th>Certificate No.</th><th>Certified Amount</th><th>Approved Amount</th><th>Paid Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if(!empty($certificates)) {
                                    foreach ($certificates as $certificate) {
                                        ?>
                                        <tr>
                                            <td style="text-align: left"><?= set_date($sub_contract_details[$project->project_name][$certificate->{$certificate::DB_TABLE_PK}]['certificate_date']) ?></td>
                                            <td style="text-align: left"><?= wordwrap($sub_contract_details[$project->project_name][$certificate->{$certificate::DB_TABLE_PK}]['contract_name'], 55, '<br/>') ?></td>
                                            <td style="text-align: left"><?= $sub_contract_details[$project->project_name][$certificate->{$certificate::DB_TABLE_PK}]['certificate_number'] ?></td>
                                            <td style="text-align: right"><?= $native_currency->symbol . ' ' . number_format($sub_contract_details[$project->project_name][$certificate->{$certificate::DB_TABLE_PK}]['certified_amount'], 2) ?></td>
                                            <td style="text-align: right"><?= $native_currency->symbol . ' ' . number_format($sub_contract_details[$project->project_name][$certificate->{$certificate::DB_TABLE_PK}]['approved_amount'], 2) ?></td>
                                            <td style="text-align: right"><?= anchor(base_url('finance/preview_payment_voucher/' . $sub_contract_details[$project->project_name][$certificate->{$certificate::DB_TABLE_PK}]['payment_voucher_id']), $native_currency->symbol . ' ' . number_format($sub_contract_details[$project->project_name][$certificate->{$certificate::DB_TABLE_PK}]['paid_amount'], 2), 'target="_blank"') ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                <tr style="background-color: #91e8e1;">
                                    <td colspan="3" style="text-align: left"><strong>TOTAL IN BASE CURRENCY</strong></td>
                                    <td style="text-align: right"><strong><?= $native_currency->symbol.' '.number_format($sub_contract_details[$project->project_name]['total_certified_amount'],2) ?></strong></td>
                                    <td style="text-align: right"><strong><?= $native_currency->symbol.' '.number_format($sub_contract_details[$project->project_name]['total_approved_amount'],2) ?></strong></td>
                                    <td style="text-align: right"><strong><?= $native_currency->symbol.' '.number_format($sub_contract_details[$project->project_name]['total_paid_amount'],2) ?></strong></td>
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