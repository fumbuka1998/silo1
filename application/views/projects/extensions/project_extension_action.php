<?php if($project_extension->created_by == $this->session->userdata('employee_id')){ ?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_project_extension_<?= $project_extension->{$project_extension::DB_TABLE_PK} ?>" class="btn btn-default btn-xs" value="<= ?>">
        <i class="fa fa-edit"></i>   Edit
    </button>
    <div id="edit_project_extension_<?= $project_extension->{$project_extension::DB_TABLE_PK} ?>" class="modal fade" tabindex="-1" role="dialog">
        <?php $this->load->view('projects/extensions/project_extension_form'); ?>
    </div>
    <button project_extension_id="<?= $project_extension->{$project_extension::DB_TABLE_PK} ?>" class="btn btn-danger btn-xs btn-xs delete_project_extension ">
        Delete
    </button>
</span>
<?php } ?>