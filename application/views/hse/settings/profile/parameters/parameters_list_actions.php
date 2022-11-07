<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 11/27/2019
 * Time: 11:21 AM
 */
$parameter_id = $parameter->{$parameter::DB_TABLE_PK};
?>

<span class="pull-left">
    <button data-toggle="modal" title="Edit" data-target="#edit_parameter_<?= $parameter_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i>
    </button>
        <button data-toggle="modal" title="Add Parameter type" data-target="#parameter_type_<?= $parameter_id ?>"
                class="btn btn-default btn-xs">
        <i class="fa fa-plus"></i>
    </button>

    <button class="btn btn-danger btn-xs delete_parameter" title="Delete Parameter" parameter_id = "<?= $parameter_id ?>">
        <i class="fa fa-trash"></i>
    </button>
</span>
<div id="edit_parameter_<?= $parameter_id ?>" class="modal fade" role="dialog">
    <?php $this->load->view('hse/settings/profile/parameters/parameter_form');?>
</div>
<div id="parameter_type_<?= $parameter_id ?>" class="modal fade parameter_type_form" role="dialog">
    <?php $this->load->view('hse/settings/profile/parameters/parameter_type_form');?>
</div>