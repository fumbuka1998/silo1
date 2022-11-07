<?php
$talk_register_id = $talk_register->{$talk_register::DB_TABLE_PK};
?>
<span class="pull-left">
        <?php
        echo anchor(base_url('hse/preview_toolbox_talk_register/'. $talk_register_id),'<i class="fa fa-print"></i>',  'title="Print" target="_blank" class="btn btn-xs btn-default"');
        ?>
        <button data-toggle="modal" title="Edit" data-target="#edit_talk_register_<?= $talk_register_id ?>"
                class="btn btn-default btn-xs">
         <i class="fa fa-edit"></i>
    </button>

    <button class="btn btn-danger btn-xs delete_talk_register" title="Delete" toolbox_talk_register_id = "<?= $talk_register_id ?>">
        <i class="fa fa-trash"></i>
    </button>
</span>
<div id="edit_talk_register_<?= $talk_register_id ?>" class="modal fade toolbox_talk_register_form " role="dialog">
    <?php  $this->load->view('hse/toolbox_talk_registers/toolbox_talk_register_form');?>
</div>