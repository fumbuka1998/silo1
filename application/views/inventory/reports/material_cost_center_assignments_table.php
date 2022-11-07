<?php
/**
 * Created by PhpStorm.
 * User: use
 * Date: 2/16/2019
 * Time: 6:07 AM
 */

if(!empty($cost_center_assignments)){
    ?>

    <table
        <?php if($print){
            ?> width="100%" border="1" cellspacing="0"
            style="font-size: 14px"
            <?php
        } else {
            ?>
            class="table table-bordered table-hover table-striped"
            <?php
        } ?>>
        <thead>
        <tr>
            <th>S/N</th><th style="width: 300px">Item</th><th>Source Project</th><th>Destination Project</th><th>UOM</th><th>Quantity</th><th style="width: 120px">Price</th><th style="width: 150px">Amount</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $total_amount = $sn = 0;
        foreach($cost_center_assignments as $assignment) {
            $total_amount += $amount = ($assignment->price * $assignment->quantity);
            $sn++;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= $assignment->item_name ?></td>
                <td><?= $assignment->Source == null ? "UNASSIGNED" : $assignment->Source ?></td>
                <td><?= $assignment->Destination == null ? "UNASSIGNED" : $assignment->Destination ?></td>
                <td><?= $assignment->symbol ?></td>
                <td><?= $assignment->quantity ?></td>
                <td style="text-align: right"><?= 'TSH  '.number_format($assignment->price,2) ?></td>
                <td style="text-align: right"><?= 'TSH  '.number_format($amount,2) ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
        <tfoot>
        <tr style="font-weight: bold">
            <td colspan="7" style="text-align: left">TOTAL</td>
            <td style="text-align: right"><?= 'TSH  '.number_format($total_amount,2) ?></td>
        </tr>
        </tfoot>
    </table>
<?php } else {
    ?>
    <div style="text-align: center; height: 50px; border-radius: 8px;" class="info alert-info col-xs-12">
        No cost center assignment(s) currently
    </div>
    <?php
} ?>