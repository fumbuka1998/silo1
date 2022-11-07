<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 8/10/2018
 * Time: 12:02 PM
 */
$this->load->view('includes/letterhead');
$project = $project_revision->project();
?>
<h2 style="text-align: center"><?= $project->project_name  ?></h2>
<h4 style="text-align: center">TASK(S) REVISION SHEET</h4>
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
$revised_tasks = $project_revision->revised_tasks();
if(!empty($revised_tasks)) {
        ?>
        <table style="font-size: 12px" width="100%" border="1" cellspacing="0"
               class="table table-bordered table-hover">
            <thead>
                <tr style="background-color: #dfdfdf">
                    <th colspan="7" style="text-align: left">Revision Date :&nbsp;&nbsp;<?= custom_standard_date($project_revision->revision_date) ?></th>
                </tr>
                <tr style="background-color: #dfdfdf">
                    <th>S/N</th>
                    <th colspan="2">Task Revised </th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Revised Amount</th>
                    <th>Previous Amount</th>
                </tr>
            </thead>
            <?php
            $total_amount = 0;
            $sn= 0;
            foreach ($revised_tasks as $revised_task) {
                $sn++;
                $amount = $revised_task->amount();
                ?>
                <tbody>
                    <tr>
                        <td><?= $sn ?></td>
                        <td colspan="2" ><?= $revised_task->task()->task_name ?></td>
                        <td ><?= $revised_task->quantity  ?></td>
                        <td style="text-align: right"><?= number_format($revised_task->rate,2)  ?></td>
                        <td style="text-align: right">&nbsp;<?= number_format($amount, 2) ?></td>
                        <td style="text-align: right">&nbsp;<?= number_format($revised_task->task->contract_sum(), 2) ?></td>
                    </tr>
              <?php
              $total_amount += $amount;
                } ?>
                </tbody>
            <tfoot>
                <tr style="font-weight: bold; background-color: #dfdfdf">
                    <td style="text-align: left" colspan="5"> TOTAL</td>
                    <td style="width: 20%; text-align: right"><?= number_format($total_amount,2) ?></td>
                    <td></td>
                </tr>
            <?php
            ?>
            </tfoot>
        </table>
<?php
}
    ?>

<br/><br/>

<table width="100%">
    <tr>
        <td style="width: 50%">
            <strong>Issued By: </strong><?= $this->session->userdata('employee_name') ?>
        </td>
        <td style="width: 50%">
            <strong>Issued Date: </strong><?= custom_standard_date(date('d-m-Y')) ?>
        </td>
    </tr>
</table>