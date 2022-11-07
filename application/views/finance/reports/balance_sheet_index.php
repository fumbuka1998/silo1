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
    <div class="row">
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
                                            <th>Description</th><th>Balance</th><th></th><th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $grand_total = 0;
                                        foreach($account_groups as $account_group){
                                            ?>
                                            <tr style="background-color: #1e282c; color: white">
                                                <td colspan="4" style="text-align: left"><strong><?= $account_group ?></strong></td>
                                            </tr>
                                            <?php
                                            $total = 0;
                                            if(array_key_exists($account_group, $table_items['balance'])) {
                                                foreach ($table_items['balance'][$account_group] as $table_item) {
                                                    if (!empty($table_item)) {
                                                        $total += $table_item['balance'];
                                                        ?>
                                                        <tr>
                                                            <td><?= ucfirst($table_item['account_name']) ?></td>
                                                            <td><?= $table_item['statement_link'] ?></td>
                                                            <td><?= '' ?></td>
                                                            <td><?= '' ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td colspan="2" style="text-align: right">TOTAL <?= strtoupper($account_group) ?></td>
                                                <td style="text-align: right"><?= $native_currency->symbol.' '.accountancy_number($total) ?></td>
                                                <td><?= '' ?></td>
                                            </tr>
                                            <?php
                                            $grand_total += $total;
                                            if($account_group == "FIXED ASSETS"){
                                                ?>
                                                Here
                                                <tr>
                                                    <td colspan="3" style="text-align: right"><strong>GRAND TOTAL ASSETS</strong></td>
                                                    <td style="text-align: right"><strong><?= $native_currency->symbol.' '.accountancy_number($grand_total) ?></strong></td>
                                                </tr>
                                                <?php
                                                $grand_total = 0;
                                            }
                                            if($account_group == "NON CURRENT LIABILITIES"){
                                                ?>
                                                <tr>
                                                    <td colspan="3" style="text-align: right"><strong>GRAND TOTAL LIABILITIES</strong></td>
                                                    <td style="text-align: right"><strong><?= $native_currency->symbol.' '.accountancy_number($grand_total) ?></strong></td>
                                                </tr>
                                                <?php
                                                $grand_total = 0;
                                            }
                                        } ?>

                                        </tbody>
                                        <tfoot>
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

