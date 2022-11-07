
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#new_contra" class="btn btn-default btn-xs">
                                New Contra
                            </button>
                            <div id="new_contra" class="modal fade contra_form" role="dialog">
                                <?php $this->load->view('finance/transactions/contras/contra_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table width="100%" account_id="" id="contras_list" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 8%">Contra Date</th>
                                    <th style="width: 8%">Contra No</th>
                                    <th style="width: 40%">Credit Account</th>
                                    <th style="width: 9%">Reference</th>
                                    <th>Amount</th>
                                    <th style="width: 13%"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
