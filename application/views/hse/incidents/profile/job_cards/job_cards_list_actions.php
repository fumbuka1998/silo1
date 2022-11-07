<?php
$job_card_id = $job_card->{$job_card::DB_TABLE_PK};
$type = 'Incident';
?>

<span class="pull-left">
    <a  title="Open Job Card" href="<?= base_url('hse/job_card_profile/'.$type .'/'. $job_card_id) ?>" class="btn btn-xs btn-default">
       <i class="fa fa-folder-open-o"></i>
    </a>
    <button data-toggle="modal" title="Edit" data-target="#edit_incident_job_card_<?= $job_card_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i>
    </button>
    <div id="edit_incident_job_card_<?= $job_card_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('hse/incidents/profile/job_cards/job_card_form');?>
    </div>

    <button class="btn btn-danger btn-xs delete_job_card" title="Delete" job_card_id = "<?= $job_card_id ?>">
        <i class="fa fa-trash"></i>
    </button>
</span>
