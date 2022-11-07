<?php
$certificate_id = $certificate->{$certificate :: DB_TABLE_PK};
?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_project_certificate<?=$certificate_id ?>" class="btn btn-info btn-xs">
        <i class="fa fa-edt"></i> Pay
    </button>
    <div id="edit_project_certificate<?=$certificate_id ?>" class="modal fade certificate_payment_form" role="dialog">
        <?php $this->load->view('finance/certificates/pay_project_certificate_form'); ?>
    </div>
</span>



