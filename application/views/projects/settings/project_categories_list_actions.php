<?php
/**
 * Created by PhpStorm.
 * User: STUNNA
 * Date: 4/24/2017
 * Time: 5:54 PM
 */

?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_project_category_<?= $category->{$category::DB_TABLE_PK} ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_project_category_<?= $category->{$category::DB_TABLE_PK} ?>" class="modal fade"
         role="dialog">
        <?php $this->load->view('projects/settings/project_category_form'); ?>
    </div>
    <button category_id="<?= $category->{$category::DB_TABLE_PK} ?>" class="btn btn-danger btn-xs delete_project_category">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
