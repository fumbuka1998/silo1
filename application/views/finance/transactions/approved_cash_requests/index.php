
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="col-xs-12">
                    <div class="form-group col-md-2">
                        <label for="" class="control-label">Status</label>
                        <?= form_dropdown('status', [
                            'all' => 'All',
                            'paid' => 'Paid',
                            'pending' => 'Pending',
                            'not_paid' => 'Not Paid',
                            'imprest' => 'Imprest',
                            'revoked' => 'Revoked'
                        ],'',' class="form-control" ') ?>
                    </div>
                    <div class="box-tools pull-right">
                        <button data-toggle="modal" data-target="#pay_in_bulk"
                                class="btn btn-default btn-xs">
                            <i class="fa fa-plus"></i> Pay In Bulk
                        </button>
                        <div id="pay_in_bulk" class="modal fade bulk_payment_form" role="dialog">
                            <?php $this->load->view('finance/transactions/bulk_payment/bulk_payment_form'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <table account_id="" class="table table-bordered table-hover table-striped approved_cash_requisitions_table">
                            <thead>
                                <tr>
                                    <th>Approval Date</th><th>Nature</th><th>Request No</th><th style="width: 350px">Requested For</th><th>Approved By</th><th style="width: 10%">Amount</th><th>Status</th><th style="width: 14%"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
