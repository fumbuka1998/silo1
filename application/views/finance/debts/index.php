<?php $this->load->view('includes/header'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Debts
            <!--<small>Preview page</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('finance')?>"><i class="fa fa-users"></i>Finance</a></li>
            <li class="active">Invoices</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-xs-12">
                        <div class="form-group col-md-2">
                            <label for="" class="control-label">Filter</label>
                            <?= form_dropdown('filter', [
                                'pending' => 'Pending',
                                'invoiced' => 'Invoiced'
                            ],'',' class="form-control searchable"') ?>
                        </div>

                        <div class="box-tools pull-right">
                            <button data-toggle="modal" data-target="#pay_in_bulk"
                                    class="btn btn-flat btn-xs" style="background: #9cc2cb;">
                                <i class="fa fa-send"></i> Invoice In Bulk
                            </button>
                            <div id="pay_in_bulk" class="modal fade bulk_invoice_form" role="dialog">
                                <?php $this->load->view('finance/debts/bulk_invoice_form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="debts_list">
                            <thead>
                            <tr>
                               <th style="width: 7%">Date</th><th>Nature</th><th>No.</th><th>Description/Project</th><th>Debtor</th><th>Amount</th><th>Status</th><th style="width: 12%"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');