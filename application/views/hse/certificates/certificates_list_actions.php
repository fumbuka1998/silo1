<?php
$certificate_id = $certificate->{$certificate::DB_TABLE_PK};
?>

<span class="pull-left">

   <button data-toggle="modal" data-target="#edit_hse_certicate_<?= $certificate_id ?>"
           class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_hse_certicate_<?= $certificate_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('hse/certificates/certificate_form');?>
    </div>
    <button class="btn btn-danger btn-xs delete_hse_certificate" hse_certificate_id = "<?= $certificate_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>