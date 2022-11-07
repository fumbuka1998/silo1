<?php
$inspection_id = $inspection->{$inspection::DB_TABLE_PK};
?>

<span class="pull-left">
<a target="_blank" title="Open Inspection" href="<?= base_url('hse/inspection_details/'.$inspection_id) ?>" class="btn btn-xs btn-default">
   <i class="fa fa-folder-open-o"></i>
</a>
<a target="_blank" title="Print Inspection" href="<?= base_url('hse/inspection_preview/'.$inspection_id.'/'.$insp_type) ?>" target="_blank" class="btn btn-xs btn-default">
   <i class="fa fa-print"></i>
</a>
<?php
if(empty($inspection->inspection_job_card())) {
?>
<button data-toggle="modal" title="Edit Inspection" data-target="#edit_inspection_<?= $inspection_id ?>"
            class="btn btn-default btn-xs">
<i class="fa fa-edit"></i>
</button>
    <button class="btn btn-danger btn-xs delete_inspection" title="Delete Inspection" inspection_id="<?= $inspection_id ?>">
<i class="fa fa-trash"></i>
</button>
<?php } ?>
</span>
<div id="edit_inspection_<?= $inspection_id ?>" class="modal fade inspection_form" role="dialog">
    <?php $this->load->view('hse/inspections/inspection_form');?>
</div>