<?php
if(!empty($job_card_reports)){
    ?>

    <table
        <?php if($print){
            ?> width="100%" border="1" cellspacing="0"
            style="font-size: 10px"
            <?php
        } else {
            ?>
            class="table table-bordered table-hover table-striped"
            <?php
        } ?>>
        <thead>
        <tr>
            <th>Number</th><th>Date</th><th>Priority</th><th>Project</th><th>Remarks</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $row = '';
        foreach($job_card_reports as $job_card) {
            $job_card = (object) $job_card;
            ?>
            <tr>
                <td><?= $job_card->job_card_no ?></td>
                <td style="text-align: left"><?= custom_standard_date($job_card->job_card_date) ?></td>
                <td style="text-align: left"><?= $job_card->priority ?></td>
                <td style="text-align: left"><?= $job_card->site ?></td>
                <td style="text-align: left"><?= $job_card->remarks ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
<?php } else {
    ?>
    <div style="text-align: center; height: 50px; border-radius: 8px; padding-top: 15px;" class="info alert-info col-xs-12">
        No job card report to display
    </div>
    <?php
} ?>
