<?php
/**
 * Created by PhpStorm.
 * User: Munyaki
 * Date: 30-Jun-17
 * Time: 3:27 PM
 */
?>
<?php
$allowance_id = $allowance_data->{$allowance_data::DB_TABLE_PK};

?>
<span class="pull-right">
    <button data-toggle="modal" data-target="#edit_allowance_<?= $allowance_id ?>"
            class="btn btn-default btn-xs">
        <i class="fa fa-edit"></i> Edit
    </button>
    <div id="edit_allowance_<?= $allowance_id ?>" class="modal fade" role="dialog">
        <?php $this->load->view('settings/allowances/allowance_form'); ?>
    </div>

    <button class="btn btn-danger btn-xs delete_allowance" allowance_id="<?= $allowance_id ?>">
        <i class="fa fa-trash"></i> Delete
    </button>
</span>
