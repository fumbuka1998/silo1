<?php
//$this->load->view('includes/letterhead');
?>
<h2 style="text-align: center">
    <?php if($job_card_type == 'Inspection') {
        echo 'INSPECTION JOB CARD REPORT';
    } else if($job_card_type == 'Incident') {
        echo 'INCIDENT JOB CARD REPORT';
    } else {
        echo 'JOB CARD REPORT' ;
    }
        ?>
</h2>
<br/>
<table style="font-size: 12px" width="100%">
    <tr>
        <td  style=" vertical-align: top">
            <strong>From: </strong><?= $from ?>
        </td>
        <td  style=" vertical-align: top">
            <strong>To: </strong><?= $to ?>
        </td>
    </tr>
    <tr>
        <td colspan="3"><hr/></td>
    </tr>

</table>
<br/>
<?php
$this->load->view('hse/job_cards/reports/job_card_table');
?>
