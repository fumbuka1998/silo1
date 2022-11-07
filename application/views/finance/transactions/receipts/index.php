
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="col-xs-12">
                            <div class="box-tools pull-right">
                            <?php if(check_privilege('Make Payment')){ ?>
                                <div class="btn-group">
                                    <button data-toggle="modal" data-target="#acknowledge_receipt" class="btn btn-default btn-xs">
                                        <i class="fa fa-plus"></i> Receive
                                    </button>
                                    <div id="acknowledge_receipt" class="modal fade receipt_form" role="dialog">
                                        <?php
                                        $this->load->view('finance/transactions/receipts/receipt_form');
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12 table-responsive">
                                <table id="receipts_list" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Receipt. No.</th><th>Receipt. Date</th><th>Received From</th><th>Debit Account</th><th>Reference</th><th>Amount</th><th></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
