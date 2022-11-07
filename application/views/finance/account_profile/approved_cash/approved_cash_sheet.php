<?php

$this->load->view('includes/letterhead');

?>
<hr/>
<h2 style="text-align: center">APPROVED CASH SHEET</h2>
<br/>


<br/>
<table style="font-size: 10px" width="100%" cellspacing="0" border="1">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Item Description</th>
            <th>Unit</th>
            <th>Quantity</th>
            <th nowrap="true">Rate</th>
            <th nowrap="true">Amount</th>
           
           
        </tr>
    </thead>
    <tbody>
    <?php


        $sn = 0;
        $total_amount=0;
        $material_items = $requisition_approval->material_items(null,$account_id);

       foreach($material_items as $item){
            $sn++;
            $material = $item->material_item();
            $total_amount += $amount = $item->approved_quantity*$item->approved_rate;
        ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $material->item_name ?></td>
                <td><?= $material->unit()->symbol ?></td>
                <td style="text-align: right"><?= $item->approved_quantity ?></td>
                <td nowrap="nowrap" style="text-align: right"><?= $item->currency()->symbol.' '. number_format($item->approved_rate,2) ?></td>
                <td style="text-align: right"><?= $item->currency()->symbol.' '.  number_format($amount,2) ?></td>
            
            </tr>
    <?php
        }

        $cash_items = $requisition_approval->cash_items(null,$account_id);

        foreach ($cash_items as $item){
            $sn++;
            $total_amount += $amount = $item->approved_quantity*$item->approved_rate;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $item->requisition_cash_item()->description ?></td>
                <td><?= $item->requisition_cash_item()->measurement_unit()->symbol ?></td>
                <td style="text-align: right"><?= $item->approved_quantity ?></td>
                <td nowrap="nowrap" style="text-align: right"><?= $item->currency()->symbol.' '. number_format($item->approved_rate,2) ?></td>
                <td nowrap="nowrap" style="text-align: right"><?= $item->currency()->symbol.' '.  number_format($amount,2) ?></td>
              
            </tr>
    <?php
        }
        
    ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">TOTAL</th>
            <th style="text-align: right"><?= number_format($total_amount,2) ?></th>
            
           
        </tr>
    </tfoot>
</table><br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td colspan="3"><hr/></td>
    </tr>

   
    <tr>
        <td  style=" width:50%; vertical-align: top">
            <strong>Approving Comments</strong><br/><?= $requisition_approval->approving_comments ?>
        </td>
        <td style=" width:25%; vertical-align: top">
            <strong>Approved By: </strong><br/><?= $requisition_approval->created_by()->full_name() ?>
        </td>
        <td style=" width:25%; vertical-align: top">
            <strong>Approval Date: </strong><br/><?= custom_standard_date($requisition_approval->approved_date) ?>
        </td>
    </tr>
    
</table>
