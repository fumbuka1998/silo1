<?php
/**
 * Created by PhpStorm.
 * User: Kihuna
 * Date: 7/11/2018
 * Time: 1:38 PM
 */


$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">PROJECT BOQ</h2>
<table style="font-size: 12px" width="100%">
    <tr>
        <td>
            <b>Project : </b><?= substr($project->project_name,0,100) ?>
        </td>
    </tr>
</table>

<br/><br/>

<table style="font-size: 12px" width="100%" border="1" cellspacing="0" class="table table-bordered table-hover">
    <thead>
    <tr >
        <th>Work Description</th>
        <th>Measurement Unit</th>
        <th>Quantity</th>
        <th>Rate</th>
        <th>Amount</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $activities = $project->activities();
    if(!empty($activities)) {
        $total_budget = 0;
        foreach ($activities as $activity) {
            ?>
            <tr style="font-weight: bold; background-color: #dfdfdf">
                <td><?= $activity->activity_name ?></td>
                <td colspan="4"></td>
                <td><?= $activity->description ?></td>
            </tr>
            <?php
            $tasks = $activity->tasks(false);
            $tasks_sub_total = 0;
            foreach ($tasks as $task) {
                $tasks_sub_total += $task->rate * $task->quantity;
                ?>
                <tr>
                    <td><?= $task->task_name ?></td>
                    <td><?= $task->measurement_unit()->symbol ?></td>
                    <td style="text-align: right"><?= $task->quantity ?></td>
                    <td style="text-align: right"><?= number_format(round($task->rate,2)) ?></td>
                    <td style="text-align: right"><?= number_format(round(($task->quantity * $task->rate), 2)) ?></td>
                    <td><?= $task->description ?></td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td style="text-align: left" colspan="4">ACTIVITY CONTRACT SUM</td>
                <td style="text-align: right"><?= number_format($tasks_sub_total,2) ?></td>
                <td></td>
            </tr>
            <?php
        }
        ?>
        <tr style="background-color: #adadad">
            <td style="font-weight: bolder" colspan="4">GRAND TOTAL</td>
            <td style="text-align: right"><?= number_format(round($total_budget,2)) ?></td>
            <td colspan="3"></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>

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
