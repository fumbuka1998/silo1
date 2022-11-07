<?php
$registered_certificate_id = $registered_certificate->{$registered_certificate::DB_TABLE_PK};
?>

<span class="pull-left">

   <button data-toggle="modal" data-target="#edit_hse_registered_certicate_<?= $registered_certificate_id ?>"
           class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_hse_registered_certicate_<?= $registered_certificate_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('hse/settings/registered_certificates/registered_certificate_form');?>
    </div>
    <button class="btn btn-danger btn-xs delete_hse_registered_certificate" registered_certificate_id = "<?= $registered_certificate_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>