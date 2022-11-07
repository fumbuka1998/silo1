<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/13/2018
 * Time: 12:21 PM
 */

$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">PROJECT PLAN SHEET</h2>
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
                <td><strong>Plan Budget:</strong> &nbsp;&nbsp;<?= number_format(($project_plan->planned_budget($project_plan->{$project_plan::DB_TABLE_PK})),2) ?></td>
            </tr>
        </table>

        <?php
        $project_plan_tasks = $project_plan->plan_tasks(false);
        $sn = 0;
        if(!empty($project_plan_tasks)) {
            foreach ($project_plan_tasks as $project_plan_task) {
                $sn++;
                ?>
                <table style="font-size: 12px" width="100%" border="1" cellspacing="0"
                       class="table table-bordered table-hover">
                    <tbody>
                    <tr>
                        <th style="text-align: center; background-color: #dfdfdf"
                            colspan="6"><?= $sn.'.'.$project_plan_task->task()->task_name ?></th>
                    </tr>
                    <tr>
                        <td colspan="4"><strong>Planned Quantity:</strong> &nbsp;&nbsp;<?= $project_plan_task->quantity.' '.$project_plan_task->task()->measurement_unit()->symbol  ?></td>
                        <td><strong>Output Per
                                Day: &nbsp;&nbsp;</strong><?= number_format(($project_plan_task->output_per_day), 2) ?></td>
                        <td>
                            <strong>Duration:</strong> &nbsp;&nbsp;<?= number_format(($project_plan_task->quantity / $project_plan_task->output_per_day), 2) ?>
                        </td>
                    </tr>
                    <?php
                    $sub_total_per_task = 0;
                    $plan_task_materials = $project_plan_task->project_plan_task_materials();
                    if (!empty($plan_task_materials)) {
                        ?>
                        <tr>
                            <th colspan="6"> <br></th>
                        </tr>
                        <tr>
                            <th style="text-align: center" colspan="6">Material Plan</th>
                        </tr>
                        <tr style="background-color: #dfdfdf">
                            <th colspan="2">Item Name</th>
                            <th>Unit</th>
                            <th>Planned Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                        <?php
                        $sub_total_material_plan = 0;
                        foreach ($plan_task_materials as $plan_task_material) {
                            $amount = $plan_task_material->quantity * $plan_task_material->rate;
                            $sub_total_material_plan += $amount;
                            ?>
                            <tr>
                                <td colspan="2"><?= $plan_task_material->material_item()->item_name ?></td>
                                <td><?= $plan_task_material->material_item()->unit()->symbol ?></td>
                                <td style="text-align: right"><?= $plan_task_material->quantity ?></td>
                                <td style="text-align: right"><?= number_format($plan_task_material->rate,2) ?></td>
                                <td style="width: 20%; text-align: right"><?= number_format($amount, 2) ?></td>
                            </tr>
                            <?php
                        }
                        $total_budget += $sub_total_material_plan;
                        $sub_total_per_task += $sub_total_material_plan;
                        ?>
                        <tr style="font-weight: bold; background-color: #dfdfdf">
                            <th style="text-align: left" colspan="5">SUB TOTAL</th>
                            <th style="width: 20%; text-align: right"><?= number_format($sub_total_material_plan, 2) ?></th>
                        </tr>
                        <?php
                    }
                    $plan_task_equipments = $project_plan_task->project_plan_task_equipments();
                    if (!empty($plan_task_equipments)){
                        ?>
                        <tr>
                            <th colspan="6"> <br></th>
                        </tr>
                        <tr>
                            <th style="text-align: center" colspan="6">Equipments Plan</th>
                        </tr>
                        <tr style="background-color: #dfdfdf">
                            <th>Equipment Name</th>
                            <th>Rate Mode</th>
                            <th>Planned Quantity</th>
                            <th>Rate</th>
                            <th>Duration</th>
                            <th>Amount</th>
                        </tr>
                        <?php
                        $sub_total_equipments_plan = 0;
                        foreach ($plan_task_equipments as $plan_task_equipment) {
                            $amount = $plan_task_equipment->quantity * $plan_task_equipment->rate * $plan_task_equipment->duration;
                            $sub_total_equipments_plan += $amount;
                            ?>
                            <tr>
                                <td><?= $plan_task_equipment->asset()->asset_item()->asset_name ?></td>
                                <td><?= $plan_task_equipment->rate_mode ?></td>
                                <td><?= $plan_task_equipment->quantity ?></td>
                                <td style="text-align: right"><?= number_format($plan_task_equipment->rate,2) ?></td>
                                <td><?= $plan_task_equipment->duration.' '.$plan_task_equipment->rate_mode() ?></td>
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
                    $plan_task_casual_labours = $project_plan_task->project_plan_task_casual_labours($project_plan->{$project_plan::DB_TABLE_PK});
                    if (!empty($plan_task_casual_labours)) {
                        ?>
                        <tr>
                            <th colspan="6"> <br></th>
                        </tr>
                        <tr>
                            <th style="text-align: center" colspan="6">Casual Labour Plan</th>
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
                        foreach ($plan_task_casual_labours as $plan_task_casual_labour) {
                            $amount = $plan_task_casual_labour->no_of_workers * $plan_task_casual_labour->rate * $plan_task_casual_labour->duration;
                            $sub_total_labour_plan += $amount;
                            ?>
                            <tr>
                                <td><?= $plan_task_casual_labour->casual_labour_type()->name ?></td>
                                <td><?= $plan_task_casual_labour->rate_mode ?></td>
                                <td><?= $plan_task_casual_labour->no_of_workers ?></td>
                                <td style="text-align: right"><?= number_format($plan_task_casual_labour->rate,2) ?></td>
                                <td><?= $plan_task_casual_labour->duration.' '.$plan_task_casual_labour->rate_mode() ?></td>
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
            <th style="text-align: left" colspan="5">OVERALL PROJECT PLANNING BUDGET</th>
            <th style="width: 20%; text-align: right"><?= number_format($total_budget,2) ?></th>
        </tr>
        </tbody>
    </table>
<?php
}else{
    ?>
    <p>No Plan allocated for <?= $project->project_name ?></p>
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
