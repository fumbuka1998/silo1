<?php
$this->load->view('includes/letterhead');
?>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td style=" width: 100%; text-align: center">
            <strong>SITE DIARY COMPLIANCE</strong><br/>
        </td>
    </tr>

</table>
<br/>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td style=" width: 30%; text-align: left">
            <strong>Project Name : </strong><?= $site_diary_compliance->site()->project_name ?>
        </td>
        <td style="width: 30%; text-align: right">
            <strong>&nbsp;</strong>
        </td>
        <td style= "width: 40%; text-align: right">
            <strong>&nbsp;</strong>
        </td>
    </tr>
    <tr>
        <td style=" width: 30%; text-align: left">
            <strong>Site Name : </strong><?= $site_diary_compliance->site()->site_location ?>
        </td>
        <td style=" width: 30%; text-align: right">
            <strong>&nbsp;</strong>
        </td>
        <td style=" width: 40%; text-align: right">
            <strong>Supervisor Name : </strong><?= $site_diary_compliance->supervisor()->full_name() ?>
        </td>
    </tr>
    <tr>
        <td style=" width: 30%; text-align: left">
            <strong>Site ID : </strong><?= $site_diary_compliance->site()->generated_project_id() ?>
        </td>
        <td style=" width: 30%; text-align: right">
            <strong>&nbsp;</strong>
        </td>
        <td style=" width: 40%; text-align: right">
            <strong> Date: </strong><?= set_date($site_diary_compliance->date) ?>
        </td>
    </tr>

</table>
<hr/>

<table style="font-size: 10px" width="100%" border="1" cellspacing="0" class="table table-bordered table-hover">
    <tr>
        <th style="width: 05%; text-align: left">S/N</th>
        <th style="width: 10%">Definition</th>
        <th style="width: 45%">Site Work Status Check</th>
        <th style="width: 05%">C</th>
        <th style="width: 05%">N/C</th>
        <th style="width: 05%">N/A</th>
        <th style="width: 25%">Comments</th>
    </tr>
    <?php
    $row = 1;
    foreach ($site_diary_compliance->site_diary_complience_statuses() as $complience_status){
        ?>
        <tr>
            <td><?= $row++?></td>
            <td>&nbsp;</td>
            <td><?= $complience_status->description ?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><?= $complience_status->comments ?></td>
        </tr>
        <?php
    }
    ?>


</table>
<br/>
<p style="text-align: center"><u>Site Supervisor's Notes</u></p>
<div>
    <?= $site_diary_compliance->remarks ?>
</div>
<hr/>
