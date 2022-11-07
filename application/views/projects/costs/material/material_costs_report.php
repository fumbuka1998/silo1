<?php
/**
 * Created by PhpStorm.
 * User: stunnaedward
 * Date: 22/03/2018
 * Time: 16:23
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">MATERIAL COST LIST</h2>
<br/>

<table style="font-size: 11px" width="100%">
    <tr>
        <td>
            <strong>Project: </strong><?= $project->project_name ?>
        </td>
        <?php
            if(isset($activity_name)){
        ?>
        <td>
            <strong>Activity: </strong><?=  $activity_name ?>
        </td>
        <?php }
            if(isset($task_name)) {
                ?>
            <td>
                <strong>Task: </strong><?= $task_name ?>
            </td>
                <?php
            }
        ?>
    </tr>
</table>
<br/>
<table border="1" width="100%" cellspacing="0" style="font-size: 11px" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>SN</th><th>Cost Date</th><th>Cost ID</th><th>Item Name</th><th>Symbol</th><th>Quantity</th><th>Rate</th><th>Amount</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $sn = 1;
        $total_amount = 0;
        foreach ($material_costs as $material_cost){
            $material_item = $material_cost->material();
            $total_amount += $amount = $material_cost->quantity*$material_cost->rate;
            ?>
            <tr>
                <td><?= $sn ?></td>
                <td><?= custom_standard_date($material_cost->cost_date) ?></td>
                <td><?= $material_cost->{$material_cost::DB_TABLE_PK} ?></td>
                <td><?= $material_item->item_name ?></td>
                <td><?= $material_item->unit()->symbol ?></td>
                <td style="text-align: right"><?= $material_cost->quantity ?></td>
                <td style="text-align: right"><?= number_format($material_cost->rate) ?></td>
                <td style="text-align: right"><?= number_format($amount) ?></td>
            </tr>
    <?php
            $sn++;
        }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6">TOTAL</th>
            <th style="text-align: right"><?= number_format($total_amount) ?></th>
        </tr>
    </tfoot>
</table>