<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 20/04/2019
 * Time: 07:47
 */
?>

<a data-toggle="modal" data-target="#approved_requisitions_pop_up_<?= $cost_center_id ?>" style="cursor: pointer">
    <?= 'TSH '. number_format($total_approved_amount) ?>
</a>
<div id="approved_requisitions_pop_up_<?= $cost_center_id ?>" class="modal fade" role="dialog">
    <div style="width: 50%" class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <form method="post" target="_blank" action="<?= base_url('reports/cash_flow') ?>">
                    <input type="hidden" name="from" value="<?= $from ?>">
                    <input type="hidden" name="to" value="<?= $to ?>">
                    <input type="hidden" name="cost_center_id" value="<?= $cost_center_id ?>">
                    <input type="hidden" name="all_approved_payments_not_paid" value="true">
                    <button type="submit" style="margin-right: 20px" class="button btn-primary pull-right btn-xs"><i class="fa fa-file-pdf-o"> PDF</i></button>
                </form>
                <h4 class="modal-title"><?= strtoupper($cost_center_name). ' APPROVED PAYMENTS' ?></h4>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 table_container"  style="max-height: 420px; overflow-y: scroll">
                       <?php $this->load->view('reports/cash_flow/approved_payments_table'); ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
