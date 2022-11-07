
    <?php

        $disposal_id=$material_disposal->{$material_disposal::DB_TABLE_PK};
    ?>

    <span class="pull-right">

        <a target="_blank" href="<?= base_url('inventory/preview_material_disposal/'.$disposal_id)?>"
            class="btn btn-xs btn-default"> <i class="fa fa-eye"></i> Preview
        </a>
    <?php if($material_disposal->created_by == $this->session->userdata('employee_id') || check_permission('Administrative Actions')){ ?>
         <button type="button" material_disposal_id="<?= $disposal_id ?>" class="btn btn-xs btn-danger delete_material_disposal" >
            <i class="fa fa-trash"></i> Delete
        </button>
    <?php } ?>
    </span>


