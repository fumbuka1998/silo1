<?php
$tax_table_id =$tax_table_rate->id;
//print_r([$tax_table_rate]);

?>

    <button data-toggle="modal" data-target="#edit_tax_table_<?=$tax_table_id ?>"
        class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_tax_table_<?= $tax_table_id ?>" class="modal fade edit_tax_table_" role="dialog">
       <?php $this->load->view('edit_tax_table_form');?>
    </div>



