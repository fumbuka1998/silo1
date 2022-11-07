<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 7/13/2016
 * Time: 2:42 PM
 */

    $this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">P.O SUMMARY</h2>
<table style="font-size: 11px"  width="100%" cellpadding="4" cellspacing="0">
    <tr>
        <td style="width: 60%">
            <h2>No. <?= $order->order_number() ?></h2><br/>
            <?php
                $requisition = $order->requisition();
                echo $order->cost_center_name().'<br/>';
                echo (trim($order->reference) != '' ? '<b>RE: </b>'.$order->reference.'<br/>' : '');
                echo ($requisition != '' ? '<b>Req. No: </b>'.$requisition->requisition_number().'<br/>' : '');
            ?>
        </td>
        <td nowrap="nowrap" style="vertical-align: top">
           <b>M/s:</b><br/>
            <?= $vendor->stakeholder_name ?><br/>
            <?= nl2br($vendor->address) ?><br/><br/>
            <b>Currency:</b> <?= $order->currency()->currency_name ?>
        </td>
    </tr>
</table>
<br/>

<h4>1: Purchased Items Values</h4>

<table style="font-size: 10px" width="100%" border="1" cellpadding="3" cellspacing="0">
    <thead>
        <tr style="background-color: #b3b3b3">
            <th colspan="2">Description</th>
            <th>Material</th>
            <th>Assets</th>
            <th>Services</th>
            <th>Freight</th>
            <th>Insurance <br/> Inspection &amp;<br/> Other Charges </th>

            <th>Total(VAT-)</th>
        </tr>
    </thead>
    <tbody>

        <tr  style="background-color: #cccccc">
            <?php
                $order_value = $column_totals['material'] = $column_totals['asset'] = $column_totals['service'] = $column_totals ['freight'] = $column_totals ['other_charges'] = $column_totals ['total'] = 0;
                $order_value += $ordered_material_value = $order->ordered_material_value();
                $order_value += $ordered_asset_value = $order->ordered_asset_value();
                $order_value += $ordered_service_value = $order->ordered_service_value();
                $order_value += $order->freight;
                $order_value += $order->inspection_and_other_charges;



            $order_grns = [];
            foreach ($grns as $grn){
                $order_grns[$grn->{$grn::DB_TABLE_PK}] = $grn->purchase_order_grn();
            }

            ?>
            <th colspan="2">Order Value</th>
            <th style="text-align: right"><?= number_format($ordered_material_value,2) ?></th>
            <th style="text-align: right"><?= number_format($ordered_asset_value,2) ?></th>
            <th style="text-align: right"><?= number_format($ordered_service_value,2) ?></th>
            <th style="text-align: right"><?= number_format($order->freight,2) ?></th>
            <th style="text-align: right"><?= number_format($order->inspection_and_other_charges,2) ?></th>
            <th style="text-align: right"><?= number_format($order_value, 2) ?></th>
        </tr>
        <tr>
            <th rowspan="<?= count($grns)+2 ?>">Received</th><th colspan="7">&nbsp;</th>
        </tr>
        <?php
            foreach ($grns as $grn) {
                $order_grn = $order_grns[$grn->{$grn::DB_TABLE_PK}];
                $grn_value = $material_value = $grn->material_value()/($order_grn->factor*$order_grn->exchange_rate);
                $grn_value += $asset_value = $grn->asset_value()/($order_grn->factor*$order_grn->exchange_rate);
                $grn_value += $service_value = $grn->service_value()/($order_grn->factor*$order_grn->exchange_rate);
                $column_totals['material'] += $material_value;
                $column_totals['asset'] += $asset_value;
                $column_totals['service'] += $service_value;
                $column_totals['freight'] += $order_grn->freight;
                $column_totals['other_charges'] += $other_charges = $order_grn->insurance + $order_grn->other_charges;
                $grn_value += $order_grn->freight;
                $grn_value += $other_charges;
                $column_totals['total'] += $grn_value;
                ?>
                <tr>
                    <th><?= $grn->grn_number() ?></th>
                    <td style="text-align: right"><?= number_format($material_value,2)  ?></td>
                    <td style="text-align: right"><?= number_format($asset_value,2)  ?></td>
                    <td style="text-align: right"><?= number_format($service_value,2)  ?></td>
                    <td style="text-align: right"><?= number_format($order_grn->freight,2)  ?></td>
                    <td style="text-align: right"><?= number_format($other_charges,2)  ?></td>
                    <th style="text-align: right"><?= number_format($grn_value, 2) ?></th>
                </tr>
                <?php
            }
        ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #dcdcdc">
            <th>Total</th>
            <th style="text-align: right"><?= number_format($column_totals['material'], 2) ?></th>
            <th style="text-align: right"><?= number_format($column_totals['asset'], 2) ?></th>
            <th style="text-align: right"><?= number_format($column_totals['service'], 2) ?></th>
            <th style="text-align: right"><?= number_format($column_totals['freight'], 2) ?></th>
            <th style="text-align: right"><?= number_format($column_totals['other_charges'], 2) ?></th>
            <th style="text-align: right"><?= number_format($column_totals['total'], 2) ?></th>
        </tr>
    </tfoot>
</table>
<br/>

<?php
 if($purchace_order_invoices) {
     ?>


     <h4>2: Invoices and Payments</h4>

     <table style="font-size: 10px" width="100%" border="1" cellpadding="3" cellspacing="0">
         <thead>
         <tr style="background-color: #b3b3b3">
             <td><strong>Date</strong></td>
             <td><strong>Invoice Ref:</strong></td>
             <td><strong>Description</strong>
             </td>
             <td><strong>Invoice Amount</strong></td>
             <td><strong>Paid</strong></td>
             <td><strong>Balance</strong></td>
         </tr>
         </thead>

         <tbody>

         <?php
         $this->load->model('invoice');
         $invoices = new Invoice();
         foreach ($purchace_order_invoices as $invoice) {
               $invoices->load($invoice->id);
             ?>

             <tr>
                 <td><?= $invoice->invoice_date; ?></td>
                 <td><?= $invoice->reference; ?></td>
                 <td><?= $invoice->description; ?></td>
                 <td style="text-align: right"><?= $invoices->currency()->symbol.' '.number_format($invoices->amount,2) ?></td>
                 <td style="text-align: right"><?= $invoices->currency()->symbol.' '.number_format($invoices->paid_amount(), 2) ?></td>
                 <td style="text-align: right"><?= $invoices->currency()->symbol.' '.number_format($invoices->unpaid_amount(), 2)  ?></td>
             </tr>

             <?php
         }
         ?>

         </tbody>

     </table>

     <?php
 }
?>


<br/><br/>

<table style="font-size: 12px" width="100%">
    <tr>
        <td width="60%">&nbsp;</td>
        <th>Prepared By: </th>
        <td>
            <?= $order->employee()->full_name() ?>
        </td>
    </tr>
    <tr>
        <?php if($order->status == 'PENDING'){ ?>
        <td></td>
        <th>Approval Signature:</th>
        <td><br/>

            <strong> </strong>
            <span style="text-decoration: underline">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
        </td>
        <?php } else {
            ?>
            <td></td>
            <th>Order Status</th>
            <td><?= $order->status ?></td>
        <?php
        } ?>
    </tr>
</table>
