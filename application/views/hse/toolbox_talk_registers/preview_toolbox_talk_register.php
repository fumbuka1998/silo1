<?php
$this->load->view('includes/letterhead');
?>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td style=" width: 100%; text-align: center">
            <strong>TOOLBOX TALK REGISTER - SAFETY, HEALTH, ENVIRONMENT AND QUALITY</strong><br/>
        </td>
    </tr>

</table>
<br/>
<table style="font-size: 10px" width="100%" border="1" cellspacing="0" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th style="width: 1%">S/N</th><th style="text-align: left">Topic</th>
        </tr>
    </thead>
<?php
$sn = 0;
$topics = $talk_register->talk_register_topics();
$count = count($topics);
//if($count%2==0){ $half_row_count == $count/2; } else {$half_row_count == round($count/2); }
foreach ($topics as $topic) {
    $sn++
    ?>
    <tr>
        <td style="width: 05%; text-align: center"><?= $sn ?></td>
        <td style="width: 50%"><?= $topic->topic()->name ?>;</td>
    </tr>
    <?php
}
?>
</table>
<br/>
<br/>
<table width="50%" cellspacing="0.1">
    <tr>
        <td style="text-align: left; font-size: small">
            <span class="pull-left"><strong><?= nl2br('MEETING CONDUCTED BY
            (Mkutano Umeendeshwa Na)') ?>: </strong></span>
        </td>
        <td style="text-align: right; font-size: small">
            <span class="pull-right"><?= $talk_register->supervisor()->full_name()  ?></span>
        </td>
    </tr>
    <tr>
        <td style="text-align: left; font-size: small">
            <span class="pull-left"><strong><?= nl2br('DATETIME
            (Tarehe Na Muda)') ?>: </strong></span>
        </td>
        <td style="text-align: right; font-size: small">
            <span class="pull-right"><?= set_date($talk_register->date)  ?></span>
        </td>
    </tr>
    <tr>
        <td style="text-align: left; font-size: small">
            <span class="pull-left"><strong><?= nl2br('SITE NAME
            (Jina la Eneo La Kazi)') ?>: </strong></span>
        </td>
        <td style="text-align: right; font-size: small">
            <span class="pull-right"><?= $talk_register->site()->site_location  ?></span>
        </td>
    </tr>
    <tr>
        <td style="text-align: left; font-size: small">
            <span class="pull-left"><strong><?= nl2br('SITE ID
            (Namba ya utambulisho ya eneo la kazi)') ?>: </strong></span>
        </td>
        <td style="text-align: right; font-size: small">
            <span class="pull-right"><?= $talk_register->site()->generated_project_id() ?></span>
        </td>
    </tr>

</table>





<br/>
<hr/>
<p><strong><u>Acknowledgement by Team Members(Timu ya washiriki wa mkutano).</u></strong></p>
<table style="font-size: 10px" width="100%" border="1" cellspacing="0" class="table table-bordered table-hover">
    <tr>
        <th style="width: 05%; text-align: left">No</th>
        <th style="width: 50%">Name</th>
        <th style="width: 45%">Signature</th>
    </tr>
    <?php
    $row = 1;
    foreach ($talk_register->talk_register_participants() as $participant) {
        ?>
        <tr>
            <td><?= $row++?></td>
            <td><?= $participant->name ?>;</td>
            <td>&nbsp;</td>
        </tr>
        <?php
    }
    ?>
</table>

<br/><hr/>


