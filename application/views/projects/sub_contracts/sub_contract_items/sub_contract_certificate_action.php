<span class="pull-left">
    <button type="button" title="View Details <?= $sub_contract_certificate->certificate_number ?>" data-toggle="modal" id="details<?= $sub_contract_certificate_id ?>" data-target="#view_details<?= $sub_contract_certificate_id ?>" class="btn btn-block btn-primary btn-xs">
        <i class="fa fa-eye"></i> View
    </button>

    <div id="view_details<?= $sub_contract_certificate_id ?>" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"><?= $sub_contract_certificate->certificate_number ?> Tasks</h4>
                    </div>
                    <div class="modal-body">
                        <div class='row'>
                            <div class="col-xs-12">
                                <table class="table table-bordered table-hover table-striped" style="table-layout: fixed;">
                                    <tbody>
                                        <tr>
                                            <td style="width: 5%;"><strong>S/N</strong></td>
                                            <td><strong>Task</strong></td>
                                            <td style="width: 20%; text-align: right"><strong>Certified Amount</strong></td>
                                        </tr>
                                        <?php
                                        $sn = $total_amount = 0;
                                        foreach ($sub_contract_certificate->certificate_tasks() as $index => $cert_task) {
                                            $total_amount += $cert_task->amount ?>
                                            <tr>
                                                <td style="width: 15%;"><?= ++$sn ?></td>
                                                <td><?= $cert_task->task()->task_name ?></td>
                                                <td style="width: 20%; text-align: right"><?= number_format($cert_task->amount, 2) ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2">Total</th>
                                            <th style="text-align: right"><?= number_format($total_amount, 2) ?></th>
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
    <?php if (!$sub_contract_certificate->has_requisition()) { ?>
        <button type="button" sub_contract_certificate_id="<?= $sub_contract_certificate_id ?>" class="btn btn-xs btn-danger delete_sub_contract_certificate">
            <i class="fa fa-trash"></i> Delete
        </button>
    <?php } ?>

</span>