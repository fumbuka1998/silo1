<?php
$company_details = get_company_details();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PV/<?= add_leading_zeros($payment_voucher->{$payment_voucher::DB_TABLE_PK}) ?></title>
    <style>
        @page {
            sheet-size: A4;
            margin: 1pt 4pt 3pt 4pt;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        body {
            margin: 0 auto;
            color: #000000;
            background: #FFFFFF;
            font-family: 'Open Sans', sans-serif;;
            font-size: 12px;
        }

        body p {
            padding: 5px;
            text-indent: 8px;
            color: #000000;
            font-family: 'Open Sans', sans-serif;;
            font-size: 11px;
        }

        .address strong {
            font-size: 12px;
            color: #bc1f27;
        }

        header {
            padding: 4px 0;
        }

        h1 {
            border-top: 1px solid #5D6975;
            border-bottom: 1px solid #5D6975;
            color: #000000;
            font-size: 1.5em;
            line-height: 1.4em;
            font-weight: normal;
            text-align: center;
            margin: 0 0 2px 0;
        }

        h4 {
            color: #000000;
            font-size: 12px;
            line-height: 1.0em;
            font-weight: normal;
            text-align: center;
            margin-bottom: 10px;
        }

        .table-main {
            width: 100%;
            border: 3px solid #000000;
            border-collapse: collapse;
            margin: 8%;
        }

        .table-header-table {
            width: 100%;
            font-size: 20px;
            border-top: 3px solid #000000;
            border-bottom: 3px solid #000000;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        .table-from-table, .table-to-table {
            width: 100%;
            font-size: 14px;
            border: 2px solid #6C6C9D;
            overflow: hidden;
            border-radius: 15px;
            -moz-border-radius: 15px;
            -webkit-border-radius: 15px;
            border-collapse: separate !important;
            margin-top: 3px;
        }

        .table-below-table {
            width: 100%;
            font-size: 14px;
            border: 2px solid white;
            overflow: hidden;
            border-radius: 15px;
            -moz-border-radius: 15px;
            -webkit-border-radius: 15px;
            border-collapse: separate !important;
            margin-top: 3px;
        }

        .table-main-item-table {
            width: 100%;
            border: 2px solid #000000;
            border-collapse: collapse;
        }

        .table-from-table td:first-child, .table-from-table th:first-child {
            border-left: none;
        }

        .table-from-table th:first-child {
            -moz-border-radius: 15px 0 0 0;
            -webkit-border-radius: 15px 0 0 0;
            border-radius: 15px 0 0 0;
        }

        .table-from-table th:last-child {
            -moz-border-radius: 0 15px 0 0;
            -webkit-border-radius: 0 15px 0 0;
            border-radius: 0 15px 0 0;
        }

        .table-from-table th:only-child {
            -moz-border-radius: 15px 15px 0 0;
            -webkit-border-radius: 15px 15px 0 0;
            border-radius: 15px 15px 0 0;
        }

        .table-from-table tr:last-child td:first-child {
            -moz-border-radius: 0 0 0 15px;
            -webkit-border-radius: 0 0 0 15px;
            border-radius: 0 0 0 15px;
        }

        .table-from-table tr:last-child td:last-child {
            -moz-border-radius: 0 0 15px 0;
            -webkit-border-radius: 0 0 15px 0;
            border-radius: 0 0 15px 0;
        }

        .table-main td, .table-main th {
            border: none;
            padding-left: 0;
            padding-right: 0;
        }

        .table-header-table td, .table-header-table th {
            font-size: 14px;
            padding: 0px;
        }

        .table-main-item-table td, .table-main-item-table th {
            border: 1px solid #000000;
            font-size: 14px;
            padding: 3px;
        }

        .table-from-table td, .table-from-table th {
            font-size: 14px;
            padding: 3px;
        }

        .table-below-table td, .table-below-table th {
            font-size: 14px;
            padding: 3px;
        }

        .table-to-table td, .table-to-table th {
            font-size: 14px;
            padding: 3px;
        }

        .table-main-item-table tfoot tr {
            border: none;
            background: #D9D9D9;
        }

        table tfoot tr:first-child td {
            border-top: 1px solid #000000;
        }
    </style>
</head>
<body>
<br/>
<?php
$payment_date = DateTime::createFromFormat('Y-m-d', $payment_voucher->payment_date);
$formated_payment_date = $payment_date->format('l, F d, Y');
?>
<table cellspacing="0.5" class="table-main" style="table-layout: fixed">
    <tr>
        <td style="width: 40%">
            <img style="width: 100px; padding: 2% 8%" src="<?= base_url('images/company_logo.png') ?>">
        </td>
        <td style="width: 60%">
            <h2 style="text-align: left; color: #8E3518">PAYMENT VOUCHER</h2>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="width: 100%">
            <table cellspacing="0.5" class="table-header-table">
                <tr style="background-color: #AEAAAA">
                    <td colspan="4" style="padding: 3px"></td>
                </tr>
                <tr style="background-color: #8E3518;">
                    <td style="width: 8%">&nbsp;</td>
                    <td style="color: white">Request Number:</td>
                    <td style="width: 32%">&nbsp;</td>
                    <td style="color: white">Request Date:</td>
                </tr>
                <tr style="background-color: #8E3518;">
                    <td style="width: 5%; line-height: 9px">&nbsp;</td>
                    <td style="line-height: 9px">
                        <table cellspacing="0.5" class="table-to-table">
                            <tr>
                                <td style="background-color: white; color: #464674; line-height: 10px; text-align: center; vertical-align: center"><?= $payment_voucher->payment_voucher_number() ?></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 30%; line-height: 9px">&nbsp;</td>
                    <td style="line-height: 9px">
                        <table cellspacing="0.5" class="table-to-table">
                            <tr>
                                <td style="background-color: white; color: #464674; line-height: 10px; text-align: center; vertical-align: center"><?= $formated_payment_date ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr style="background-color: #AEAAAA">
                    <td colspan="4" style="padding: 3px"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="width: 50%">
            <table cellspacing="0.5" class="table-from-table" style="height: 200px">
                <tr>
                    <td style="color: #8E3518; height: 10px"><?= strtoupper($company_details->company_name) ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 50px"><?= ucwords(nl2br($company_details->address)) ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 10px"><?= $company_details->mobile ?></td>
                </tr>
            </table>
        </td>
        <td style="width: 50%">
            <table cellspacing="0.5" class="table-to-table">
                <tr>
                    <td style="color: #31314E; height: 10px; text-align: left">For:</td>
                    <td style="color: #417AC0; height: 10px; text-align: right"><?= strtoupper($payment_voucher->cost_center_name()) ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 10px; text-align: left">Cr Account:</td>
                    <td style="color: #417AC0; height: 10px; text-align: right"><?= $payment_voucher->credit_account()->account()->account_name ?></td>
                </tr>
                <?php if($payment_voucher->cheque_number != null){ ?>
                    <tr>
                        <td style="color: #31314E; height: 10px; text-align: left">Cheque Number:</td>
                        <td style="color: #417AC0; height: 10px; text-align: right"><?= $payment_voucher->cheque_number ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td style="color: #31314E; height: 10px; text-align: left">Payee:</td>
                    <td style="color: #417AC0; height: 10px; text-align: right"><?= $payment_voucher->payee ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 10px; text-align: left">Reference:</td>
                    <td style="color: #417AC0; height: 10px; text-align: right"><?= $payment_voucher->reference ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr style="background-color: #8E3518;">
        <td colspan="2"
            style="padding: 8px; width: 100%; text-align: center; color: white; font-size: 14px"><?= strtoupper($payment_voucher->cost_center_name()) ?></td>
    </tr>
    <tr>
        <td colspan="2" style="width: 100%">
            <table cellspacing="0.5" class="table-main-item-table" style="table-layout: fixed">
                <thead>
                <tr>
                    <th>Description</th><th>Amount</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $pv_items = $payment_voucher->payment_voucher_items();
                $payable_amount = $total_withheld_amount = 0;
                foreach ($pv_items as $pv_item){
                    $payable_amount += $pv_item->amount;
                    $total_withheld_amount += $pv_item->withholding_tax_amount();
                    ?>
                    <tr>
                        <td><?= $pv_item->description ?></td>
                        <td style="text-align: right"><?= $payment_voucher->currency()->symbol .' '.number_format($pv_item->amount,2) ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                <?php
                $total_amount  = ($payable_amount + $total_withheld_amount);
                if($payment_voucher->withholding_tax != null && $payment_voucher->withholding_tax > 0){
                    ?>
                    <tr>
                        <th style="text-align: right">Withholding Tax</th><th style="text-align: right"><?= $payment_voucher->currency()->symbol .' '.number_format($total_withheld_amount,2) ?></th>
                    </tr>
                <?php } ?>
                <tr>
                    <th style="text-align: right">TOTAL</th><th style="text-align: right"><?=  $payment_voucher->currency()->symbol .' '.number_format($total_amount,2) ?></th>
                </tr>
                </tfoot>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2"><strong>Amount In Words: </strong><br/><?= numbers_to_words($total_amount) ?></td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Remarks: </strong><br/><?= $payment_voucher->remarks ?></td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">
            <table style=" font-size: 12px"  class="table-below-table" >
                <tr>
                    <td style="vertical-align: top" width="33.3%">
                        <strong>Authorized By: </strong><br/>
                        <i><?= $payment_voucher->payment_voucher_origin() ? $payment_voucher->payment_voucher_origin()->created_by()->full_name() : ''?></i><br/>
                        <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span><br/>
                        <?= $payment_voucher->payment_voucher_origin() ? $payment_voucher->payment_voucher_origin()->created_by()->position()->position_name : '' ?>
                    </td>
                    <td style="vertical-align: top"  width="33.3%">
                        <strong>Issued By: </strong><br/><br/>
                        <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span><br/>
                        <?= $payment_voucher->employee()->full_name(); ?>
                    </td>
                    <td style="vertical-align: top"  width="33.3%">
                        <strong>Received By: </strong><br/><br/>
                        <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span><br/>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

