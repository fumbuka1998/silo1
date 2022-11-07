<?php

/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 5/15/2017
 * Time: 5:12 AM
 */

?>

<table <?php if ($print) { ?> style="font-size: 10px" width="100%" border="1" cellspacing="0" <?php } ?> class="table table-bordered table-hover">
    <thead>
        <tr>
            <th rowspan="2">Cost Center</th>
            <th rowspan="2">Contract Sum</th>
            <th colspan="6">Budget</th>
            <th colspan="6">Actual</th>
        </tr>
        <tr>
            <th>Material</th>
            <th>Equipment</th>
            <th>Labour</th>
            <th>Sub Contracts</th>
            <th>Miscellaneous</th>
            <th>Total</th>
            <th>Material</th>
            <th>Equipment</th>
            <th>Labour</th>
            <th>Sub Contracts</th>
            <th>Miscellaneous</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_contract_sum = $total_material_budget =  $total_equipment_budget = $total_labour_budget = $total_sub_contract_budget = $total_material_cost =  $total_equipment_cost = $total_labour_cost = $total_sub_contract_cost = 0;
        $total_contract_sum += $project_shared['contract_sum'];
        $total_material_budget += $project_shared['material_budget'];
        $total_equipment_budget += $project_shared['equipment_budget'];
        $total_labour_budget += $labour_budget = $project_shared['casual_labour_budget'] + $project_shared['permanent_labour_budget'];
        $total_sub_contract_budget += $project_shared['sub_contract_budget'];
        $total_material_cost += $project_shared['material_cost'];
        $total_equipment_cost += $project_shared['equipment_cost'];
        $total_labour_cost += $labour_cost = $project_shared['casual_labour_cost'] + $project_shared['permanent_labour_cost'];
        $total_sub_contract_cost += $project_shared['sub_contract_cost'];
        $total_miscellaneous_budget = $project_shared['miscellaneous_budget'];
        $total_miscellaneous_cost = $project_shared['miscellaneous_cost'];
        ?>
        <tr style="font-style: italic; font-weight: bold; background-color: #dfdfdf">
            <td style="font-weight: bold"><?= $project_shared['cost_center_name'] ?></td>
            <td style="text-align: right"><?= number_format($project_shared['contract_sum']) ?></td>
            <td style="text-align: right"><?= number_format($project_shared['material_budget']) ?></td>
            <td style="text-align: right"><?= number_format($project_shared['equipment_budget']) ?></td>
            <td style="text-align: right"><?= number_format($labour_budget) ?></td>
            <td style="text-align: right"><?= number_format($project_shared['sub_contract_budget']) ?></td>
            <td style="text-align: right"><?= number_format($project_shared['miscellaneous_budget']) ?></td>
            <th style="text-align: right; background-color: #dfdfdf"><?= number_format(($project_shared['material_budget'] + $project_shared['equipment_budget'] + $labour_budget + $project_shared['sub_contract_budget'] + $project_shared['miscellaneous_budget'])) ?></th>
            <td style="text-align: right"><?= number_format($project_shared['material_cost']) ?></td>
            <td style="text-align: right"><?= number_format($project_shared['equipment_cost']) ?></td>
            <td style="text-align: right"><?= number_format(($project_shared['casual_labour_cost'] + $project_shared['permanent_labour_cost'])) ?></td>
            <td style="text-align: right"><?= number_format($project_shared['sub_contract_cost'])  ?></td>
            <td style="text-align: right"><?= number_format($project_shared['miscellaneous_cost']) ?></td>
            <th style="text-align: right"><?= number_format(($project_shared['material_cost'] + $project_shared['equipment_cost'] + $labour_cost + $project_shared['sub_contract_cost'] + $project_shared['miscellaneous_cost'])) ?></th>
        </tr>
        <?php
        foreach ($activities as $activity) {
           $labour_cost = $activity['casual_labour_cost'] + $activity['permanent_labour_cost'];
        ?>
            <tr style="background-color: #adadad; font-weight: bold">
                <th rowspan=""><?= $activity['cost_center_name'] ?></th>
                <th style="text-align: right"><?= number_format($activity['contract_sum']) ?></th>
                <th style="text-align: right"><?= number_format($activity['material_budget']) ?></th>
                <td style="text-align: right"><?= number_format($activity['equipment_budget']) ?></td>
                <th style="text-align: right"><?= number_format(($activity['casual_labour_budget'] + $activity['permanent_labour_budget'])) ?></th>
                <th style="text-align: right"><?= number_format($activity['sub_contract_budget']) ?></th>
                <th style="text-align: right"><?= number_format($activity['miscellaneous_budget']) ?></th>
                <th style="text-align: right"><?= number_format(($activity['material_budget'] + $activity['equipment_budget'] + $activity['casual_labour_budget']  + $activity['permanent_labour_budget'] + $activity['sub_contract_budget'] + $activity['miscellaneous_budget'])) ?></th>
                <th style="text-align: right"><?= number_format($activity['material_cost']) ?></th>
                <th style="text-align: right"><?= number_format($activity['equipment_cost']) ?></th>
                <th style="text-align: right"><?= number_format($labour_cost) ?></th>
                <td style="text-align: right"><?= number_format($activity['sub_contract_cost']) ?></td>
                <th style="text-align: right"><?= number_format($activity['miscellaneous_cost']) ?></th>
                <th style="text-align: right"><?= number_format(($activity['material_cost'] + $activity['equipment_cost'] + $activity['casual_labour_cost'] + $activity['permanent_labour_cost'] + $activity['sub_contract_cost'] + $activity['miscellaneous_cost'])) ?></th>
            </tr>
            <?php
            foreach ($activity['tasks'] as $task) {
                $total_contract_sum += $task['contract_sum'];
                $total_material_budget += $task['material_budget'];
                $total_equipment_budget += $task['equipment_budget'];
                $total_labour_budget += $labour_budget = $task['casual_labour_budget'] + $task['permanent_labour_budget'];
                $total_sub_contract_budget += $task['sub_contract_budget'];
                $total_material_cost += $task['material_cost'];
                $total_equipment_cost += $task['equipment_cost'];
                $total_labour_cost += $labour_cost = $task['casual_labour_cost'] + $task['permanent_labour_cost'];
                $total_sub_contract_cost += $task['sub_contract_cost'];
                $total_miscellaneous_budget += $task['miscellaneous_budget'];
                $total_miscellaneous_cost += $task['miscellaneous_cost'];
            ?>
                <tr>
                    <td><?= $task['cost_center_name'] ?></td>
                    <td style="text-align: right"><?= number_format($task['contract_sum']) ?></td>
                    <td style="text-align: right"><?= number_format($task['material_budget']) ?></td>
                    <td style="text-align: right"><?= number_format($task['equipment_budget']) ?></td>
                    <td style="text-align: right"><?= number_format($labour_budget) ?></td>
                    <td style="text-align: right"><?= number_format($task['sub_contract_budget']) ?></td>
                    <td style="text-align: right"><?= number_format($task['miscellaneous_budget']) ?></td>
                    <th style="text-align: right; background-color: #dfdfdf"><?= number_format(($task['material_budget'] + $task['equipment_budget']  + $labour_budget + $task['sub_contract_budget'] + $task['miscellaneous_budget'])) ?></th>
                    <td style="text-align: right"><?= number_format($task['material_cost']) ?></td>
                    <td style="text-align: right"><?= number_format($task['equipment_cost']) ?></td>
                    <td style="text-align: right"><?= number_format($labour_cost) ?></td>
                    <td style="text-align: right"><?= number_format($task['sub_contract_cost']) ?></td>
                    <td style="text-align: right"><?= number_format($task['miscellaneous_cost']) ?></td>
                    <th style="text-align: right; background-color: #dfdfdf"><?= number_format(($task['material_cost'] + $task['equipment_cost']  + $labour_cost + $task['sub_contract_cost'] + $task['miscellaneous_cost'])) ?></th>
                </tr>
        <?php
            }
        }
        ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #adadad">
            <th>TOTAL</th>
            <th style="text-align: right"><?= number_format($total_contract_sum) ?></th>
            <th style="text-align: right"><?= number_format($total_material_budget) ?></th>
            <th style="text-align: right"><?= number_format($total_equipment_budget) ?></th>
            <th style="text-align: right"><?= number_format($total_labour_budget) ?></th>
            <th style="text-align: right"><?= number_format($total_sub_contract_budget) ?></th>
            <th style="text-align: right"><?= number_format($total_miscellaneous_budget) ?></th>
            <th style="text-align: right"><?= number_format(($total_material_budget + $total_equipment_budget  + $total_labour_budget  + $total_sub_contract_budget + $total_miscellaneous_budget)) ?></th>
            <th style="text-align: right"><?= number_format($total_material_cost) ?></th>
            <th style="text-align: right"><?= number_format($total_equipment_cost) ?></th>
            <th style="text-align: right"><?= number_format($total_labour_cost) ?></th>
            <td style="text-align: right"><?= number_format($total_sub_contract_cost) ?></td>
            <th style="text-align: right"><?= number_format($total_miscellaneous_cost) ?></th>
            <th style="text-align: right"><?= number_format(($total_material_cost + $total_equipment_cost + $total_labour_cost  + $total_sub_contract_cost + $total_miscellaneous_cost)) ?></th>
        </tr>
    </tfoot>
</table>