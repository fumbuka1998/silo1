<?php
$this->load->view('includes/header');
$month_string = explode('-',date('Y-m-d'))[1] - 1 > 0 ? explode('-',date('Y-m-d'))[1] - 1 : 12;
$previous_month = explode('-', date('Y-m-d'))[0].'-'.add_leading_zeros($month_string,2).'-'.explode('-',date('Y-m-d'))[2];
?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= strtoupper($report_name) ?>
            <small>Stement Report(<span id="currency_display"><?= $native_currency->symbol ?></span>)</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="<?= base_url('finance')?>"><i class="fa fa-money"></i>Finance</a></li>
            <li><a href="<?= base_url('finance/reports') ?>"><i class="fa fa-money"></i>Reports</a></li>
            <li class="active"><?= strtoupper($report_name) ?></li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row" id="account_statement_main_container">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-md-12">
                        <form method="post" target="_blank" action="<?= base_url('finance/report_statements') ?>">
                            <div class="box-tools pull-left">
                                <div class="form-group col-md-6">
                                    <label class="col-md-4" for="to">As Of</label>
                                    <div class="col-md-8">
                                        <input class="form-control datepicker" name="to" value="<?= date('Y-m-d') ?>">
                                        <input type="hidden" name="currency_id" value="<?= $native_currency->{$native_currency::DB_TABLE_PK} ?>">
                                        <input type="hidden" name="account_name_and_type" value="<?= $account_name_and_type ?>">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <button type="button" id="generate_account_statement" class="btn btn-default btn-xs"><i class="fa fa-download"></i>Generate</button>
                                    <button name="print_pdf" type="submit" value="true"  class="btn btn-default btn-xs"><i class="fa fa-file-pdf-o"></i> PDF</button>
                                    <button name="export_excel" type="submit" value="true"  class="btn btn-default btn-xs"><i class="fa fa-file-excel-o"></i>Export Excel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="col-xs-12 table-responsive">
                                <div class="col-xs-12 table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>S/N</th><th>Account Name</th><th>Running Balance</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sn = 0;
                                            foreach($table_items[$account_name] as $table_item){
                                                if($table_item['account_name'] != '') {
                                                    $sn++;
                                                    ?>
                                                    <tr>
                                                        <td><?= $sn ?></td>
                                                        <td><?= $table_item['account_name'] ?></td>
                                                        <td><?= $table_item['statement_link'] ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr style="background-color: #8B8986">
                                                <td colspan="2"></td><td style="text-align: right"><strong><?= $native_currency->symbol.' '.accountancy_number($table_items[$account_name]['grand_total'],2) ?></strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer');

