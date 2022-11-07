<div style="width: 40%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Documents</h4>
        </div>
        <div class="modal-body">
            <div>
                <table class="table table-hover table-striped" width="100%" style="table-layout: fixed">
                    <tbody>
                    <tr>
                        <td><strong>DateTime</strong></td>
                        <td colspan="3"><strong>Particulars</strong></td>
                    </tr>
                    <tr>
                        <td colspan="4"><span style="padding-left: 38%"><strong>Request</strong></span></td>
                    </tr>
                    <tr>
                        <td><?= date_format(date_create($requisition->request_date),"Y/m/d") ?></td>
                        <td><?= $requisition_number ?></td>
                        <td style="text-align: right">
                            <a  target="_blank" href="<?= base_url($approved_print_out_link.$requisition->{$requisition::DB_TABLE_PK} )?>">
                                <i class="fa fa-clipboard"></i>
                            </a>
                        </td>
                        <?php $approved_amount = $last_approval ? $last_approval->total_approved_amount(false,'cash') : 0 ?>
                        <td style="text-align: right"><?= $currency->symbol.' '.number_format($approved_amount,2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="alert alert-info col-xs-12" style="text-align: left">
                                NOTE :<span style="text-align: match-parent ">The approved amount is only for the items that requires cash payment the ones<br/>going to vendors are not included.</span>
                            </div>
                        </td>
                    </tr>
                    <?php
                    if(check_privilege('Make Payment')) { ?>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <?php
                        $gross_paid = 0;
                        if ($payment_voucher) {
                            ?>
                            <tr>
                                <td colspan="4"><span style="padding-left: 38%"><strong>Payment Voucher(s)</strong></span></td>
                            </tr>
                            <?php
                            foreach ($paid_items as $pv) {
                                $gross_paid += $pv->amount();
                                ?>
                                <tr>
                                    <td><?= date_format(date_create($pv->created_at),"Y/m/d H:i:s") ?></td>
                                    <td><?= 'PV/' . add_leading_zeros($pv->payment_voucher_id) ?></td>
                                    <td style="text-align: right">
                                        <a target="_blank"
                                           href="<?= base_url($payment_voucher_print_out_link . $pv->payment_voucher_id) ?>">
                                            <i class="fa fa-clipboard"></i>
                                        </a>
                                    </td>
                                    <td style="text-align: right"><?= $pv->currency()->symbol . ' ' . number_format($pv->amount(), 2) ?></td>
                                </tr>
                                <?php
                            }
                        }
                        if ($journal_voucher) {
                            ?>
                            <tr>
                                <td colspan="4"><span style="padding-left: 38%"><strong>Journal Voucher(s)</strong></span></td>
                            </tr>
                            <?php
                            foreach ($requisition->journal_vouchers() as $jv) {
                                $gross_paid += $jv->journal_voucher_amount();
                                ?>
                                <tr>
                                    <td><?= date_format(date_create($jv->created_at),"Y/m/d H:i:s") ?></td>
                                    <td><?= 'JV/' . add_leading_zeros($jv->journal_id) ?></td>
                                    <td style="text-align: right">
                                        <a target="_blank"
                                           href="<?= base_url($journal_voucher_print_out_link . $jv->journal_id) ?>">
                                            <i class="fa fa-clipboard"></i>
                                        </a>
                                    </td>
                                    <td style="text-align: right"><?= $jv->currency()->symbol . ' ' . number_format($jv->journal_voucher_amount(), 2) ?></td>
                                </tr>
                                <?php
                            }
                        }
                        if ($imprest_voucher) {
                            ?>
                            <tr>
                                <td colspan="4"><span style="padding-left: 38%"><strong>Imprest Voucher(s)</strong></span></td>
                            </tr>
                            <?php
                            $gross_paid += $last_approval->imprest_voucher_object()->total_amount_vat_inclusive();
                            ?>
                            <tr>
                                <td><?= date_format(date_create($last_approval->imprest_voucher_object()->created_at),"Y/m/d H:i:s") ?></td>
                                <td><?= 'IMPV/'. add_leading_zeros($imprest_voucher) ?></td>
                                <td style="text-align: right">
                                    <a target="_blank"
                                       href="<?= base_url($imprest_voucher_print_out_link . $imprest_voucher) ?>">
                                        <i class="fa fa-clipboard"></i>
                                    </a>
                                </td>
                                <td style="text-align: right"><?= $last_approval->imprest_voucher_object()->currency()->symbol . ' ' . number_format($last_approval->imprest_voucher_object()->total_amount_vat_inclusive(), 2) ?></td>
                            </tr>
                        <?php } ?>
                        <tr style="background-color: #6EBEF4">
                            <td colspan="3"><strong>TOTAL PAID AMOUNT</strong></td>
                            <td style="text-align: right"><strong><?= number_format($gross_paid, 2) ?></strong></td>
                        </tr>
                        <?php
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
