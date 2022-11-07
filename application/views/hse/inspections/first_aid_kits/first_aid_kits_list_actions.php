<?php
$inspection_id = $inspection->{$inspection::DB_TABLE_PK};
?>

<span class="pull-left">
<?php
echo anchor(base_url('hse/inspection_details/'.$inspection->{$inspection::DB_TABLE_PK}),'Open',' class="btn btn-xs btn-default"');
echo anchor(base_url('hse/inspection_preview/'.$inspection_id),'Print',' target="_blank" class="btn btn-xs btn-default"');
if(empty($inspection->inspection_job_card()))
{
    ?>
<button data-toggle="modal" data-target="#edit_inspection_<?= $inspection_id ?>"
        class="btn btn-default btn-xs">
<i class="fa fa-edit"></i> Edit
</button>
    <button class="btn btn-danger btn-xs delete_fik_inspection" inspection_id="<?= $inspection_id ?>">
<i class="fa fa-trash"></i> Delete
</button>
<?php } ?>
</span>

<div id="edit_inspection_<?= $inspection_id ?>" class="modal fade first_aid_kit_form" role="dialog">
    <?php  $this->load->view('hse/inspections/first_aid_kits/first_aid_kit_form');?>
</div>


