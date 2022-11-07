<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 8/14/2018
 * Time: 9:03 AM
 */
?>
<div style="width: 80%" class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Documents</h4>
        </div>
        <form>
            <div class="modal-body">
                <div>
                <?php
                if($retirements){
                ?>
                <table class="table table-bordered table-hover imprest_documents_table">
                    <thead>
                    <th>Reference</th>
                    <th>Status</th>
                    <th>Retirement Sheet</th>
                    <th>GRN</th>
                    <th></th>
                    </thead>
                    <tbody>
                    <?php
                        foreach ($retirements as $retirement) {
                            ?>
                            <tr>
                                <td><?= $retirement->imprest_voucher_retirement_number() ?></td>
                                <td><?= $retirement->status() ?></td>
                                <td>
                                    <a class="btn btn-default btn-xs" target="_blank"
                                       href="<?= base_url('finance/preview_imprest_voucher_retirement/' . $retirement->{$retirement::DB_TABLE_PK}) ?>">
                                        <i class="fa fa-file-pdf-o"></i> PDF
                                    </a>
                                </td>
                                <?php
                                $retirement_grn = $retirement->imprest_voucher_retirement_grn();
                                if($retirement_grn) {
                                ?>
                                <td><?= 'GRN/' . add_leading_zeros($retirement_grn->grn_id) ?></td>
                                <td>
                                    <a class="btn btn-default btn-xs" target="_blank"
                                       href="<?= base_url('inventory/preview_grn/' . $retirement_grn->grn_id . '/' . true) ?>">
                                        <i class="fa fa-file-pdf-o"></i> PDF
                                    </a>
                                </td>
                                <?php
                                } else {
                                    ?>
                                    <td></td>
                                    <td></td>
                                    <?php
                                }
                                ?>
                            </tr>
                            <?php
                        }

                    ?>
                    </tbody>
                </table>
                <?php
                }else { ?>
                <div style="text-align: center" class="alert alert-info col-xs-12">
                    No Document for this imprest
                </div>
                <?php } ?>

                </div>
                <div>
                    <section class="content">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <div class="col-xs-12">
                                            <div class="box-tools pull-right">
                                                <?php if(check_permission('Finance')){ ?>
                                                <button type="button" data-toggle="modal" data-target="#new_contra_<?= $imprest_voucher->{$imprest_voucher::DB_TABLE_PK} ?>" class="btn btn-default btn-xs">
                                                    New Contra
                                                </button>
                                                <?php } ?>
                                                <div id="new_contra_<?= $imprest_voucher->{$imprest_voucher::DB_TABLE_PK} ?>" class="modal fade imprest_contra_form" role="dialog">
                                                    <?php $this->load->view('finance/transactions/approved_cash_requisitions/imprest/imprest_contra_form'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-xs-12 table-responsive">
                                                <table imprest_voucher_id="<?= $imprest_voucher->{$imprest_voucher::DB_TABLE_PK} ?>" id="imprest_contras_list" class="table table-bordered table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>Contra Date</th>
                                                        <th>Contra No</th>
                                                        <th>Reference</th>
                                                        <th>Credit Account</th>
                                                        <th>Debit Account</th>
                                                        <th>Amount</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            </div>
            <div class="modal-footer">
            </div>
        </form>
    </div>
</div>
