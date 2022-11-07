<?php
$handover_number=$handover->{$handover::DB_TABLE_PK};
?>
<span class="pull-right">

    <a target="_blank" href="<?= base_url('assets/preview_assets_handover/'.$handover_number)?>"
       class="btn btn-xs btn-default"> <i class="fa fa-eye"></i> Preview
    </a>
<?php if(($handover->created_by == $this->session->userdata('employee_id')) || check_permission('Administrative Actions')){ ?>
    <button data-toggle="modal" data-target="#edit_handover_asset<?=$handover_number ?>" class="btn btn-default btn-xs">
        <i class="fa fa-edt"></i> Edit
    </button>
    <div id="edit_handover_asset<?=$handover_number ?>" class="modal handover_form fade" role="dialog">
    <?php
        $data['handover'] = $handover;
        $this->load->view('inventory/assets/handovers/assets_handover_form',$data); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_handover_asset" handover_id="<?=$handover_number ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
<?php } ?>
</span>



