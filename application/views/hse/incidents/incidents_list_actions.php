<?php
$incident_id = $incident->{$incident::DB_TABLE_PK};
?>

<span class="pull-left">
    <a target="_blank" title="Open Incident" href="<?= base_url('hse/incident_details/'.$incident->{$incident::DB_TABLE_PK}) ?>" class="btn btn-xs btn-default">
       <i class="fa fa-folder-open-o"></i>
    </a>
    <?php
    if(empty($incident->incident_job_card()))
    { ?>
    <button data-toggle="modal" title="Edit" data-target="#edit_incident_<?= $incident_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i>
    </button>
    <div id="edit_incident_<?= $incident_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('hse/incidents/incident_form');?>
    </div>

    <button class="btn btn-danger btn-xs delete_incident" title="Delete" incident_id = "<?= $incident_id ?>">
        <i class="fa fa-trash"></i>
    </button>
    <?php } ?>
</span>
