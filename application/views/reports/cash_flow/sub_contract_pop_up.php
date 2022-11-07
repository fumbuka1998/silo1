<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 20/04/2019
 * Time: 07:47
 */

?>

<a data-toggle="modal" data-target="#sub_contract_pop_up_<?= $project_id ?>" style="cursor: pointer">
    <?= $currency_symbol.' '. number_format($sub_contracts_commitments) ?>
</a>
<div id="sub_contract_pop_up_<?= $project_id ?>" class="modal fade" role="dialog">
    <div style="width: 50%" class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <form method="post" target="_blank" action="<?= base_url('reports/cash_flow') ?>">
                    <input type="hidden" name="from" value="<?= $from ?>">
                    <input type="hidden" name="to" value="<?= $to ?>">
                    <input type="hidden" name="project_ids" value="<?= $project_id ?>">
                    <input type="hidden" name="sub_contract_sub_sheet" value="true">
                    <input type="hidden" name="title" value="certificate">
                    <input type="hidden" name="project_name" value="<?= $project_name ?>">
                    <button type="submit" style="margin-right: 20px" class="button btn-primary pull-right btn-xs"><i class="fa fa-file-pdf-o"> PDF</i></button>
                </form>
                <h4 class="modal-title"><?= $project_name ?> SUB-CONTRACTORS CERTIFICATES</h4>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 table_container"  style="max-height: 420px; overflow-y: scroll">
                            <table class="table table-bordered table-hover">
                                <thead>
                                   <tr>
                                       <th>Date</th>
                                       <th>Certificate No</th>
                                       <th>Subcontract Descr:</th>
                                       <th>Certified Amount</th>
                                       <th>Paid Amount</th>
                                       <th>Balance</th>
                                   </tr>
                                <tbody>
                                  <?php
                                  $total_certified_amount = 0;
                                  $total_certificate_paid_amount = 0;
                                    foreach ($sub_contractor_wit_certificate as $certificate){
                                        $total_certified_amount = $total_certified_amount + $certificate['certified_amount'];
                                        $total_certificate_paid_amount = $total_certificate_paid_amount + $certificate['amount_paid'];
                                        ?>
                                        <tr>
                                          <td style="text-align: left"><?= $certificate['certificate_date'] ?></td>
                                          <td style="text-align: left"><?= $certificate['certificate_number'] ?></td>
                                          <td style="text-align: left"><?= $certificate['subcontract_description'] ?></td>
                                          <td style="text-align: right"><?= $currency_symbol.' '.number_format($certificate['certified_amount'],2) ?></td>
                                          <td style="text-align: right"><?= $currency_symbol.' '.number_format($certificate['amount_paid'],2) ?></td>
                                          <td style="text-align: right"><?= $currency_symbol.' '.number_format($certificate['current_certificate_balance'],2) ?></td>
                                        </tr>

                                        <?php
                                    }
                                  ?>
                                </tbody>
                                <tfoot>
                                  <tr>
                                      <th colspan="3">TOTAL</th>
                                      <th style="text-align: right"><?= $currency_symbol.' '. number_format($total_certified_amount) ?></th>
                                      <th style="text-align: right"><?= $currency_symbol.' '. number_format($total_certificate_paid_amount) ?></th>
                                      <th style="text-align: right"><?= $currency_symbol.' '. number_format($sub_contracts_commitments) ?></th>
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
