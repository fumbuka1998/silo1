<?php $this->load->view('includes/header'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Purchase Orders
        <!--<small>Preview page</small>-->
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="<?= base_url('procurements') ?>"><i class="fa fa-shopping-cart"></i>Procurements</a></li>
        <li class="active">Purchase Orders</li>
        <li class="active">GRN Preview</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12" id="goods_received_notes_preview">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- Main content -->
                            <div class="invoice p-3 mb-3">
                                <?php
                                $this->load->view('includes/letterhead');

                                if ($order_grn) {
                                    $data['order'] = $order = $order_grn->purchase_order();
                                    $data['currency'] = $currency = $order->currency();
                                    $data['factor'] = $factor = $order_grn->factor * $order_grn->exchange_rate;
                                } else if ($imprest_grn) {
                                    $currency = $grn->imprest_grn()->imprest()->currency();
                                } else if ($imprest_voucher_grn) {
                                    $currency = $grn->imprest_voucher_retirement_grn()->imprest_voucher_retirement()->imprest_voucher()->requisition_approval()->requisition()->currency();
                                }

                                ?>
                                <h2 style="text-align: center"><?= $is_site_grn ? 'DELIVERY' : 'GOODS RECEIVED' ?> NOTE</h2>
                                <br />


                                <table style="font-size: 14px" width="100%">
                                    <tr>
                                        <th style="text-align: left" colspan="4">
                                            <?= $grn->cost_center_name() ?>
                                            <hr style="height: 1px; color: #CCCCCC" />
                                        </th>
                                    </tr>
                                    <tr>
                                        <td style=" width:33.3%">
                                            <strong><?= $is_site_grn ? 'Delivery' : 'GRN' ?> No: </strong><?= $grn->grn_number() ?>
                                        </td>
                                        <td style=" width:<?= $order_grn ? 33.3 : 40 ?>%">
                                            <strong>Location: </strong><br /><?= $grn->location()->location_name ?>
                                        </td>
                                        <td style=" width:<?= $order_grn ? 33.3 : 30 ?>%">
                                            <strong>Sub-Location: </strong><br /><?= $grn->sub_location()->sub_location_name ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style=" width:<?= $order_grn ? 33.3 : 40 ?>%">
                                            <strong>Received From: </strong><br /><?= $grn->source_name() ?>
                                        </td>
                                        <?php if ($order_grn) { ?>
                                            <td style=" width:33.3%">
                                                <strong>Factor: </strong><br /><?= number_format($factor, 2) ?>
                                            </td>
                                        <?php } ?>
                                        <td>
                                            <strong>Reference: </strong> <?= $grn->source_reference() ?>
                                        </td>
                                    </tr>
                                </table>
                                <br />
                                <table cellpadding="3" style="font-size: 13px" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="bordered">S.No.</th>
                                            <th class="bordered">Item Description</th>
                                            <th class="bordered">Part No.</th>
                                            <th class="bordered">Unit</th>
                                            <th class="bordered">Received Quantity</th>
                                            <?php if ($order_grn) { ?>
                                                <th class="bordered">Delivered Quantity</th>
                                                <th class="bordered">Rejected Quantity</th>
                                                <th colspan="2" class="bordered">Unit Price</th class="bordered">
                                                <th colspan="2" class="bordered">Amount</th>
                                                <th class="bordered">Cost P.U (TSHS)</th>
                                                <th class="bordered">Amount(TSH)</th>
                                            <?php } else if ($imprest_grn || $imprest_voucher_grn) { ?>
                                                <th colspan="2" class="bordered">Unit Price</th class="bordered">
                                                <th colspan="2" class="bordered">Amount</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $items = $grn->material_items();
                                        $sn = $total_amount = $foreign_total_amount = 0;
                                        foreach ($items as $item) {
                                            $sn++;
                                            $stock = $item->stock_item();
                                            $material = $stock->material_item();
                                            $total_amount += $amount = $stock->price * $stock->quantity;
                                        ?>
                                            <tr>
                                                <td class="bordered"><?= $sn ?></td>
                                                <td class="bordered"><?= $material->item_name ?></td>
                                                <td class="bordered"><?= $material->part_number ?></td>
                                                <td class="bordered"><?= $material->unit()->symbol ?></td>
                                                <td class="bordered" style="text-align: right"><?= $stock->quantity ?></td>
                                                <?php if ($order_grn) {
                                                    if ($factor > 0) {
                                                        $foreign_rate = $stock->price / $factor;
                                                    } else {
                                                        $foreign_rate = $stock->price;
                                                    }
                                                    $foreign_total_amount += $foreign_amount = $foreign_rate * $stock->quantity;
                                                ?>
                                                    <td class="bordered" style="text-align: right"><?= $item->rejected_quantity + $stock->quantity ?></td>
                                                    <td class="bordered" style="text-align: right"><?= $item->rejected_quantity ?></td>
                                                    <td class="left_bordered"><?= $currency->symbol ?></td>
                                                    <td class="right_bordered" style="text-align: right"><?= number_format($foreign_rate, 2) ?></td>
                                                    <td class="left_bordered"><?= $currency->symbol ?></td>
                                                    <td class="right_bordered" style="text-align: right"><?= number_format($foreign_amount, 2) ?></td>
                                                    <td class="bordered" style="text-align: right"><?= number_format($stock->price, 2) ?></td>
                                                    <td class="bordered" style="text-align: right"><?= number_format($amount, 2) ?></td>
                                                <?php
                                                } else if ($imprest_grn || $imprest_voucher_grn) {
                                                ?>
                                                    <td class="left_bordered"><?= $currency->symbol ?></td>
                                                    <td class="right_bordered" style="text-align: right"><?= number_format($stock->price, 2) ?></td>
                                                    <td class="left_bordered"><?= $currency->symbol ?></td>
                                                    <td class="right_bordered" style="text-align: right"><?= number_format($amount, 2) ?></td>
                                                <?php
                                                }  ?>
                                            </tr>
                                            <?php
                                        }

                                        $asset_items = $grn->asset_items();

                                        foreach ($asset_items as $item) {
                                            $sn++;
                                            if ($transfer_grn) {

                                            ?>
                                                <tr>
                                                    <td class="bordered"><?= $sn ?></td>
                                                    <td class="bordered"><?= $item->asset_code() ?></td>
                                                    <td class="bordered"></td>
                                                    <td class="bordered">No.</td>
                                                    <td class="bordered" style="text-align: right">1</td>
                                                </tr>
                                            <?php
                                            } else if ($imprest_grn || $imprest_voucher_grn) {
                                                $total_amount += $amount = $item->price * $item->quantity;
                                            ?>
                                                <tr>
                                                    <td class="bordered"><?= $sn ?></td>
                                                    <td class="bordered"><?= $item->asset_name ?></td>
                                                    <td class="bordered"></td>
                                                    <td class="bordered">No.</td>
                                                    <td class="bordered" style="text-align: right"><?= $item->quantity ?></td>
                                                    <td class="left_bordered"><?= $currency->symbol ?></td>
                                                    <td class="right_bordered" style="text-align: right"><?= number_format($item->price, 2) ?></td>
                                                    <td class="left_bordered"><?= $currency->symbol ?></td>
                                                    <td class="right_bordered" style="text-align: right"><?= number_format($amount, 2) ?></td>
                                                </tr>
                                            <?php
                                            } else if ($unprocured_grn) { ?>
                                                <tr>
                                                    <td class="bordered"><?= $sn ?></td>
                                                    <td class="bordered"><?= $item->asset_name ?></td>
                                                    <td class="bordered"></td>
                                                    <td class="bordered">No.</td>
                                                    <td class="bordered" style="text-align: right"><?= $item->quantity ?></td>
                                                </tr>

                                            <?php
                                            } else {
                                                $foreign_rate = $item->price / $factor;
                                                $foreign_total_amount += $foreign_amount = $foreign_rate * $item->quantity;
                                                $total_amount += $amount = $item->price * $item->quantity;
                                            ?>
                                                <tr>
                                                    <td class="bordered"><?= $sn ?></td>
                                                    <td class="bordered"><?= $item->asset_name ?></td>
                                                    <td class="bordered"></td>
                                                    <td class="bordered">No.</td>
                                                    <td class="bordered" style="text-align: right"><?= $item->quantity ?></td>
                                                    <td class="bordered" style="text-align: right"><?= $item->rejected_quantity + $item->quantity ?></td>
                                                    <td class="bordered" style="text-align: right"><?= $item->rejected_quantity ?></td>
                                                    <td class="left_bordered"><?= $currency->symbol ?></td>
                                                    <td class="right_bordered" style="text-align: right"><?= number_format($foreign_rate, 2) ?></td>
                                                    <td class="left_bordered"><?= $currency->symbol ?></td>
                                                    <td class="right_bordered" style="text-align: right"><?= number_format($foreign_amount, 2) ?></td>
                                                    <td class="bordered" style="text-align: right"><?= number_format($item->price, 2) ?></td>
                                                    <td class="bordered" style="text-align: right"><?= number_format($amount, 2) ?></td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        <?php
                                        }

                                        $service_items = $grn->service_items();

                                        foreach ($service_items as $item) {
                                            $sn++;

                                            $foreign_rate = $item->rate / $factor;
                                            $foreign_total_amount += $foreign_amount = $foreign_rate * $item->received_quantity;
                                            $total_amount += $amount = $item->rate * $item->received_quantity;
                                        ?>
                                            <tr>
                                                <td class="bordered"><?= $sn ?></td>
                                                <td class="bordered"><?= $item->description ?></td>
                                                <td class="bordered"></td>
                                                <td class="bordered">No.</td>
                                                <td class="bordered" style="text-align: right"><?= $item->received_quantity ?></td>
                                                <td class="bordered" style="text-align: right"><?= $item->rejected_quantity + $item->received_quantity ?></td>
                                                <td class="bordered" style="text-align: right"><?= $item->rejected_quantity ?></td>
                                                <td class="left_bordered"><?= $currency->symbol ?></td>
                                                <td class="right_bordered" style="text-align: right"><?= number_format($foreign_rate, 2) ?></td>
                                                <td class="left_bordered"><?= $currency->symbol ?></td>
                                                <td class="right_bordered" style="text-align: right"><?= number_format($foreign_amount, 2) ?></td>
                                                <td class="bordered" style="text-align: right"><?= number_format($item->rate, 2) ?></td>
                                                <td class="bordered" style="text-align: right"><?= number_format($amount, 2) ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <?php if ($order_grn) { ?>
                                            <tr style="background-color: #BBBBBB;">
                                                <th class="bordered" colspan="9">TOTAL</th>
                                                <th class="left_bordered"><?= $currency->symbol ?></th>
                                                <th class="right_bordered" style="text-align: right"><?= number_format($foreign_total_amount, 2) ?></th>
                                                <th class="bordered"></th>
                                                <th class="bordered" style="text-align: right"><?= number_format($total_amount, 2) ?></th>
                                            </tr>
                                        <?php } else if ($imprest_voucher_grn) { ?>
                                            <tr style="background-color: #BBBBBB;">
                                                <th class="bordered" colspan="7">TOTAL</th>
                                                <th class="left_bordered"><?= $currency->symbol ?></th>
                                                <th class="bordered" style="text-align: right"><?= number_format($total_amount, 2) ?></th>
                                            </tr>
                                        <?php } ?>
                                    </tfoot>
                                </table>
                                <br />
                                <?php
                                if ($order_grn) {
                                    $freight_insurance_and_other_charges = $order_grn->freight + $order_grn->insurance + $order_grn->other_charges;
                                    $grand_total_value = 0;
                                ?>
                                    <table cellpadding="3" style="font-size: 13px" class="duties_table" cellspacing="0" width="70%">
                                        <?php if ($order_grn->exchange_rate != 1) { ?>
                                            <tr>
                                                <th class="bordered">Exchange Rate</th>
                                                <td class="left_bordered"></td>
                                                <td class="right_bordered" style="text-align: right">
                                                    <?= number_format($order_grn->exchange_rate, 2) ?>
                                                </td>
                                            </tr>
                                        <?php
                                        }

                                        if ($foreign_total_amount != 0) {
                                        ?>
                                            <tr>
                                                <th class="bordered">Freight On Board (FOB)</th>
                                                <td class="left_bordered"><?= $currency->symbol ?></td>
                                                <td class="right_bordered" style="text-align: right">
                                                    <?= number_format($foreign_total_amount, 2)
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php
                                        }


                                        $total_foreign_cif = $freight_insurance_and_other_charges + $foreign_total_amount;
                                        $grand_total_value += $total_cif = $total_foreign_cif * $order_grn->exchange_rate;

                                        if ($freight_insurance_and_other_charges != 0) {
                                        ?>
                                            <tr>
                                                <th class="bordered">Freight, Insurance and Other Charges</th>
                                                <td class="left_bordered"><?= $currency->symbol ?></td>
                                                <td class="right_bordered" style="text-align: right">
                                                    <?= number_format($freight_insurance_and_other_charges, 2) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="bordered">TOTAL CIF</th>
                                                <td class="left_bordered"><?= $currency->symbol ?></td>
                                                <td class="right_bordered" style="text-align: right">
                                                    <?= number_format($total_foreign_cif, 2) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="bordered">TOTAL CIF (TSHS)</th>
                                                <td class="left_bordered"></td>
                                                <td class="right_bordered" style="text-align: right">
                                                    <?= number_format($total_cif, 2) ?>
                                                </td>
                                            </tr>
                                        <?php
                                        }

                                        $grand_total_value += $import_duty = $order_grn->import_duty;

                                        ?>
                                        <tr>
                                            <th class="bordered">IMPORT DUTY</th>
                                            <td class="left_bordered"></td>
                                            <td class="right_bordered" style="text-align: right">
                                                <?= number_format($import_duty, 2) ?>
                                            </td>
                                        </tr>
                                        <?php

                                        $grand_total_value += $cpf = $order_grn->cpf;
                                        if ($cpf != 0) {
                                        ?>
                                            <tr>
                                                <th class="bordered">CPF</th>
                                                <td class="left_bordered"></td>
                                                <td class="right_bordered" style="text-align: right">
                                                    <?php
                                                    echo number_format($cpf, 2)
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php
                                        }

                                        $grand_total_value += $rdl = $order_grn->rdl;
                                        if ($rdl != 0) {
                                        ?>
                                            <tr>
                                                <th class="bordered">RDL</th>
                                                <td class="left_bordered"></td>
                                                <td class="right_bordered" style="text-align: right">
                                                    <?php
                                                    echo number_format($rdl, 2)
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <th class="bordered">VAT</th>
                                            <td class="left_bordered"></td>
                                            <td class="right_bordered" style="text-align: right">
                                                <?php
                                                //$grand_total_value += $vat = $order->vat_inclusive == 'VAT COMPONENT' ? $total_cif*$order->vat_percentage/100 : $order_grn->vat;
                                                $grand_total_value += $vat = $order_grn->vat;
                                                echo number_format($vat, 2)
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                        if ($grand_total_value != $foreign_total_amount) {
                                        ?>
                                            <tr>
                                                <th class="bordered">TOTAL COST BEFORE RELEASE GOODS</th>
                                                <td class="left_bordered"></td>
                                                <td class="right_bordered" style="text-align: right">
                                                    <?= number_format($grand_total_value, 2) ?>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        $clearance_charges = $order_grn->clearance_charges + $order_grn->clearance_vat;
                                        if ($clearance_charges != 0) {
                                        ?>
                                            <tr>
                                                <th class="bordered">Clearance Charges</th>
                                                <td class="left_bordered"></td>
                                                <td class="right_bordered" style="text-align: right">
                                                    <?php
                                                    echo number_format($clearance_charges, 2)
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php }

                                        if ($order_grn->wharfage != 0) {
                                        ?>
                                            <tr>
                                                <th class="bordered">Wharfage</th>
                                                <td class="left_bordered"></td>
                                                <td class="right_bordered" style="text-align: right">
                                                    <?php
                                                    echo number_format($order_grn->wharfage, 2)
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php }

                                        if ($order_grn->service_fee != 0) {
                                        ?>
                                            <tr>
                                                <th class="bordered">Port Charges</th>
                                                <td class="left_bordered"></td>
                                                <td class="right_bordered" style="text-align: right">
                                                    <?php
                                                    echo number_format($order_grn->service_fee, 2)
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <th class="bordered">VALUE OF GOODS AT SITE (VAT INCL)</th>
                                            <td class="left_bordered"></td>
                                            <td class="right_bordered" style="text-align: right">
                                                <?php
                                                $value_of_goods_vat_incl = $grand_total_value + $clearance_charges + $order_grn->service_fee + $order_grn->wharfage;
                                                echo number_format($value_of_goods_vat_incl, 2)
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="bordered">Less VAT</td>
                                            <td class="left_bordered"></td>
                                            <td class="right_bordered" style="text-align: right">
                                                <?= number_format($vat + $order_grn->clearance_vat, 2) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="bordered">VALUE OF GOODS AT SITE (VAT EXCL)</th>
                                            <td class="left_bordered"></td>
                                            <td class="right_bordered" style="text-align: right">
                                                <?= number_format(($value_of_goods_vat_incl - $vat - $order_grn->clearance_vat), 2) ?>
                                            </td>
                                        </tr>
                                    </table>
                                <?php } ?>
                                <br /><br />
                                <strong>Comments</strong><br /><?= $grn->comments != '' ? nl2br($grn->comments) : 'N/A' ?>

                                <br /><br />

                                <table width="100%">
                                    <tr>
                                        <td style="width: 50%">
                                            <strong>Received By: </strong><?= $grn->receiver()->full_name() ?>
                                        </td>
                                        <td style=" width:50%">
                                            <strong>Date Received: </strong><?= custom_standard_date($grn->receive_date) ?>
                                        </td>
                                    </tr>
                                </table>

                                <br /><br />
                                <?php if ($imprest_voucher_grn) { ?>
                                    <table border="1" cellspacing="0" cellpadding="6px" width="100%">
                                        <tr>
                                            <td style="width: 50%; vertical-align: top">
                                                Storekeeper<br />
                                                (Full Name):
                                            </td>
                                            <td style="width: 50%; vertical-align: top">
                                                Signature:
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Comments:<br /><br /><br /><br /><br /><br /><br /></td>
                                        </tr>
                                    </table>
                                <?php } ?>

                                <div class="card-footer">
                                    <button type="button" data-toggle="modal" data-target="#receive_purchase_order" class="btn btn-default grn_preview_edit">Edit</button>
                                    <div id="receive_purchase_order" class="modal fade purchase_order_receive_form" role="dialog">
                                        <?php $this->load->view('procurements/purchase_orders/receive_purchase_order_form', $data) ?>
                                    </div>
                                    <button style="margin-left: 89%" type="button" class="btn btn-success float-right grn_preview_submit">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php $this->load->view('includes/footer');
