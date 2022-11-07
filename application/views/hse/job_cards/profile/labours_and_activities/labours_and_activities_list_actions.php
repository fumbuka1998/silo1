<?php
$job_card_labour_id = $job_card_labour->{$job_card_labour::DB_TABLE_PK};
?>
<span class="pull-left">
    <a  title="Print" target="_blank" href="<?= base_url('hse/preview_labour_activity/'. $job_card_labour_id.'/'.$type) ?>" class="btn btn-xs btn-default">
       <i class="fa fa-print"></i>
    </a>
    <button data-toggle="modal" title="Edit" data-target="#edit_job_card_labour_and_activity_<?= $job_card_labour_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i>
    </button>
    <div id="edit_job_card_labour_and_activity_<?= $job_card_labour_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('hse/job_cards/profile/labours_and_activities/labour_and_activity_form');?>
    </div>
    <button class="btn btn-danger btn-xs delete_job_card_labour_and_service" title="Delete" job_card_labour_id = "<?= $job_card_labour_id ?>">
        <i class="fa fa-trash"></i>
    </button>
</span>
