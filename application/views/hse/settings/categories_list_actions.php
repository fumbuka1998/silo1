<?php
/**
 * Created by PhpStorm.
 * User: Macode
 * Date: 11/27/2019
 * Time: 10:26 AM
 */
$category_id = $category->{$category::DB_TABLE_PK};
?>

<span class="pull-left">
    <button data-toggle="modal" data-target="#edit_category_<?= $category_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_category_<?= $category_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('hse/settings/category_form');?>
    </div>

    <button class="btn btn-danger btn-xs delete_category" category_id = "<?= $category_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>