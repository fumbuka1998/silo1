<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 5/15/2017
 * Time: 5:12 AM
 */

$cost_types = ['material','sub_contract','equipment','permanent_labour','casual_labour','miscellaneous']
?>

<table <?php if($print){ ?> style="font-size: 8px" width="100%" border="1" cellspacing="0"  <?php } ?> class="table table-bordered table-hover">
    <thead>
    <tr>
        <th rowspan="2">Cost Center</th>
        <?php
        foreach ($cost_types as $cost_type){
            ?>
            <th colspan="3"><?= ucwords(str_replace('_', ' ', $cost_type)) ?></th>
            <?php
        }
        ?>
        <th colspan="5">Total</th>
    </tr>
    <tr>
        <?php
            foreach ($cost_types as $cost_type){
                ?>
                <th>Budget</th><th>Actual Cost</th><th>% Used</th>
        <?php
            }
        ?>
        <th>Contract Sum</th><th>Budget</th><th>Actual Cost</th><th>% Used</th><th>Site Progress</th>
    </tr>
    </thead>
    <tbody>
    <tr style="font-style: italic; font-weight: bold; background-color: #dfdfdf">
        <td style="font-weight: bold"><?= $project_shared['cost_center_name'] ?></td>
        <?php
            $row_total_budget = $row_total_cost = 0;
            foreach ($cost_types as $cost_type){
                $column_total_budget = $cost_type.'_total_budget';
                $column_total_cost = $cost_type.'_total_cost';
                $row_total_budget += $$column_total_budget = $budget  = $project_shared[$cost_type.'_budget'];
                $row_total_cost += $$column_total_cost = $cost =  $project_shared[$cost_type.'_cost'];
                ?>
                <td style="text-align: right"><?= number_format($budget) ?></td>
                <td style="text-align: right"><?= number_format($cost) ?></td>
                <td style="text-align: right"><?= round(100 * $cost / $budget,3) ?>%</td>
        <?php
            }
        ?>
        <td style="text-align: right"><?= number_format(0) ?></td>
        <td style="text-align: right"><?= number_format($row_total_budget) ?></td>
        <td style="text-align: right"><?= number_format($row_total_cost) ?></td>
        <td style="text-align: right"><?= round(100 * $row_total_cost / $row_total_budget,3) ?>%</td>
        <td style="text-align: right"><?= $project_shared['completion_percentage'] ?>%</td>
    </tr>
    <?php
        foreach ($activities as $activity){
            ?>
            <tr style="background-color: #adadad; font-weight: bold">
                <th><?= $activity['cost_center_name'] ?></th>
                <?php
                $activity_row_total_budget = $activity_row_total_cost = 0;
                foreach ($cost_types as $cost_type){
                    $activity_row_total_budget += $budget = $activity[$cost_type.'_budget'];
                    $activity_row_total_cost += $cost = $activity[$cost_type.'_cost'];
                    $column_total_budget = $cost_type.'_total_budget';
                    $column_total_cost = $cost_type.'_total_cost';

                    $$column_total_budget += $budget;
                    $$column_total_cost += $cost;

                    ?>
                    <th style="text-align: right"><?= number_format($budget) ?></th>
                    <th style="text-align: right"><?= number_format($cost) ?></th>
                    <td style="text-align: right"><?= round(100 * $cost / $budget,3) ?>%</td>
                    <?php
                }
                ?>
                <th style="text-align: right"><?= number_format($activity['contract_sum']) ?></th>
                <th style="text-align: right"><?= number_format($activity_row_total_budget) ?></th>
                <th style="text-align: right"><?= number_format($activity_row_total_cost) ?></th>
                <th style="text-align: right"><?= round(100 * $activity_row_total_cost / $activity_row_total_budget,3) ?>%</th>
                <th style="text-align: right"><?= $activity['completion_percentage'] ?>%</th>
            </tr>
    <?php
            foreach ($activity['tasks'] as $task){
?>
                <tr>
                    <td><?= $task['cost_center_name'] ?></td>
                    <?php
                    $row_total_budget = $row_total_cost = 0;
                    foreach ($cost_types as $cost_type){
                        $row_total_budget += $budget = $task[$cost_type.'_budget'];
                        $row_total_cost += $cost = $task[$cost_type.'_cost'];

                        ?>
                        <td style="text-align: right"><?= number_format($budget) ?></td>
                        <td style="text-align: right"><?= number_format($cost) ?></td>
                        <td style="text-align: right"><?= round(100 * $cost / $budget,3) ?>%</td>
                        <?php
                    }
                    ?>
                    <td style="text-align: right"><?= number_format($task['contract_sum']) ?></td>
                    <td style="text-align: right"><?= number_format($row_total_budget) ?></td>
                    <td style="text-align: right"><?= number_format($row_total_cost) ?></td>
                    <td style="text-align: right"><?= round(100 * $row_total_cost / $row_total_budget,3) ?>%</td>
                    <td style="text-align: right" style="text-align: right"><?= $task['completion_percentage'] ?>%</td>
                </tr>
    <?php
            }
        }
    ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #adadad">
            <th>TOTAL</th>
            <?php
            $grand_total_budget = $grand_total_cost = 0;
            foreach ($cost_types as $cost_type) {
                $column_total_budget = $cost_type . '_total_budget';
                $column_total_cost = $cost_type . '_total_cost';
                $grand_total_budget += $$column_total_budget;
                $grand_total_cost += $$column_total_cost;
                ?>
                <th style="text-align: right"><?= number_format($$column_total_budget) ?></th>
                <th style="text-align: right"><?= number_format($$column_total_cost) ?></th>
                <td style="text-align: right"><?= round(100 * $$column_total_cost / $$column_total_budget,3) ?>%</td>
                <?php
            }
            ?>
            <th><?= number_format($contract_sum) ?></th>
            <th style="text-align: right"><?= number_format($grand_total_budget) ?></th>
            <th style="text-align: right"><?= number_format($grand_total_cost) ?></th>
            <td style="text-align: right"><?= round(100 * $grand_total_cost / $grand_total_budget,3) ?>%</td>
            <th style="text-align: right"><?= $project_shared['completion_percentage'] ?>%</th>
        </tr>
    </tfoot>
</table>
