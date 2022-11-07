<?php $has_grns = $order->status == 'RECEIVED' || $order->status == 'PARTIAL RECEIVED'; ?>
<div style="width: 70%" class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Purchase Order Invoices</h4>
        </div>
        <div order_id="<?= $order->{$order::DB_TABLE_PK} ?>" class="modal-body">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#general_invoices_<?= $order->{$order::DB_TABLE_PK} ?>"
                                          data-toggle="tab">
                            General Invoices
                        </a>
                    </li>
                    <?php if($has_grns){ ?>
                    <li>
                        <a href="#grn_invoices_<?= $order->{$order::DB_TABLE_PK} ?>"
                                          data-toggle="tab">
                            GRN Invoices
                        </a>
                    </li>
                    <?php } ?>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="general_invoices_<?= $order->{$order::DB_TABLE_PK} ?>">
                        <div class='row'>
                            <div class="col-xs-12 table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Invoice Date</th><th>Reference</th><th>Vendor</th><th>Currency</th><th>Amount</th><th>Description</th><th></th>
                                    </tr>
                                    </thead>
                                    <tbody class="order_grn_invoices_container">
                                    <?php $this->load->view('procurements/purchase_orders/purchase_order_general_invoices_tbody',['invoices' => $order->general_invoices()]); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php if($has_grns){ ?>
                    <div class="tab-pane" id="grn_invoices_<?= $order->{$order::DB_TABLE_PK} ?>">
                        <div class='row'>
                            <div class="col-xs-12 table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Invoice Date</th><th>Reference</th><th>GRN</th><th>Amount</th><th>Description</th><th></th>
                                    </tr>
                                    </thead>
                                    <tbody class="order_grn_invoices_container">
                                    <?php $this->load->view('procurements/purchase_orders/purchase_order_grn_invoices_tbody',['invoices' => $order->grn_invoices()]); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
