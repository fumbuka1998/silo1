<?php
$this->load->view('includes/letterhead');
?>
<h3 style="text-align: center">
    JOB CARD LABOUR ACTIVITIES
</h3>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td  style=" vertical-align: top">
             Labour :   <?= ' '. $labour->full_name(); ?>
        </td>
        <td  style=" vertical-align: top">
            Project : <?= ' '. $project->project_name ?>
        </td>
    </tr>
    <tr>
        <td colspan="3"><hr/>

        </td>
    </tr>

</table>
<br/>
<?php
 if(!empty($job_card_services)){
    ?>

    <table width="100%" border="1" cellspacing="0" style="font-size: 10px">
        <thead>
        <tr>
            <th style="width: 05%">S/N</th><th style="text-align: left">Activity</th><th style="text-align: left;">Description</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $row = 1;
        foreach($job_card_services as $labour_activity) {
            ?>
            <tr>
                <td style="width: 05%"><?= $row++ ?></td>
                <td style="text-align: left"><?= $labour_activity->activity()->activity_name ?></td>
                <td style="text-align: left"><?= '' ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
<?php } else {
    ?>
    <div style="text-align: center; height: 50px; border-radius: 8px; padding-top: 15px;" class="info alert-info col-xs-12">
        No activity record to display
    </div>
    <?php
}
 ?>

