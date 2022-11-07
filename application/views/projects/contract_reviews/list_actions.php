<?php
/**
 * Created by PhpStorm.
 * User: josephie
 * Date: 8/9/2018
 * Time: 4:13 PM
 */
?>
<div style="width: 100%">
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default dropdown-toggle"  data-toggle="dropdown">
            Actions
        </button>
        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li>
                <a href="#"  data-toggle="modal" data-target="#edit_revision_<?= $revision->{$revision::DB_TABLE_PK} ?>"
                   class="btn btn-default btn-xs">
                    <i class="fa fa-edit"></i> Edit
                </a>
            </li>
            <li>
                <a style="color: white" href="#" class="btn btn-danger btn-xs delete_project_revision" project_revision_id="<?= $revision->{$revision::DB_TABLE_PK} ?>">
                    <i class="fa fa-trash"></i> Delete
                </a>
            </li>
        </ul>
        <a  class="btn btn-default btn-xs" target="_blank" href="<?= base_url('projects/preview_project_revision/'.$revision->{$revision::DB_TABLE_PK}) ?>">
            <i class="fa fa-file-pdf-o"></i> PDF
        </a>
    </div>
    <div id="edit_revision_<?= $revision->{$revision::DB_TABLE_PK} ?>" class="modal fade revision_form" role="dialog">
        <?php
        $this->load->view('projects/contract_reviews/revision_form');
        ?>
    </div>
</div>
