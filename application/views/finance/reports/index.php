<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 11/13/2018
 * Time: 9:30 AM
 */

$this->load->view('includes/header');
$report_type_options = [
    '&nbsp;'=>'&nbsp;',
    'ordered_items'=>'Ordered Items',
    'cash_purchased_items'=>'Cash Purchased Items'
];
$month_string = explode('-',date('Y-m-d'))[1] - 1;
$privious_month = explode('-', date('Y-m-d'))[0].'-'.add_leading_zeros($month_string,2).'-'.explode('-',date('Y-m-d'))[2];


?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Finance
        <small>Reports</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url()?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('finance')?>"><i class="fa fa-money"></i>Finance</a></li>
        <li class="active">Financial Reports</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 ">
                            <div class="col-md-12 table_container">
                                <table  width="100%" class="table " id="" >
                                    <thead>
                                    <tr style="background-color: #97a0b3">
                                        <td colspan="3"><strong>MAIN REPORTS</strong></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td style="width: 33.3332%">
                                            <div>
                                                <?php
                                                $account_name = 'balance';
                                                $account_type = null;
                                                ?>
                                                <a href="<?= base_url('finance/report_statements/'.$account_name.'/'.$account_type) ?>" style="cursor: pointer">
                                                    Balance Sheet
                                                </a>
                                            </div>
                                        </td>
                                        <td style="width: 33.3332%">
                                            <div>
                                                <?php
                                                $account_name = 'income';
                                                $account_type = null;
                                                ?>
                                                <a href="<?= base_url('finance/report_statements/'.$account_name.'/'.$account_type) ?>" style="cursor: pointer">
                                                    Statement Of Income
                                                </a>
                                            </div>
                                        </td>
                                        <td style="width: 33.3332%">
                                            &nbsp;
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="col-xs-12 ">
                            <div class="col-md-12 table_container">
                                <table  width="100%" class="table " id="" >
                                    <thead>
                                    <tr style="background-color: #97a0b3">
                                        <td colspan="3"><strong>JOURNALS</strong></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td style="width: 33.3332%">
                                            <div>
                                                <?php $index = true ?>
                                                <a href="<?= base_url('finance/journal/'.$index)?>" style="cursor: pointer">
                                                    General Journal
                                                </a>
                                            </div>
                                        </td>
                                        <td style="width: 33.3332%">
                                            &nbsp;
                                        </td>
                                        <td style="width: 33.3332%">
                                            &nbsp;
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="col-xs-12 ">
                            <div class="col-md-12 table_container">
                                <table  width="100%" class="table " id="" >
                                    <thead>
                                        <tr style="background-color: #97a0b3">
                                            <td colspan="3"><strong>PURCHASES REPORT</strong></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width: 33.3332%">Detailed Purchases Report </td>
                                            <td style="width: 33.3332%">Cumulative Purchases Report</td>
                                            <td style="width: 33.3332%">Daily Purchases Report</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="col-xs-12 ">
                            <div class="col-md-12 table_container">
                                <table  width="100%" class="table " id="" >
                                    <thead>
                                        <tr style="background-color: #97a0b3">
                                            <td colspan="3"><strong>STAKEHOLDERS REPORT</strong></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width: 33.3332%">
                                                <div>
                                                    <?php
                                                    $account_name = 'receivables';
                                                    $account_type = null;
                                                    ?>
                                                    <a href="<?= base_url('finance/report_statements/'.$account_name.'/'.$account_type) ?>" style="cursor: pointer">
                                                        Account Receivables Report
                                                    </a>
                                                </div>
                                            </td>
                                            <td style="width: 33.3332%">
                                                <div>
                                                    <?php
                                                    $account_name = 'receivables';
                                                    $account_type = 'aging';
                                                    ?>
                                                    <a href="<?= base_url('finance/report_statements/'.$account_name.'/'.$account_type) ?>" style="cursor: pointer">
                                                        Account Receivables Aging Report
                                                    </a>
                                                </div>
                                            </td>
                                            <td style="width: 33.3332%">
                                                <div>
                                                    <?php
                                                    $account_name = 'receivables';
                                                    $account_type = 'aging_details';
                                                    ?>
                                                    <a href="<?= base_url('finance/report_statements/'.$account_name.'/'.$account_type) ?>" style="cursor: pointer">
                                                        Account Receivables Aging Details Report
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 33.3332%">
                                                <div>
                                                    <?php
                                                    $account_name = 'payables';
                                                    $account_type = null;
                                                    ?>
                                                    <a href="<?= base_url('finance/report_statements/'.$account_name.'/'.$account_type) ?>" style="cursor: pointer">
                                                        Account Payables Report
                                                    </a>
                                                </div>
                                            </td>
                                            <td style="width: 33.3332%">
                                                <div>
                                                    <?php
                                                    $account_name = 'payables';
                                                    $account_type = 'aging';
                                                    ?>
                                                    <a href="<?= base_url('finance/report_statements/'.$account_name.'/'.$account_type) ?>" style="cursor: pointer">
                                                        Account Payables Aging Report
                                                    </a>
                                                </div>
                                            </td>
                                            <td style="width: 33.3332%">
                                                <div>
                                                    <?php
                                                    $account_name = 'payables';
                                                    $account_type = 'aging_details';
                                                    ?>
                                                    <a href="<?= base_url('finance/report_statements/'.$account_name.'/'.$account_type) ?>" style="cursor: pointer">
                                                        Account Payables Aging Details Report
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="col-xs-12 ">
                            <div class="col-md-12 table_container">
                                <table  width="100%" class="table " id="" >
                                    <thead>
                                    <tr style="background-color: #97a0b3">
                                        <td colspan="3"><strong><strong>PROJECTS REPORT</strong></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td style="width: 33.3332%">Detailed Purchases Report </td>
                                        <td style="width: 33.3332%">Cumulative Purchases Report</td>
                                        <td style="width: 33.3332%">Daily Purchases Report</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="col-xs-12 ">
                            <div class="col-md-12 table_container">
                                <table  width="100%" class="table " id="" >
                                    <thead>
                                    <tr style="background-color: #97a0b3">
                                        <td colspan="3"><strong><strong>OTHER</strong></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td style="width: 33.3332%">
                                            <div>
                                                <a href="<?= base_url('reports/cost_center_payments') ?>" style="cursor: pointer">
                                                    Cost Center Payments
                                                </a>
                                            </div>
                                        </td>
                                        <td style="width: 33.3332%"></td>
                                        <td style="width: 33.3332%"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section><!-- /.content -->
<?php $this->load->view('includes/footer'); ?>
