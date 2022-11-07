<?php
$certificate_id = $certificate->{$certificate :: DB_TABLE_PK};
if($certificate->created_by == $this->session->userdata('employee_id') || check_permission('Administrative Actions')) {
    ?>
    <span class="pull-right">
    <button data-toggle="modal" data-target="#edit_project_certificate<?= $certificate_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edt"></i> Edit
    </button>
    <div id="edit_project_certificate<?= $certificate_id ?>" class="modal fade" tabindex="-1" role="dialog">
    <?php
    $data['certificate'] = $certificate;
    $this->load->view('projects/certificates/project_certificate_form', $data); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_project_certificate" certificate_id="<?= $certificate_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
    <?php
}



