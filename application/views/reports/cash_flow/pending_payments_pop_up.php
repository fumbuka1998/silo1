<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 4/24/2019
 * Time: 3:34 PM
 */
?>

<a data-toggle="modal" data-target="#other_commitments_pop_up_<?= $project_id ?>" style="cursor: pointer">
    <?= $currency_symbol.' '. number_format($other_commitments_amount) ?>
</a>
<div id="other_commitments_pop_up_<?= $project_id ?>" class="modal fade" role="dialog">
    <div style="width: 50%" class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <form method="post" target="_blank" action="<?= base_url('reports/cash_flow') ?>">
                    <input type="hidden" name="from" value="<?= $from ?>">
                    <input type="hidden" name="to" value="<?= $to ?>">
                    <input type="hidden" name="project_ids" value="<?= $project_id ?>">
                    <input type="hidden" name="other_paending_payment_sub_sheet" value="true">
                    <input type="hidden" name="title" value="pending payments">
                    <input type="hidden" name="project_name" value="<?= $project_name ?>">
                    <button type="submit" style="margin-right: 20px" class="button btn-primary pull-right btn-xs"><i class="fa fa-file-pdf-o"> PDF</i></button>
                </form>
                <h4 class="modal-title"><?= $project_name ?> APPROVED PAYMENTS</h4>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 table_container"  style="max-height: 420px; overflow-y: scroll">
                            <table class="table table-bordered table-hover">
                                <thead>
                                   <tr>
                                       <th>Approval Date</th>
                                       <th>Correspondence Number</th>
                                       <th>Approved Amount</th>
                                       <th>Approved By</th>
                                   </tr>
                                <tbody>
                                  <?php
                                  $total_other_commitments_amount = 0;
                                    foreach ($other_commitments as $other_commitment){
                                        $total_other_commitments_amount += $other_commitment['approved_amount'];
                                        if($other_commitment['approved_amount'] > 0) {
                                            ?>
                                            <tr>
                                                <td style="text-align: left"><?= custom_standard_date($other_commitment['approval_date']) ?></td>
                                                <td style="text-align: left"><?= $other_commitment['pdf_preview_link'] ?></td>
                                                <td style="text-align: right"><?= $currency_symbol . ' ' . number_format($other_commitment['approved_amount'], 2) ?></td>
                                                <td style="text-align: left"><?= $other_commitment['approver_name'] ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                  ?>
                                </tbody>
                                <tfoot>
                                  <tr>
                                      <th colspan="2">TOTAL</th>
                                      <th style="text-align: right"><?= $currency_symbol.' '. number_format($total_other_commitments_amount) ?></th>
                                      <th></th>
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
