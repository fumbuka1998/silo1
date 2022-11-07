<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 8/3/2018
 * Time: 9:14 AM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">PROJECT PLANS EXECUTION SHEET</h2>
<table style="font-size: 12px" width="100%">
    <tr>
        <td>
            <b>Project : </b><?= substr($project->project_name,0,100) ?>
        </td>
        <td>
            <strong>Start Date: </strong><?= custom_standard_date($project->start_date) ?>
        </td>
        <td>
            <strong>End Date: </strong><?= custom_standard_date($project->end_date) ?>
        </td>
    </tr>
</table>

<br/><br/>

<?php
$project_plans = $project->project_plans($from,$to);
if(!empty($project_plans)) {
    $total_budget = 0;
    foreach ($project_plans as $project_plan) {
        ?>
        <table  style="font-size: 12px" width="100%">
            <tr>
                <td><strong>Plan Title:</strong> &nbsp;&nbsp;<?= $project_plan->title ?></td>
                <td><strong>Start Date:</strong> &nbsp;&nbsp;<?= $project_plan->start_date ?></td>
                <td><strong>End Date:</strong> &nbsp;&nbsp;<?= $project_plan->end_date ?></td>
                <td><strong>Execution Cost:</strong> &nbsp;&nbsp;<?= number_format(($project_plan->plan_execution_cost($project_plan->{$project_plan::DB_TABLE_PK})),2) ?></td>
            </tr>
        </table>

        <?php
        $plan_executed_tasks = $project_plan->plan_executed_tasks();
        $sn = 0;
        if(!empty($plan_executed_tasks)) {
            foreach ($plan_executed_tasks as $plan_executed_task) {
                $sn++;
                ?>
                <table style="font-size: 12px" width="100%" border="1" cellspacing="0"
                       class="table table-bordered table-hover">
                    <tbody>
                    <tr>
                        <th style="text-align: center; background-color: #dfdfdf"
                            colspan="6"><?= $sn.'.'.$plan_executed_task->task()->task_name ?></th>
                    </tr>
                    <tr style="background-color: #dfdfdf">
                        <th colspan="2">Executed Quantity </th>
                        <th>Previously Executed Quantity</th>
                        <th>Planned Quantity</th>
                        <th>Execution Date</th>
                        <th>Duration</th>
                    </tr>

                    <tr>
                        <td colspan="2" style="text-align: center"><?= $plan_executed_task->executed_quantity.' '.$plan_executed_task->task()->measurement_unit()->symbol ?></td>
                        <td style="text-align: center"><?= $plan_executed_task->previuos_task_execution($plan_executed_task->{$plan_executed_task::DB_TABLE_PK}).' '.$plan_executed_task->task()->measurement_unit()->symbol ?></td>
                        <td style="text-align: center"><?= $plan_executed_task->project_plan()->plan_task_quantity().' '.$plan_executed_task->task()->measurement_unit()->symbol  ?></td>
                        <td style="text-align: center"><?= $plan_executed_task->execution_date ?></td>
                        <td style="text-align: center">&nbsp;&nbsp; NULL</td>
                    </tr>

                    <tr>
                        <th colspan="6"> <br></th>
                    </tr>
                    <?php
                    $sub_total_per_task = 0;
                    $plan_executed_task_materials = $plan_executed_task->plan_task_execution_materials();
                        if (!empty($plan_executed_task_materials)) {
                            ?>
                            <tr>
                                <th style="text-align: center" colspan="6">Material</th>
                            </tr>
                            <tr style="background-color: #dfdfdf">
                                <th colspan="2">Item Name</th>
                                <th>Unit</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Amount</th>
                            </tr>
                            <?php
                            $sub_total_material = 0;
                            foreach ($plan_executed_task_materials as $plan_executed_task_material) {

                                $amount = $plan_executed_task_material->material_cost()->quantity * $plan_executed_task_material->material_cost()->rate;
                                $sub_total_material += $amount;
                                ?>
                                <tr>
                                    <td colspan="2"><?= $plan_executed_task_material->material_cost()->material()->item_name ?></td>
                                    <td><?= $plan_executed_task_material->material_cost()->material()->unit()->symbol ?></td>
                                    <td style="text-align: right"><?= $plan_executed_task_material->material_cost()->quantity ?></td>
                                    <td style="text-align: right"><?= number_format($plan_executed_task_material->material_cost()->rate, 2) ?></td>
                                    <td style="width: 20%; text-align: right"><?= number_format($amount, 2) ?></td>
                                </tr>
                                <?php
                            }
                            $total_budget += $sub_total_material;
                            $sub_total_per_task += $sub_total_material;
                            ?>
                            <tr style="font-weight: bold; background-color: #dfdfdf">
                                <th style="text-align: left" colspan="5">SUB TOTAL</th>
                                <th style="width: 20%; text-align: right"><?= number_format($sub_total_material, 2) ?></th>
                            </tr>
                            <tr>
                                <th colspan="6"><br></th>
                            </tr>
                            <?php
                        }


                    $plan_task_execution_equipments = $plan_executed_task->plan_task_execution_equipments();
                    if (!empty($plan_task_execution_equipments)){
                        ?>
                        <tr>
                            <th style="text-align: center" colspan="6">Equipment</th>
                        </tr>
                        <tr style="background-color: #dfdfdf">
                            <th>Equipment Name</th>
                            <th>Rate Mode</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Duration</th>
                            <th>Amount</th>
                        </tr>
                        <?php
                        $sub_total_equipments_plan = 0;
                        foreach ($plan_task_execution_equipments as $plan_task_execution_equipment) {
                            $amount = $plan_task_execution_equipment->quantity * $plan_task_execution_equipment->rate * $plan_task_execution_equipment->duration;
                            $sub_total_equipments_plan += $amount;
                            ?>
                            <tr>
                                <td><?= $plan_task_execution_equipment->asset()->asset_item()->asset_name ?></td>
                                <td><?= $plan_task_execution_equipment->rate_mode ?></td>
                                <td><?= $plan_task_execution_equipment->quantity ?></td>
                                <td style="text-align: right"><?= number_format($plan_task_execution_equipment->rate,2) ?></td>
                                <td><?= $plan_task_execution_equipment->duration.' '.$plan_task_execution_equipment->rate_mode() ?></td>
                                <td style="text-align: right"><?= number_format($amount, 2) ?></td>
                            </tr>
                            <?php
                        }
                        $total_budget += $sub_total_equipments_plan;
                        $sub_total_per_task += $sub_total_equipments_plan;
                        ?>
                        <tr style="font-weight: bold; background-color: #dfdfdf">
                            <th style="text-align: left" colspan="5">SUB TOTAL</th>
                            <th style="text-align: right"><?= number_format($sub_total_equipments_plan, 2) ?></th>
                        </tr>
                        <?php
                    }

                    $plan_task_execution_casual_labours = $plan_executed_task->plan_task_execution_casual_labour($project_plan->{$project_plan::DB_TABLE_PK});
                    if (!empty($plan_task_execution_casual_labours)) {
                        ?>
                        <tr>
                            <th style="text-align: center" colspan="6">Casual Labour</th>
                        </tr>
                        <tr style="background-color: #dfdfdf">
                            <th>Labour Type</th>
                            <th>Rate Mode</th>
                            <th>No. Of Workers</th>
                            <th>Rate</th>
                            <th>Duration</th>
                            <th>Amount</th>
                        </tr>
                        <?php
                        $sub_total_labour_plan = 0;
                        foreach ($plan_task_execution_casual_labours as $plan_task_execution_casual_labour) {
                            $amount = $plan_task_execution_casual_labour->no_of_workers * $plan_task_execution_casual_labour->rate * $plan_task_execution_casual_labour->duration;
                            $sub_total_labour_plan += $amount;
                            ?>
                            <tr>
                                <td><?= $plan_task_execution_casual_labour->casual_labour_type()->name ?></td>
                                <td><?= $plan_task_execution_casual_labour->rate_mode ?></td>
                                <td><?= $plan_task_execution_casual_labour->no_of_workers ?></td>
                                <td style="text-align: right"><?= number_format($plan_task_execution_casual_labour->rate,2) ?></td>
                                <td><?= $plan_task_execution_casual_labour->duration.' '.$plan_task_execution_casual_labour->rate_mode() ?></td>
                                <td style="width: 20%; text-align: right"><?= number_format($amount, 2) ?></td>
                            </tr>
                            <?php
                        }
                        $total_budget += $sub_total_labour_plan;
                        $sub_total_per_task += $sub_total_labour_plan;
                        ?>
                        <tr style="font-weight: bold; background-color: #dfdfdf">
                            <th style="text-align: left" colspan="5">SUB TOTAL</th>
                            <th style="width: 20%; text-align: right"><?= number_format($sub_total_labour_plan, 2) ?></th>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr style="font-weight: bold; background-color: #dfdfdf">
                        <th style="text-align: left" colspan="5">SUB TOTAL PER TASK</th>
                        <th style="width: 20%; text-align: right"><?= number_format($sub_total_per_task,2) ?></th>
                    </tr>
                    <?php
                    ?>
                    </tbody>
                </table>
                <br/><br/>
                <?php
            }
        }else{
            ?>
            <p>No allocated Task for <?= $project_plan->title ?></p>
            <?php
        }
        ?>
        <?php
    }
    ?>
    <table style="font-size: 12px" width="100%" border="1" cellspacing="0" class="table table-bordered table-hover">
        <tbody>
        <tr style="font-weight: bold; background-color: #dfdfdf">
            <th style="text-align: left" colspan="5">PROJECT PLAN EXECUTION COST</th>
            <th style="width: 20%; text-align: right"><?= number_format($total_budget,2) ?></th>
        </tr>
        </tbody>
    </table>
    <?php
}else{
    ?>
    <p>No Execution allocated for <?= $project->project_name ?></p>
    <?php
}
?>
<br/><br/>

<table width="100%">
    <tr>
        <td style="width: 50%">
            <strong>Issued By: </strong><?= $project->created_by()->full_name() ?>
        </td>
        <td style="width: 50%">
            <strong>Issued Date: </strong><?= custom_standard_date(date('d-m-Y')) ?>
        </td>
    </tr>
</table>