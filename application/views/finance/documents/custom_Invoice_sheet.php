<?php
$company_details = get_company_details();
switch ($invoice_type){
    case 'sales':
        $stakeholder = 'Client';
        $client = $invoice->invoice_to();
        $title = 'Tax Invoice';
        $web_title = 'TAX-INV-'.$invoice->{$invoice::DB_TABLE_PK};
        $corresponding_to = 'Sale/Certificate/Service';
        $desc_or_note = 'Notes';
        break;
    case 'purchases':
        $stakeholder = 'Vendor';
        $client = $invoice->stakeholder();
        $title = 'Purchase Invoice';
        $web_title = 'PURCH-INV-'.$invoice->{$invoice::DB_TABLE_PK};
        $corresponding_to = 'Order';
        $desc_or_note = 'Descriptions';
        break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= $web_title ?></title>
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
$invoice_date = DateTime::createFromFormat('Y-m-d', $invoice->invoice_date);
$formated_invoice_date = $invoice_date->format('l, F d, Y');
?>
<table cellspacing="0.5" class="table-main" style="table-layout: fixed">
    <tr>
        <td style="width: 40%">
            <img style="width: 100px; padding: 2% 8%" src="<?= base_url('images/company_logo.png') ?>">
        </td>
        <td style="width: 60%">
            <h2 style="text-align: left; color: #8E3518"><?=  strtoupper($title) ?></h2>
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
                    <td style="color: white">Invoice Number:</td>
                    <td style="width: 32%">&nbsp;</td>
                    <td style="color: white">Invoice Date:</td>
                </tr>
                <tr style="background-color: #8E3518;">
                    <td style="width: 5%; line-height: 9px">&nbsp;</td>
                    <td style="line-height: 9px">
                        <table cellspacing="0.5" class="table-to-table">
                            <tr>
                                <td style="background-color: white; color: #464674; line-height: 10px; text-align: center; vertical-align: center"><?= $invoice->invoice_no ?></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 30%; line-height: 9px">&nbsp;</td>
                    <td style="line-height: 9px">
                        <table cellspacing="0.5" class="table-to-table">
                            <tr>
                                <td style="background-color: white; color: #464674; line-height: 10px; text-align: center; vertical-align: center"><?= $formated_invoice_date ?></td>
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
                <?php if($invoice_type == 'sales'){ ?>
                    <tr>
                        <td style="color: #000080; padding-left: 15px; height: 10px"><strong>FROM</strong></td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td style="color: #8E3518; padding-left: 15px; height: 10px"><strong>TO</strong></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td style="color: #31314E; height: 10px">Account Department</td>
                </tr>
                <tr>
                    <td style="color: #8E3518; height: 10px"><?= strtoupper($company_details->company_name) ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 50px"><?= ucwords(nl2br($company_details->address)) ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 10px"><?= $company_details->telephone ?></td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 10px">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td style="width: 50%">
            <table cellspacing="0.5" class="table-to-table">
                <?php if($invoice_type == 'sales'){ ?>
                    <tr>
                        <td style="color: #8E3518; padding-left: 15px; height: 10px"><strong>TO</strong></td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td style="color: #000080; padding-left: 15px; height: 10px"><strong>FROM</strong></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td style="color: #31314E; height: 10px">M/s</td>
                </tr>
                <tr>
                    <td style="color: #417AC0; height: 10px">
                        <span><?= strtoupper($client->stakeholder_name) ?></span><br/>
                    </td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 50px">
                        <?php
                        $client_address = explode(PHP_EOL, $client->address);
                        foreach ($client_address as $item){
                            ?>
                            <span><?= $item ?></span><br/>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="color: #31314E; height: 50px">
                        <span><?= $client->phone ?></span><br/>
                        <span><?= $client->email ?></span><br/>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr style="background-color: #8E3518;">
        <td colspan="2"
            style="padding: 8px; width: 100%; text-align: center; color: white; font-size: 14px">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" style="width: 100%">
            <table cellspacing="0.5" class="table-main-item-table" style="table-layout: fixed">
                <thead>
                <tr>
                    <th style="width: 10px">S/N</th><th style="width: 350px">Description</th><th>UOM</th><th>Quantity</th><th>Rate</th><th>Amount</th>
                </tr>
                </thead>
                <tbody>

                <?php
                $invoice_amount = $sn = 0;
                if($invoice_type == 'sales') {
                    $invoice_items = $invoice->invoice_items();
                    foreach ($invoice_items as $item) {
                        $sn++;
                        $invoice_amount += $amount = ($item->quantity * $item->rate);
                        ?>

                        <tr>
                            <td><?= $sn ?></td>
                            <td><?= $item->description ?></td>
                            <td><?= $item->measurement_unit()->symbol ?></td>
                            <td style="text-align: right"><?= $item->quantity ?></td>
                            <td style="text-align: right"><?= number_format($item->rate, 2) ?></td>
                            <td style="text-align: right"><?= number_format($amount, 2) ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    $sn++;
                    $invoice_amount = $invoice->amount;
                    ?>
                    <tr>
                        <td><?= $sn ?></td>
                        <td><?= $invoice->description ?></td>
                        <td><?= 'Item' ?></td>
                        <td style="text-align: right"><?= 1 ?></td>
                        <td style="text-align: right"><?= number_format($invoice->amount, 2) ?></td>
                        <td style="text-align: right"><?= number_format($invoice->amount, 2) ?></td>
                    </tr>
                    <?php
                }

                if($invoice->vat_inclusive == 1) {
                    $vat_amount = (0.01 * $invoice->vat_percentage * $invoice_amount);
                } else {
                    $vat_amount = 0;
                }
                $grand_actual = ($vat_amount + $invoice_amount);
                ?>
                </tbody>
                <tfoot>

                <tr>
                    <td colspan="5" style="text-align: right"><strong>TOTAL</strong></td>
                    <td style="text-align: right"><strong><?= number_format($invoice_amount, 2)  ?></strong></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: right"><strong>VAT</strong></td>
                    <td style="text-align: right"><strong><?= number_format($vat_amount, 2)  ?></strong></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: right"><strong>GRAND TOTAL</strong></td>
                    <td style="text-align: right"><strong><?= number_format($grand_actual, 2)  ?></strong></td>
                </tr>
                <tr style="background-color: white;">
                    <td colspan="5" style="color: #393894; text-align: right;">Total Payable in <?= $invoice->currency()->symbol ?></td>
                    <td style="background-color: #548235; color: white; text-align: right"><strong><?= number_format($grand_actual, 2) ?></strong></td>
                </tr>
                <tr>
                    <td colspan="6" style="color: #4166BD"><strong>AMOUNT IN WORDS: </strong>&nbsp;<?= numbers_to_words($grand_actual).' '.$invoice->currency()->currency_name ?>&nbsp;ONLY<br/></td>
                </tr>
                </tfoot>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">
            <table style=" font-size: 12px"  class="table-below-table" >
                <?php if($invoice_type == 'sales'){ ?>
                    <tr>
                        <td style="text-align: left; width: 50% important!; vertical-align: top">
                            <strong>Bank Details:</strong><br/>
                            <?= nl2br($invoice->bank_details) ?>
                        </td>
                        <td style="text-align: left; vertical-align: top">
                            <?php if($invoice->notes != ''){ ?>
                            <strong>Remarks:</strong><br/>
                            <?= nl2br($invoice->notes) ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <br/>

                    <tr style="background-color: #AEAAAA">
                        <td colspan="2">
                            <strong>Payment Terms:</strong><br/>
                            <?= nl2br($invoice->payment_terms()) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr/></td>
                    </tr>
                    <br/>
                    <tr>
                        <td  colspan="2" style="vertical-align: top; text-align: center;">
                            <strong style="color: #1B219C">APPROVED BY: </strong><br/><br/>
                            <span style="text-decoration: underline">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </span><br/>
<!--                            --><?//= $invoice->created_by()->full_name() ?>
                            Adelmars Kiselar
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

